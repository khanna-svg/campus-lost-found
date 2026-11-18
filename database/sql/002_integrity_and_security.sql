-- =====================================================================
-- Campus Lost & Found  |  002_integrity_and_security.sql
-- Creates: updated_at triggers, business-rule triggers, RLS lockdown.
-- Run SECOND (after 001 succeeded).
-- =====================================================================

-- ---------------------------------------------------------------------
-- A. updated_at triggers
-- ---------------------------------------------------------------------
create trigger users_set_updated_at           before update on public.users           for each row execute function public.set_updated_at();
create trigger item_categories_set_updated_at before update on public.item_categories for each row execute function public.set_updated_at();
create trigger lost_items_set_updated_at      before update on public.lost_items      for each row execute function public.set_updated_at();
create trigger found_items_set_updated_at     before update on public.found_items     for each row execute function public.set_updated_at();
create trigger claim_requests_set_updated_at  before update on public.claim_requests  for each row execute function public.set_updated_at();
create trigger item_returns_set_updated_at    before update on public.item_returns    for each row execute function public.set_updated_at();
create trigger notifications_set_updated_at   before update on public.notifications   for each row execute function public.set_updated_at();

-- ---------------------------------------------------------------------
-- B. claim_requests: guard INSERT
--    - you cannot claim your own found report
--    - only APPROVED found items can be claimed
-- ---------------------------------------------------------------------
create or replace function public.claim_requests_guard_insert()
returns trigger
language plpgsql
set search_path = ''
as $$
declare
    v_finder uuid;
    v_status text;
begin
    select user_id, status into v_finder, v_status
    from public.found_items
    where id = new.found_item_id;

    if v_finder = new.claimant_id then
        raise exception 'You cannot claim an item that you reported.' using errcode = '23514';
    end if;

    if v_status is distinct from 'approved' then
        raise exception 'Only approved found items can be claimed (current status: %).', coalesce(v_status, 'missing')
            using errcode = '23514';
    end if;

    return new;
end;
$$;

create trigger claim_requests_before_insert
    before insert on public.claim_requests
    for each row execute function public.claim_requests_guard_insert();

-- ---------------------------------------------------------------------
-- C. claim_requests: guard UPDATE
--    - item and claimant can never be changed
--    - 'completed' requires a recorded return
-- ---------------------------------------------------------------------
create or replace function public.claim_requests_guard_update()
returns trigger
language plpgsql
set search_path = ''
as $$
begin
    if new.found_item_id <> old.found_item_id or new.claimant_id <> old.claimant_id then
        raise exception 'The item and claimant of a claim request cannot be changed.' using errcode = '23514';
    end if;

    if new.status = 'completed' and old.status is distinct from 'completed' then
        if not exists (select 1 from public.item_returns where claim_request_id = new.id) then
            raise exception 'A claim cannot be completed before the return is recorded.' using errcode = '23514';
        end if;
    end if;

    return new;
end;
$$;

create trigger claim_requests_before_update
    before update on public.claim_requests
    for each row execute function public.claim_requests_guard_update();

-- ---------------------------------------------------------------------
-- D. found_items: guard status changes
--    - 'claimed'  requires an approved claim
--    - 'returned' requires a recorded return
-- ---------------------------------------------------------------------
create or replace function public.found_items_guard_status()
returns trigger
language plpgsql
set search_path = ''
as $$
begin
    if new.status is distinct from old.status then
        if new.status = 'claimed' and not exists (
            select 1 from public.claim_requests
            where found_item_id = new.id and status in ('approved', 'completed')
        ) then
            raise exception 'An item cannot be marked Claimed without an approved claim.' using errcode = '23514';
        end if;

        if new.status = 'returned' and not exists (
            select 1 from public.item_returns where found_item_id = new.id
        ) then
            raise exception 'An item cannot be marked Returned before the return is recorded.' using errcode = '23514';
        end if;
    end if;

    return new;
end;
$$;

create trigger found_items_before_update
    before update on public.found_items
    for each row execute function public.found_items_guard_status();

-- ---------------------------------------------------------------------
-- E. item_returns: guard INSERT (claim must be approved, item must be claimed)
-- ---------------------------------------------------------------------
create or replace function public.item_returns_guard_insert()
returns trigger
language plpgsql
set search_path = ''
as $$
declare
    v_claim_status text;
    v_item_status  text;
begin
    select status into v_claim_status from public.claim_requests where id = new.claim_request_id;
    select status into v_item_status  from public.found_items    where id = new.found_item_id;

    if v_claim_status is distinct from 'approved' then
        raise exception 'Only an approved claim can be completed with a return (claim status: %).', coalesce(v_claim_status, 'missing')
            using errcode = '23514';
    end if;

    if v_item_status is distinct from 'claimed' then
        raise exception 'The item must be in Claimed status before it can be returned (item status: %).', coalesce(v_item_status, 'missing')
            using errcode = '23514';
    end if;

    return new;
end;
$$;

create trigger item_returns_before_insert
    before insert on public.item_returns
    for each row execute function public.item_returns_guard_insert();

-- ---------------------------------------------------------------------
-- F. activity_logs is append-only
-- ---------------------------------------------------------------------
create or replace function public.activity_logs_block_changes()
returns trigger
language plpgsql
set search_path = ''
as $$
begin
    raise exception 'activity_logs is append-only; rows cannot be updated or deleted.' using errcode = '42501';
end;
$$;

create trigger activity_logs_immutable
    before update or delete on public.activity_logs
    for each row execute function public.activity_logs_block_changes();

-- ---------------------------------------------------------------------
-- G. Lock the database away from Supabase's public Data API.
--    Only the Laravel server (direct Postgres connection) touches these tables.
-- ---------------------------------------------------------------------
alter table public.users                 enable row level security;
alter table public.password_reset_tokens enable row level security;
alter table public.sessions              enable row level security;
alter table public.cache                 enable row level security;
alter table public.cache_locks           enable row level security;
alter table public.item_categories       enable row level security;
alter table public.lost_items            enable row level security;
alter table public.found_items           enable row level security;
alter table public.claim_requests        enable row level security;
alter table public.item_returns          enable row level security;
alter table public.notifications         enable row level security;
alter table public.activity_logs         enable row level security;

-- No RLS policies are created on purpose => anon/authenticated roles see nothing.
-- Belt and braces: also remove their table privileges.
revoke all on all tables    in schema public from anon, authenticated;
revoke all on all sequences in schema public from anon, authenticated;
revoke all on all functions in schema public from public, anon, authenticated;

alter default privileges in schema public revoke all on tables    from anon, authenticated;
alter default privileges in schema public revoke all on sequences from anon, authenticated;
alter default privileges in schema public revoke all on functions from public, anon, authenticated;
