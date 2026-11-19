<?php

namespace App\Mail;

use App\Models\ClaimRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaimApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public ClaimRequest $claim
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $itemName = $this->claim->foundItem->item_name ?? 'Item';

        return new Envelope(
            subject: "Your Claim for '{$itemName}' Has Been Approved! Ready for Pickup",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.claim-approved',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
