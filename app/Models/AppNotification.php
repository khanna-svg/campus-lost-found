<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'related_type',
        'related_id',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        if (! $this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead(Builder $query): Builder
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Compute route to the related model if applicable.
     */
    public function getActionUrlAttribute(): ?string
    {
        if (! $this->related_type || ! $this->related_id) {
            return null;
        }

        return match ($this->related_type) {
            'lost_item' => route('lost-items.show', $this->related_id),
            'found_item' => route('found-items.show', $this->related_id),
            'claim_request' => route('claims.show', $this->related_id),
            default => null,
        };
    }
}
