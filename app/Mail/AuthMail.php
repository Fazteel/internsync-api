<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuthMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $link;
    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $link, $type)
    {
        $this->user = $user;
        $this->link = $link;
        $this->type = $type;
    }

    public function build()
    {
        $subject = $this->type === 'activation' ? 'Aktivasi Akun & Set Password' : 'Reset Password';

        return $this->subject($subject)
                    ->view('emails.auth')
                    ->with([
                        'name' => $this->user->name,
                        'link' => $this->link,
                        'type' => $this->type
                    ]);
    }
}
