<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\AppNotification;
use App\Models\ClaimRequest;
use App\Models\FoundItem;
use App\Models\ItemReturn;
use App\Models\LostItem;
use App\Models\User;

class NotificationService
{
    /**
     * Dispatch a notification to a specific user.
     */
    public function send(
        User|string $user,
        string $type,
        string $title,
        string $message,
        ?string $relatedType = null,
        ?string $relatedId = null
    ): AppNotification {
        $userId = $user instanceof User ? $user->id : $user;

        // Ensure related_type and related_id are either both provided or both null (enforced by DB constraint)
        if (($relatedType === null && $relatedId !== null) || ($relatedType !== null && $relatedId === null)) {
            $relatedType = null;
            $relatedId = null;
        }

        return AppNotification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
        ]);
    }

    /**
     * Broadcast a notification to all active campus administrators.
     */
    public function notifyAdmins(
        string $type,
        string $title,
        string $message,
        ?string $relatedType = null,
        ?string $relatedId = null
    ): void {
        $adminIds = User::where('role', UserRole::Admin)
            ->where('is_active', true)
            ->pluck('id');

        foreach ($adminIds as $adminId) {
            $this->send($adminId, $type, $title, $message, $relatedType, $relatedId);
        }
    }

    /**
     * 1. Report Approved
     */
    public function reportApproved(LostItem|FoundItem $item): AppNotification
    {
        $isLost = $item instanceof LostItem;
        $relatedType = $isLost ? 'lost_item' : 'found_item';

        return $this->send(
            $item->user_id,
            'report_approved',
            'Report Approved · ' . $item->item_name,
            "Your report for '{$item->item_name}' has been verified and published to the campus directory.",
            $relatedType,
            $item->id
        );
    }

    /**
     * 2. Report Rejected
     */
    public function reportRejected(LostItem|FoundItem $item, string $reason): AppNotification
    {
        $isLost = $item instanceof LostItem;
        $relatedType = $isLost ? 'lost_item' : 'found_item';

        return $this->send(
            $item->user_id,
            'report_rejected',
            'Report Not Approved · ' . $item->item_name,
            "Your report for '{$item->item_name}' could not be published. Reason: {$reason}",
            $relatedType,
            $item->id
        );
    }

    /**
     * 3. Report Correction Requested
     */
    public function reportCorrectionRequested(LostItem|FoundItem $item, string $note): AppNotification
    {
        $isLost = $item instanceof LostItem;
        $relatedType = $isLost ? 'lost_item' : 'found_item';

        return $this->send(
            $item->user_id,
            'report_correction_requested',
            'Corrections Requested · ' . $item->item_name,
            "An administrator requested corrections for '{$item->item_name}': {$note}",
            $relatedType,
            $item->id
        );
    }

    /**
     * 4. New Claim Submitted (Notifies Finder if kept by finder, and Administrators)
     */
    public function claimReceived(ClaimRequest $claim): void
    {
        $itemName = $claim->foundItem->item_name;
        $claimantName = $claim->claimant->name;

        // If the item is in the finder's possession, notify the finder directly!
        if ($claim->foundItem->isKeptByFinder()) {
            $this->send(
                $claim->foundItem->user_id,
                'claim_received',
                'New Claim on Your Found Item · ' . $itemName,
                "{$claimantName} submitted an ownership claim for '{$itemName}' that you are currently keeping. Review their proof to coordinate the return.",
                'claim_request',
                $claim->id
            );
        }

        // Also notify administrators for system awareness
        $this->notifyAdmins(
            'claim_received',
            'New Claim Submitted · ' . $itemName,
            "{$claimantName} submitted an ownership claim for '{$itemName}'.",
            'claim_request',
            $claim->id
        );
    }

    /**
     * 5. Claim Approved (Notifies Claimant via in-app notification & email)
     */
    public function claimApproved(ClaimRequest $claim): AppNotification
    {
        $itemName = $claim->foundItem->item_name;
        $isKeptByFinder = $claim->foundItem->isKeptByFinder();

        $message = $isKeptByFinder
            ? "Your claim for '{$itemName}' was approved by the finder! Check the meetup instructions to retrieve your item."
            : "Your claim for '{$itemName}' was approved! Your item is secured and ready for pickup at the Admin Office. Please bring your school ID.";

        // 1. In-App Notification
        $notification = $this->send(
            $claim->claimant_id,
            'claim_approved',
            'Claim Approved · ' . $itemName,
            $message,
            'claim_request',
            $claim->id
        );

        // 2. Email Notification to Student
        if ($claim->claimant && $claim->claimant->email) {
            try {
                \Illuminate\Support\Facades\Mail::to($claim->claimant->email)
                    ->send(new \App\Mail\ClaimApprovedMail($claim));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Failed to send claim approval email to {$claim->claimant->email}: " . $e->getMessage());
            }
        }

        return $notification;
    }

    /**
     * 6. Additional Information Required (Notifies Claimant)
     */
    public function claimInfoRequired(ClaimRequest $claim, string $adminMessage): AppNotification
    {
        $itemName = $claim->foundItem->item_name;
        $isKeptByFinder = $claim->foundItem->isKeptByFinder();
        $senderTitle = $isKeptByFinder ? 'The finder' : 'An administrator';

        return $this->send(
            $claim->claimant_id,
            'claim_info_required',
            'Action Needed: More Details Requested · ' . $itemName,
            "{$senderTitle} requested additional information on your claim: {$adminMessage}",
            'claim_request',
            $claim->id
        );
    }

    /**
     * 7. Claim Rejected
     */
    public function claimRejected(ClaimRequest $claim, string $reason): AppNotification
    {
        $itemName = $claim->foundItem->item_name;
        $isKeptByFinder = $claim->foundItem->isKeptByFinder();
        $senderTitle = $isKeptByFinder ? 'the finder' : 'an administrator';

        return $this->send(
            $claim->claimant_id,
            'claim_rejected',
            'Claim Not Approved · ' . $itemName,
            "Your claim for '{$itemName}' was rejected by {$senderTitle}. Reason: {$reason}",
            'claim_request',
            $claim->id
        );
    }

    /**
     * 8. Item Returned (Physical hand-over complete)
     */
    public function itemReturned(ItemReturn $return): void
    {
        $itemName = $return->foundItem->item_name;
        $claimantId = $return->claimRequest->claimant_id;
        $finderId = $return->foundItem->user_id;

        // Notify claimant
        $this->send(
            $claimantId,
            'item_returned',
            'Item Returned Successfully · ' . $itemName,
            "Your property '{$itemName}' has been marked as physically returned and signed for.",
            'claim_request',
            $return->claim_request_id
        );

        // Notify finder if different
        if ($finderId !== $claimantId) {
            $this->send(
                $finderId,
                'item_returned',
                'Item Reunited! · ' . $itemName,
                "Great news! The item '{$itemName}' that you turned in has been reunited with its owner.",
                'found_item',
                $return->found_item_id
            );
        }
    }
}
