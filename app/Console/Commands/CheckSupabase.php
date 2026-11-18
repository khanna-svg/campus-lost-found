<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class CheckSupabase extends Command
{
    protected $signature = 'supabase:check';

    protected $description = 'Verify the Supabase PostgreSQL connection, schema and Storage buckets';

    public function handle(): int
    {
        $ok = true;

        $this->components->info('1. PostgreSQL connection');
        try {
            $row = DB::selectOne('select current_database() as db, version() as version');
            $this->line("   Connected to database \"{$row->db}\"");
            $this->line('   '.strtok($row->version, ','));
        } catch (Throwable $e) {
            $this->components->error('Cannot connect: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->components->info('2. Schema (tables created in Phase 2)');
        $expected = [
            'users', 'item_categories', 'lost_items', 'found_items', 'claim_requests',
            'item_returns', 'notifications', 'activity_logs', 'sessions', 'cache',
            'cache_locks', 'password_reset_tokens',
        ];
        $found = collect(DB::select("select table_name from information_schema.tables where table_schema = 'public'"))
            ->pluck('table_name');
        foreach ($expected as $table) {
            $present = $found->contains($table);
            $ok = $ok && $present;
            $this->line(($present ? '   [ok]      ' : '   [MISSING] ').$table);
        }

        $categories = DB::table('item_categories')->count();
        $this->line("   item_categories rows: {$categories} (expected 12)");

        $this->components->info('3. Supabase Storage buckets');
        $url = config('supabase.url');
        $key = config('supabase.service_role_key');
        if (! $url || ! $key || str_contains($url, 'YOUR_PROJECT_REF') || str_contains($key, 'YOUR_')) {
            $this->components->warn('SUPABASE_URL / SUPABASE_SERVICE_ROLE_KEY not filled in .env yet.');

            return self::FAILURE;
        }

        $headers = ['apikey' => $key];
        if (str_starts_with($key, 'eyJ')) { // legacy JWT-style service_role key
            $headers['Authorization'] = 'Bearer '.$key;
        }

        try {
            $response = Http::withHeaders($headers)->timeout(15)->get("{$url}/storage/v1/bucket");
        } catch (Throwable $e) {
            $this->components->error('Storage request failed: '.$e->getMessage());

            return self::FAILURE;
        }

        if (! $response->successful()) {
            $this->components->error('Storage API returned HTTP '.$response->status().' (check URL and key).');

            return self::FAILURE;
        }

        $buckets = collect($response->json())->keyBy('name');
        foreach (['photos' => true, 'evidence' => false] as $name => $shouldBePublic) {
            $bucketName = config("supabase.buckets.{$name}");
            $bucket = $buckets->get($bucketName);
            if (! $bucket) {
                $ok = false;
                $this->line("   [MISSING] {$bucketName}");

                continue;
            }
            $isPublic = (bool) ($bucket['public'] ?? false);
            $good = $isPublic === $shouldBePublic;
            $ok = $ok && $good;
            $this->line(($good ? '   [ok]      ' : '   [WRONG]   ').$bucketName.' ('.($isPublic ? 'public' : 'private').')');
        }

        $ok
            ? $this->components->info('All checks passed.')
            : $this->components->error('Some checks failed - see above.');

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
