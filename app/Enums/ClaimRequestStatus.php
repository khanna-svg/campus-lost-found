<?php

namespace App\Enums;

enum ClaimRequestStatus: string
{
    case Pending = 'pending';
    case UnderReview = 'under_review';
    case AdditionalInformationRequired = 'additional_information_required';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::UnderReview => 'Under Review',
            self::AdditionalInformationRequired => 'Action Needed',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Completed => 'Completed',
        };
    }

    public function colorClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-50 text-amber-800 ring-amber-300',
            self::UnderReview => 'bg-blue-50 text-blue-800 ring-blue-300',
            self::AdditionalInformationRequired => 'bg-purple-50 text-purple-800 ring-purple-300',
            self::Approved => 'bg-emerald-50 text-emerald-800 ring-emerald-300',
            self::Rejected => 'bg-rose-50 text-rose-800 ring-rose-300',
            self::Completed => 'bg-teal-50 text-teal-800 ring-teal-300',
        };
    }
}
