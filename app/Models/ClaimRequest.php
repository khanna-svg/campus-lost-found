<?php

namespace App\Models;

use App\Enums\ClaimRequestStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimRequest extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'found_item_id',
        'claimant_id',
        'lost_item_id',
        'status',
        'proof_description',
        'evidence_paths',
        'claimant_response',
        'admin_message',
        'internal_notes',
        'rejection_reason',
        'pickup_instructions',
        'reviewed_by',
        'reviewed_at',
        'approved_by',
        'approved_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ClaimRequestStatus::class,
            'evidence_paths' => 'array',
            'reviewed_at' => 'datetime',
            'approved_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function foundItem(): BelongsTo
    {
        return $this->belongsTo(FoundItem::class, 'found_item_id');
    }

    public function claimant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimant_id');
    }

    public function lostItem(): BelongsTo
    {
        return $this->belongsTo(LostItem::class, 'lost_item_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function itemReturn(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ItemReturn::class, 'claim_request_id');
    }

    /**
     * Generate temporary signed URLs for the claimant and admin to view evidence photos.
     *
     * @return array<int, string>
     */
    public function getEvidenceUrlsAttribute(): array
    {
        if (empty($this->evidence_paths) || ! is_array($this->evidence_paths)) {
            return [];
        }

        $storage = app(\App\Services\SupabaseStorageService::class);
        $urls = [];

        foreach ($this->evidence_paths as $path) {
            $signed = $storage->signedUrl($path, 3600);
            if ($signed) {
                $urls[] = $signed;
            }
        }

        return $urls;
    }
}
