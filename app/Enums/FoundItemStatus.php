<?php

namespace App\Enums;

enum FoundItemStatus: string
{
    case PendingReview = 'pending_review';
    case Approved = 'approved';
    case Claimed = 'claimed';
    case Returned = 'returned';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::PendingReview => 'Pending Review',
            self::Approved => 'Approved',
            self::Claimed => 'Claimed',
            self::Returned => 'Returned',
            self::Archived => 'Archived',
        };
    }

    public function colorClass(): string
    {
        return match ($this) {
            self::PendingReview => 'bg-amber-50 text-amber-800 ring-amber-300',
            self::Approved => 'bg-emerald-50 text-emerald-800 ring-emerald-300',
            self::Claimed => 'bg-indigo-50 text-indigo-800 ring-indigo-300',
            self::Returned => 'bg-teal-50 text-teal-800 ring-teal-300',
            self::Archived => 'bg-slate-50 text-slate-700 ring-slate-300',
        };
    }
}
