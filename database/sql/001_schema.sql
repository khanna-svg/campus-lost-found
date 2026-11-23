-- =====================================================================
-- Campus Lost & Found  |  001_schema.sql
-- Creates: extensions, helper function, tables, constraints, indexes.
-- Run FIRST in Supabase Dashboard -> SQL Editor.
-- =====================================================================

-- Fuzzy / partial-word search (ILIKE '%lap%') support.
create extension if not exists pg_trgm with schema extensions;

-- Keeps updated_at correct even if someone edits rows directly in SQL.
create or replace function public.set_updated_at()
returns trigger
language plpgsql
set search_path = ''
as $$
begin
    new.updated_at = now();
    return new;
end;
$$;

-- =====================================================================
-- 1. users
-- =====================================================================
create table public.users (
    id                   uuid primary key default gen_random_uuid(),
    school_id            varchar(50)  unique,                -- student / employee number
    name                 varchar(150) not null,
    email                varchar(255) not null unique,
    email_verified_at    timestamptz,
    password             varchar(255) not null,              -- bcrypt/argon hash from Laravel, never plain text
    role                 varchar(20)  not null default 'user',
    department           varchar(150),
    phone                varchar(30),
    is_active            boolean      not null default true,
    must_change_password boolean      not null default false, -- true for admin-created accounts
    last_login_at        timestamptz,
    remember_token       varchar(100),
    created_at           timestamptz  not null default now(),
    updated_at           timestamptz  not null default now(),

    constraint users_role_check       check (role in ('user', 'admin')),
    constraint users_email_lowercase  check (email = lower(email)),
    constraint users_name_not_blank   check (btrim(name) <> '')
);
create index users_role_idx on public.users (role);

-- Laravel infrastructure tables (needed because Vercel has no persistent disk)
create table public.password_reset_tokens (
    email      varchar(255) primary key,
    token      varchar(255) not null,
    created_at timestamptz
);

create table public.sessions (
    id            varchar(255) primary key,
    user_id       uuid references public.users (id) on delete cascade,
    ip_address    varchar(45),
    user_agent    text,
    payload       text    not null,
    last_activity integer not null
);
create index sessions_user_id_idx       on public.sessions (user_id);
create index sessions_last_activity_idx on public.sessions (last_activity);

create table public.cache (
    "key"      varchar(255) primary key,
    value      text    not null,
    expiration integer not null
);
create index cache_expiration_idx on public.cache (expiration);

create table public.cache_locks (
    "key"      varchar(255) primary key,
    owner      varchar(255) not null,
    expiration integer      not null
);
create index cache_locks_expiration_idx on public.cache_locks (expiration);

-- =====================================================================
-- 2. item_categories
-- =====================================================================
create table public.item_categories (
    id          uuid primary key default gen_random_uuid(),
    name        varchar(100) not null unique,
    slug        varchar(120) not null unique,
    description text,
    is_active   boolean      not null default true,
    created_at  timestamptz  not null default now(),
    updated_at  timestamptz  not null default now(),

    constraint item_categories_name_not_blank check (btrim(name) <> '')
);

-- =====================================================================
-- 3. lost_items
-- =====================================================================
create table public.lost_items (
    id                  uuid primary key default gen_random_uuid(),
    user_id             uuid not null references public.users (id)            on delete restrict,
    category_id         uuid not null references public.item_categories (id)  on delete restrict,
    item_name           varchar(150) not null,
    description         text         not null,
    date_lost           date         not null,
    time_lost           time,                                -- approximate, optional
    location_last_seen  varchar(200) not null,
    photo_path          varchar(500),                        -- path inside Supabase bucket "item-photos"
    identifying_details text,                                -- PRIVATE: owner + admin only
    status              varchar(20)  not null default 'pending_review',
    needs_correction    boolean      not null default false,
    correction_note     text,
    rejection_reason    text,
    reviewed_by         uuid references public.users (id) on delete restrict,
    reviewed_at         timestamptz,
    resolved_at         timestamptz,
    archived_at         timestamptz,
    created_at          timestamptz  not null default now(),
    updated_at          timestamptz  not null default now(),

    -- Full-text search over PUBLIC fields only
    search_vector tsvector generated always as (
        setweight(to_tsvector('english', coalesce(item_name, '')),          'A') ||
        setweight(to_tsvector('english', coalesce(description, '')),        'B') ||
        setweight(to_tsvector('english', coalesce(location_last_seen, '')), 'C')
    ) stored,

    constraint lost_items_status_check
        check (status in ('pending_review', 'approved', 'rejected', 'resolved', 'archived')),
    constraint lost_items_name_not_blank check (btrim(item_name) <> ''),
    constraint lost_items_reviewed_check
        check (status = 'pending_review' or (reviewed_by is not null and reviewed_at is not null)),
    constraint lost_items_correction_check
        check (not needs_correction or (status = 'pending_review' and correction_note is not null)),
    constraint lost_items_rejected_reason_check
        check (status <> 'rejected' or rejection_reason is not null),
    constraint lost_items_resolved_check
        check (status <> 'resolved' or resolved_at is not null),
    constraint lost_items_archived_check
        check (status <> 'archived' or archived_at is not null)
);
create index lost_items_user_idx          on public.lost_items (user_id);
create index lost_items_category_idx      on public.lost_items (category_id);
create index lost_items_status_date_idx   on public.lost_items (status, created_at desc);
create index lost_items_date_lost_idx     on public.lost_items (date_lost desc);
create index lost_items_search_idx        on public.lost_items using gin (search_vector);
create index lost_items_name_trgm_idx     on public.lost_items using gin (item_name extensions.gin_trgm_ops);
create index lost_items_location_trgm_idx on public.lost_items using gin (location_last_seen extensions.gin_trgm_ops);

