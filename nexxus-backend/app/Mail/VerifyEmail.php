<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        return $this->from(config('mail.noreply.address'), config('mail.noreply.name'))
            ->subject('Verify Your Email')
            ->view('emails.verify')
            ->with([
                'url' => env('FRONTEND_URL') . '/auth/verify-email?token=' . $this->token,
            ]);
    }
}
