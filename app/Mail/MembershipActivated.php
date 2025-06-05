<?php

namespace App\Mail;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MembershipActivated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Membership $membership;
    public User $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Membership $membership)
    {
        $this->membership = $membership;
        $this->user = $membership->user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Membership has been Activated!')
                    ->markdown('emails.membership.activated'); // Create this Blade view
    }
}
