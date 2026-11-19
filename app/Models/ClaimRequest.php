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
}
