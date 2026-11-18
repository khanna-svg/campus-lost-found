-- =====================================================================
-- Campus Lost & Found  |  003_seed_and_storage.sql
-- Creates: starter categories + the two Supabase Storage buckets.
-- Run THIRD (after 002 succeeded).
-- =====================================================================

insert into public.item_categories (name, slug, description) values
    ('Electronics',            'electronics',            'Phones, laptops, tablets, chargers, earphones, calculators'),
    ('Bags & Backpacks',       'bags-backpacks',         'Backpacks, handbags, pouches, laptop bags'),
    ('Wallets & Purses',       'wallets-purses',         'Wallets, coin purses, card holders'),
    ('IDs & Cards',            'ids-cards',              'School IDs, ATM cards, licenses'),
    ('Keys',                   'keys',                   'Keys, keychains, key cards'),
    ('Books & Stationery',     'books-stationery',       'Books, notebooks, pens, art supplies'),
    ('Clothing & Accessories', 'clothing-accessories',   'Jackets, uniforms, caps, scarves, umbrellas'),
    ('Water Bottles & Containers', 'water-bottles-containers', 'Tumblers, bottles, lunch boxes'),
    ('Jewelry & Watches',      'jewelry-watches',        'Rings, necklaces, bracelets, watches'),
    ('Sports Equipment',       'sports-equipment',       'Balls, rackets, gym items'),
    ('Documents',              'documents',              'Papers, certificates, forms'),
    ('Others',                 'others',                 'Anything that does not fit another category')
on conflict (slug) do nothing;

-- Storage buckets ------------------------------------------------------
-- item-photos    : PUBLIC  (photos shown on listing cards; file names are random UUIDs)
-- claim-evidence : PRIVATE (only reachable through short-lived signed URLs created by Laravel)
insert into storage.buckets (id, name, public, file_size_limit, allowed_mime_types)
values
    ('item-photos',    'item-photos',    true,  5242880, array['image/jpeg', 'image/png', 'image/webp']),
    ('claim-evidence', 'claim-evidence', false, 5242880, array['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
on conflict (id) do update
    set public             = excluded.public,
        file_size_limit    = excluded.file_size_limit,
        allowed_mime_types = excluded.allowed_mime_types;

-- No storage.objects policies are created on purpose:
-- uploads/reads/deletes are done server-side by Laravel with the service-role key.
