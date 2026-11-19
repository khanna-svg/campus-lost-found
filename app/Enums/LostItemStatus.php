<?php

namespace App\Enums;

enum LostItemStatus: string
{
    case PendingReview = 'pending_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Resolved = 'resolved';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::PendingReview => 'Pending Review',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Resolved => 'Resolved',
            self::Archived => 'Archived',
        };
    }

    public function colorClass(): string
    {
        return match ($this) {
            self::PendingReview => 'bg-amber-50 text-amber-800 ring-amber-300',
            self::Approved => 'bg-emerald-50 text-emerald-800 ring-emerald-300',
            self::Rejected => 'bg-rose-50 text-rose-800 ring-rose-300',
            self::Resolved => 'bg-sky-50 text-sky-800 ring-sky-300',
            self::Archived => 'bg-slate-50 text-slate-700 ring-slate-300',
        };
    }
}
