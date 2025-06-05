<?php

namespace App\Mail;

use App\Models\Membership; // Assuming Membership model namespace
use App\Models\Payment;    // Assuming Payment model namespace
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MembershipPaymentPending extends Mailable
{
    use Queueable, SerializesModels;

    public Membership $membership; // Make it public to be available in the view
    public Payment $payment;       // Make it public to be available in the view

    /**
     * Create a new message instance.
     */
    public function __construct(Membership $membership, Payment $payment) // Accept the objects
    {
        $this->membership = $membership;
        $this->payment = $payment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Membership Payment Pending Confirmation', // Slightly more descriptive
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            // CHOOSE A BETTER VIEW NAME
            // For example: 'emails.memberships.payment-pending'
            // This means the file will be at:
            // resources/views/emails/membership/payment-pending.blade.php
            view: 'emails.membership.payment-pending',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
