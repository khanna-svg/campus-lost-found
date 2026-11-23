<?php

namespace App\Models;

use App\Enums\LostItemStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LostItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'category_id',
        'item_name',
        'description',
        'date_lost',
        'time_lost',
        'location_last_seen',
        'photo_path',
        'contact_info',
        'identifying_details',
        'status',
        'needs_correction',
        'correction_note',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'resolved_at',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'date_lost' => 'date',
            'status' => LostItemStatus::class,
            'needs_correction' => 'boolean',
            'reviewed_at' => 'datetime',
            'resolved_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
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
        return $this->hasMany(ClaimRequest::class, 'lost_item_id');
    }

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

        return "{$base}/storage/v1/object/public/{$bucket}/" . ltrim($this->photo_path, '/');
    }
}
