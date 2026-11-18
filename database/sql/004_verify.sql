-- =====================================================================
-- Campus Lost & Found  |  004_verify.sql
-- Self-test of the schema. Safe to run: it creates temporary test rows
-- (@selftest.invalid users) and deletes them again. Run LAST.
-- Expected: every row in the final result says PASS.
-- =====================================================================

create temp table selftest_results (
    step     serial,
    test     text,
    outcome  text
);

create or replace function pg_temp.selftest_cleanup() returns void language plpgsql as $$
begin
    delete from public.item_returns   where found_item_id in (select id from public.found_items where user_id in (select id from public.users where email like '%@selftest.invalid'));
    delete from public.claim_requests where found_item_id in (select id from public.found_items where user_id in (select id from public.users where email like '%@selftest.invalid'));
    delete from public.found_items    where user_id in (select id from public.users where email like '%@selftest.invalid');
    delete from public.users          where email like '%@selftest.invalid';
end $$;

select pg_temp.selftest_cleanup();

do $$
declare
    v_admin uuid; v_finder uuid; v_owner uuid; v_owner2 uuid;
    v_cat uuid; v_item uuid; v_claim uuid; v_claim2 uuid;
begin
    select id into v_cat from public.item_categories order by name limit 1;

    insert into public.users (name, email, password, role) values ('T Admin',  'admin@selftest.invalid',  'x', 'admin') returning id into v_admin;
    insert into public.users (name, email, password)       values ('T Finder', 'finder@selftest.invalid', 'x')          returning id into v_finder;
    insert into public.users (name, email, password)       values ('T Owner',  'owner@selftest.invalid',  'x')          returning id into v_owner;
    insert into public.users (name, email, password)       values ('T Owner2', 'owner2@selftest.invalid', 'x')          returning id into v_owner2;

    -- 1 invalid role rejected
    begin
        insert into public.users (name, email, password, role) values ('Bad', 'bad@selftest.invalid', 'x', 'superuser');
        insert into selftest_results (test, outcome) values ('Invalid role is rejected', 'FAIL - no error');
    exception when others then
        insert into selftest_results (test, outcome) values ('Invalid role is rejected', 'PASS');
    end;

    -- 2 uppercase email rejected
    begin
        insert into public.users (name, email, password) values ('Bad', 'UPPER@selftest.invalid', 'x');
        insert into selftest_results (test, outcome) values ('Uppercase email is rejected', 'FAIL - no error');
    exception when others then
        insert into selftest_results (test, outcome) values ('Uppercase email is rejected', 'PASS');
    end;

    -- 3 invalid found status rejected
    begin
        insert into public.found_items (user_id, category_id, item_name, public_description, date_found, location_found, photo_path, private_details, status)
        values (v_finder, v_cat, 'X', 'X', current_date, 'X', 'x.jpg', 'x', 'bogus');
        insert into selftest_results (test, outcome) values ('Invalid found-item status is rejected', 'FAIL - no error');
    exception when others then
        insert into selftest_results (test, outcome) values ('Invalid found-item status is rejected', 'PASS');
    end;

    -- 4 valid found item inserted
    insert into public.found_items (user_id, category_id, item_name, public_description, date_found, location_found, photo_path, private_details)
    values (v_finder, v_cat, 'Blue backpack', 'A blue backpack', current_date, 'Library', 'test.jpg', 'Has a red keychain inside')
    returning id into v_item;
    insert into selftest_results (test, outcome) values ('Valid found item can be created', 'PASS');

    -- 5 claim on a PENDING item rejected
    begin
        insert into public.claim_requests (found_item_id, claimant_id, proof_description) values (v_item, v_owner, 'mine');
        insert into selftest_results (test, outcome) values ('Cannot claim an item that is not approved', 'FAIL - no error');
    exception when others then
        insert into selftest_results (test, outcome) values ('Cannot claim an item that is not approved', 'PASS');
    end;

    -- approve the item (needs reviewer)
    update public.found_items set status = 'approved', reviewed_by = v_admin, reviewed_at = now() where id = v_item;

    -- 6 finder cannot claim own item
    begin
        insert into public.claim_requests (found_item_id, claimant_id, proof_description) values (v_item, v_finder, 'mine');
        insert into selftest_results (test, outcome) values ('Finder cannot claim own item', 'FAIL - no error');
    exception when others then
        insert into selftest_results (test, outcome) values ('Finder cannot claim own item', 'PASS');
    end;

    -- 7 valid claims by two different users
    insert into public.claim_requests (found_item_id, claimant_id, proof_description) values (v_item, v_owner,  'Red keychain') returning id into v_claim;
    insert into public.claim_requests (found_item_id, claimant_id, proof_description) values (v_item, v_owner2, 'Blue bag')     returning id into v_claim2;
    insert into selftest_results (test, outcome) values ('Two different users can have open claims', 'PASS');

    -- 8 duplicate open claim by same user rejected
    begin
        insert into public.claim_requests (found_item_id, claimant_id, proof_description) values (v_item, v_owner, 'again');
        insert into selftest_results (test, outcome) values ('Duplicate open claim by same user is rejected', 'FAIL - no error');
    exception when others then
        insert into selftest_results (test, outcome) values ('Duplicate open claim by same user is rejected', 'PASS');
    end;

    -- 9 approval without approver rejected
    begin
        update public.claim_requests set status = 'approved' where id = v_claim;
        insert into selftest_results (test, outcome) values ('Approval requires approver + timestamp', 'FAIL - no error');
    exception when others then
        insert into selftest_results (test, outcome) values ('Approval requires approver + timestamp', 'PASS');
    end;

    -- 10 4 evidence files rejected
    begin
        update public.claim_requests set evidence_paths = '["a","b","c","d"]'::jsonb where id = v_claim;
        insert into selftest_results (test, outcome) values ('More than 3 evidence files is rejected', 'FAIL - no error');
    exception when others then
        insert into selftest_results (test, outcome) values ('More than 3 evidence files is rejected', 'PASS');
    end;

    -- 11 item cannot be Claimed without an approved claim
    begin
        update public.found_items set status = 'claimed' where id = v_item;
        insert into selftest_results (test, outcome) values ('Item cannot be Claimed without approved claim', 'FAIL - no error');
    exception when others then
        insert into selftest_results (test, outcome) values ('Item cannot be Claimed without approved claim', 'PASS');
    end;

    -- approve first claim
    update public.claim_requests set status = 'approved', approved_by = v_admin, approved_at = now() where id = v_claim;
    insert into selftest_results (test, outcome) values ('Admin can approve a claim', 'PASS');

    -- 12 second approval for same item rejected
    begin
        update public.claim_requests set status = 'approved', approved_by = v_admin, approved_at = now() where id = v_claim2;
        insert into selftest_results (test, outcome) values ('Two approved claims for one item is rejected', 'FAIL - no error');
    exception when others then
        insert into selftest_results (test, outcome) values ('Two approved claims for one item is rejected', 'PASS');
    end;

    -- 13 cannot return before the item is Claimed / before a return is recorded
    begin
        update public.found_items set status = 'returned' where id = v_item;
        insert into selftest_results (test, outcome) values ('Item cannot be Returned without recorded return', 'FAIL - no error');
    exception when others then
        insert into selftest_results (test, outcome) values ('Item cannot be Returned without recorded return', 'PASS');
    end;

    -- 14 return cannot be recorded while item is still Approved (not Claimed)
    begin
        insert into public.item_returns (claim_request_id, found_item_id, recorded_by, returned_to_name) values (v_claim, v_item, v_admin, 'T Owner');
        insert into selftest_results (test, outcome) values ('Return requires item in Claimed status', 'FAIL - no error');
    exception when others then
        insert into selftest_results (test, outcome) values ('Return requires item in Claimed status', 'PASS');
    end;

    -- happy path
    update public.found_items set status = 'claimed' where id = v_item;
    insert into public.item_returns (claim_request_id, found_item_id, recorded_by, returned_to_name, id_presented)
    values (v_claim, v_item, v_admin, 'T Owner', 'School ID');

    -- 15 finish the flow: item -> returned, claim -> completed
    update public.found_items   set status = 'returned' where id = v_item;
    update public.claim_requests set status = 'completed', completed_at = now() where id = v_claim;
    insert into selftest_results (test, outcome) values ('Full flow: approve -> claimed -> return -> returned -> completed', 'PASS');

    -- 16 changing claimant is rejected
    begin
        update public.claim_requests set claimant_id = v_owner2 where id = v_claim;
        insert into selftest_results (test, outcome) values ('Claimant cannot be changed', 'FAIL - no error');
    exception when others then
        insert into selftest_results (test, outcome) values ('Claimant cannot be changed', 'PASS');
    end;

    -- 17 activity log immutable (insert + update in one block; rolled back on error)
    begin
        insert into public.activity_logs (action, description) values ('selftest', 'selftest');
        update public.activity_logs set description = 'tampered' where action = 'selftest';
        insert into selftest_results (test, outcome) values ('activity_logs rows cannot be edited', 'FAIL - no error');
    exception when others then
        insert into selftest_results (test, outcome) values ('activity_logs rows cannot be edited', 'PASS');
    end;

exception when others then
    insert into selftest_results (test, outcome) values ('UNEXPECTED ERROR in test script', 'FAIL - ' || sqlerrm);
end $$;

select pg_temp.selftest_cleanup();

-- ---------- RESULTS ----------
select step, test, outcome from selftest_results order by step;
