<?php

/*
|--------------------------------------------------------------------------
| Supabase (server-side only)
|--------------------------------------------------------------------------
|
| Used by the PHP backend to talk to Supabase Storage. The service role key
| bypasses all Supabase security rules, so it must ONLY ever be read from the
| server environment. Never print it, log it, or pass it to a Blade view.
|
*/

return [

    'url' => rtrim((string) env('SUPABASE_URL', ''), '/'),

    'service_role_key' => env('SUPABASE_SERVICE_ROLE_KEY'),

    'buckets' => [
        // Public bucket: item photographs shown on listing cards.
        'photos' => env('SUPABASE_PHOTOS_BUCKET', 'item-photos'),

        // Private bucket: claim evidence, reachable only through short-lived signed URLs.
        'evidence' => env('SUPABASE_EVIDENCE_BUCKET', 'claim-evidence'),
    ],

    // Upload rules (enforced server-side in later phases).
    'max_upload_kb' => 5120,

];
