<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemReturn extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'claim_request_id',
        'found_item_id',
        'recorded_by',
        'returned_to_name',
        'lrn',
        'course_year',
        'id_presented',
        'notes',
        'returned_at',
    ];

    protected function casts(): array
    {
        return [
            'returned_at' => 'datetime',
        ];
    }

    public function claimRequest(): BelongsTo
    {
        return $this->belongsTo(ClaimRequest::class, 'claim_request_id');
    }

    public function foundItem(): BelongsTo
    {
        return $this->belongsTo(FoundItem::class, 'found_item_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
