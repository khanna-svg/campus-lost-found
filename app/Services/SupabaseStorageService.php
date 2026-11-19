<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SupabaseStorageService
{
    protected string $url;
    protected string $serviceKey;
    protected string $photosBucket;
    protected string $evidenceBucket;

    public function __construct()
    {
        $this->url = rtrim((string) config('supabase.url'), '/');
        $this->serviceKey = (string) config('supabase.service_role_key');
        $this->photosBucket = (string) config('supabase.buckets.photos', 'item-photos');
        $this->evidenceBucket = (string) config('supabase.buckets.evidence', 'claim-evidence');
    }

    /**
     * Upload an item photograph or claim file directly to Supabase Storage.
     *
     * @param UploadedFile $file
     * @param string|null $bucket Defaults to photos bucket
     * @param string|null $folder e.g. 'lost' or 'found'
     * @return string Path relative to bucket
     * @throws Exception
     */
    public function upload(UploadedFile $file, ?string $bucket = null, ?string $folder = null): string
    {
        $bucket = $bucket ?? $this->photosBucket;
        $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg';
        $filename = Str::uuid()->toString() . '.' . strtolower($extension);
        $path = $folder ? trim($folder, '/') . '/' . $filename : $filename;

        $response = Http::withHeaders([
            'apikey' => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
            'Content-Type' => $file->getMimeType() ?: 'application/octet-stream',
            'x-upsert' => 'true',
        ])->withBody(file_get_contents($file->getRealPath()), $file->getMimeType() ?: 'application/octet-stream')
          ->post("{$this->url}/storage/v1/object/{$bucket}/{$path}");

        if (! $response->successful()) {
            throw new Exception('Supabase Storage upload failed: ' . $response->body());
        }

        return $path;
    }

    /**
     * Get the public URL for an object stored in a public bucket.
     */
    public function publicUrl(string $path, ?string $bucket = null): string
    {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $bucket = $bucket ?? $this->photosBucket;

        return "{$this->url}/storage/v1/object/public/{$bucket}/" . ltrim($path, '/');
    }

    /**
     * Generate a temporary signed URL for an object in a private bucket (e.g. claim-evidence).
     */
    public function signedUrl(string $path, int $expiresIn = 3600, ?string $bucket = null): ?string
    {
        $bucket = $bucket ?? $this->evidenceBucket;

        $response = Http::withHeaders([
            'apikey' => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->post("{$this->url}/storage/v1/object/sign/{$bucket}/" . ltrim($path, '/'), [
            'expiresIn' => $expiresIn,
        ]);

        if (! $response->successful()) {
            return null;
        }

        $signedUri = $response->json('signedURL');

        if (! $signedUri) {
            return null;
        }

        // Supabase returns relative path starting with /object/sign/..., needs /storage/v1
        if (str_starts_with($signedUri, '/object/')) {
            return "{$this->url}/storage/v1" . $signedUri;
        }

        return "{$this->url}" . $signedUri;
    }

    /**
     * Delete an object from a bucket.
     */
    public function delete(string $path, ?string $bucket = null): bool
    {
        $bucket = $bucket ?? $this->photosBucket;

        $response = Http::withHeaders([
            'apikey' => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
        ])->delete("{$this->url}/storage/v1/object/{$bucket}", [
            'prefixes' => [$path],
        ]);

        return $response->successful();
    }
}
