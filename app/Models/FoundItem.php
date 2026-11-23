<?php

namespace App\Models;

use App\Enums\FoundItemStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FoundItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'category_id',
        'item_name',
        'public_description',
        'date_found',
        'location_found',
        'photo_path',
        'private_details',
        'remarks',
        'storage_location',
        'is_surrendered_to_office',
        'contact_info',
        'status',
        'needs_correction',
        'correction_note',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'date_found' => 'date',
            'is_surrendered_to_office' => 'boolean',
            'status' => FoundItemStatus::class,
            'needs_correction' => 'boolean',
            'reviewed_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function isSurrenderedToOffice(): bool
    {
        return (bool) $this->is_surrendered_to_office;
    }

    public function isKeptByFinder(): bool
    {
        return ! $this->is_surrendered_to_office;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function claimRequests(): HasMany
    {
        return $this->hasMany(ClaimRequest::class, 'found_item_id');
    }

    public function itemReturn(): HasOne
    {
        return $this->hasOne(ItemReturn::class, 'found_item_id');
    }

    /**
     * Helper to get public photo URL from Supabase Storage bucket.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo_path) {
            return null;
        }

        if (filter_var($this->photo_path, FILTER_VALIDATE_URL)) {
            return $this->photo_path;
        }

        $base = config('supabase.url');
        $bucket = config('supabase.buckets.photos', 'item-photos');

        return "{$base}/storage/v1/object/public/{$bucket}/{$this->photo_path}";
    }
}