-- =====================================================================
-- 4. found_items
-- =====================================================================
create table public.found_items (
    id                 uuid primary key default gen_random_uuid(),
    user_id            uuid not null references public.users (id)           on delete restrict,  -- the finder
    category_id        uuid not null references public.item_categories (id) on delete restrict,
    item_name          varchar(150) not null,
    public_description text         not null,               -- PUBLIC, general wording only
    date_found         date         not null,
    location_found     varchar(200) not null,
    photo_path         varchar(500) not null,               -- path inside bucket "item-photos"
    private_details    text         not null,               -- PRIVATE: used by admin to verify the owner
    remarks            text,                                -- PRIVATE: finder's additional remarks
    storage_location   varchar(200),                        -- where the item is physically kept
    is_surrendered_to_office boolean not null default false, -- true: in Admin Office, false: kept by finder/student
    status             varchar(20)  not null default 'pending_review',
    needs_correction   boolean      not null default false,
    correction_note    text,
    rejection_reason   text,
    reviewed_by        uuid references public.users (id) on delete restrict,
    reviewed_at        timestamptz,
    archived_at        timestamptz,
    created_at         timestamptz  not null default now(),
    updated_at         timestamptz  not null default now(),

    -- Full-text search over PUBLIC fields only (private_details/remarks are deliberately excluded)
    search_vector tsvector generated always as (
        setweight(to_tsvector('english', coalesce(item_name, '')),          'A') ||
        setweight(to_tsvector('english', coalesce(public_description, '')), 'B') ||
        setweight(to_tsvector('english', coalesce(location_found, '')),     'C')
    ) stored,

    constraint found_items_status_check
        check (status in ('pending_review', 'approved', 'claimed', 'returned', 'archived')),
    constraint found_items_name_not_blank    check (btrim(item_name) <> ''),
    constraint found_items_private_not_blank check (btrim(private_details) <> ''),
    constraint found_items_reviewed_check
        check (status = 'pending_review' or (reviewed_by is not null and reviewed_at is not null)),
    constraint found_items_correction_check
        check (not needs_correction or (status = 'pending_review' and correction_note is not null)),
    constraint found_items_archived_check
        check (status <> 'archived' or archived_at is not null)
);
create index found_items_user_idx          on public.found_items (user_id);
create index found_items_category_idx      on public.found_items (category_id);
create index found_items_status_date_idx   on public.found_items (status, created_at desc);
create index found_items_date_found_idx    on public.found_items (date_found desc);
create index found_items_search_idx        on public.found_items using gin (search_vector);
create index found_items_name_trgm_idx     on public.found_items using gin (item_name extensions.gin_trgm_ops);
create index found_items_location_trgm_idx on public.found_items using gin (location_found extensions.gin_trgm_ops);

-- =====================================================================
-- 5. claim_requests
-- =====================================================================
create table public.claim_requests (
    id                  uuid primary key default gen_random_uuid(),
    found_item_id       uuid not null references public.found_items (id) on delete restrict,
    claimant_id         uuid not null references public.users (id)       on delete restrict,
    lost_item_id        uuid references public.lost_items (id)           on delete set null, -- optional link to claimant's own lost report
    status              varchar(30) not null default 'pending',
    proof_description   text  not null,                                  -- PRIVATE
    evidence_paths      jsonb not null default '[]'::jsonb,              -- PRIVATE: paths in bucket "claim-evidence" (max 3)
    claimant_response   text,                                            -- answer to an admin info request
    admin_message       text,                                            -- visible to claimant (info request, instructions)
    internal_notes      text,                                            -- admin only
    rejection_reason    text,
    pickup_instructions text,
    reviewed_by         uuid references public.users (id) on delete restrict,
    reviewed_at         timestamptz,
    approved_by         uuid references public.users (id) on delete restrict,
    approved_at         timestamptz,
    completed_at        timestamptz,
    created_at          timestamptz not null default now(),
    updated_at          timestamptz not null default now(),

    constraint claim_requests_status_check
        check (status in ('pending', 'under_review', 'additional_information_required',
                          'approved', 'rejected', 'completed')),
    constraint claim_requests_proof_not_blank check (btrim(proof_description) <> ''),
    constraint claim_requests_evidence_check
        check (jsonb_typeof(evidence_paths) = 'array' and jsonb_array_length(evidence_paths) <= 3),
    constraint claim_requests_approver_check
        check (status not in ('approved', 'completed') or (approved_by is not null and approved_at is not null)),
    constraint claim_requests_rejected_check
        check (status <> 'rejected' or rejection_reason is not null),
    constraint claim_requests_info_required_check
        check (status <> 'additional_information_required' or admin_message is not null),
    constraint claim_requests_completed_check
        check (status <> 'completed' or completed_at is not null),
    -- lets item_returns prove that a claim and an item belong together
    constraint claim_requests_id_item_unique unique (id, found_item_id)
);
create index claim_requests_item_idx      on public.claim_requests (found_item_id);
create index claim_requests_claimant_idx  on public.claim_requests (claimant_id, created_at desc);
create index claim_requests_status_idx    on public.claim_requests (status, created_at desc);

-- At most ONE approved/completed claim per found item (prevents conflicting approvals).
create unique index claim_requests_one_approved_per_item
    on public.claim_requests (found_item_id)
    where status in ('approved', 'completed');

-- A user may only have ONE open claim per item.
create unique index claim_requests_one_open_per_user_item
    on public.claim_requests (found_item_id, claimant_id)
    where status in ('pending', 'under_review', 'additional_information_required');

-- =====================================================================
-- 6. item_returns   (proof of the physical hand-over)
-- =====================================================================
create table public.item_returns (
    id                uuid primary key default gen_random_uuid(),
    claim_request_id  uuid not null unique,
    found_item_id     uuid not null unique references public.found_items (id) on delete restrict, -- an item is returned once
    recorded_by       uuid not null references public.users (id) on delete restrict,              -- admin who confirmed
    returned_to_name  varchar(150) not null,
    lrn               varchar(100),
    course_year       varchar(100),
    id_presented      varchar(100),                      -- e.g. "School ID"
    notes             text,
    returned_at       timestamptz not null default now(),
    created_at        timestamptz not null default now(),
    updated_at        timestamptz not null default now(),

    -- the claim MUST be a claim for this exact item
    constraint item_returns_claim_matches_item
        foreign key (claim_request_id, found_item_id)
        references public.claim_requests (id, found_item_id) on delete restrict
);
create index item_returns_recorded_by_idx on public.item_returns (recorded_by);
create index item_returns_returned_at_idx on public.item_returns (returned_at desc);

-- =====================================================================
-- 7. notifications
-- =====================================================================
create table public.notifications (
    id           uuid primary key default gen_random_uuid(),
    user_id      uuid not null references public.users (id) on delete cascade,
    type         varchar(40)  not null,
    title        varchar(150) not null,
    message      text         not null,
    related_type varchar(20),
    related_id   uuid,
    read_at      timestamptz,
    created_at   timestamptz  not null default now(),
    updated_at   timestamptz  not null default now(),

    constraint notifications_type_check check (type in (
        'report_submitted', 'report_approved', 'report_rejected', 'report_correction_requested',
        'claim_received', 'claim_info_required', 'claim_response', 'claim_approved', 'claim_rejected',
        'claim_ready_for_retrieval', 'item_returned', 'announcement')),
    constraint notifications_related_type_check
        check (related_type is null or related_type in ('lost_item', 'found_item', 'claim_request')),
    constraint notifications_related_pair_check
        check ((related_type is null) = (related_id is null))
);
create index notifications_user_unread_idx on public.notifications (user_id, read_at, created_at desc);
create index notifications_user_created_idx on public.notifications (user_id, created_at desc);

-- =====================================================================
-- 8. activity_logs   (append-only audit trail; no updated_at on purpose)
-- =====================================================================
create table public.activity_logs (
    id           uuid primary key default gen_random_uuid(),
    actor_id     uuid references public.users (id) on delete restrict,  -- null = system
    action       varchar(100) not null,                                  -- e.g. 'claim.approved'
    subject_type varchar(50),
    subject_id   uuid,
    description  text         not null,
    properties   jsonb,                                                  -- before/after values
    ip_address   inet,
    user_agent   varchar(255),
    created_at   timestamptz  not null default now(),

    constraint activity_logs_subject_pair_check
        check ((subject_type is null) = (subject_id is null))
);
create index activity_logs_created_idx on public.activity_logs (created_at desc);
create index activity_logs_actor_idx   on public.activity_logs (actor_id, created_at desc);
create index activity_logs_subject_idx on public.activity_logs (subject_type, subject_id);
create index activity_logs_action_idx  on public.activity_logs (action);
