<?php

namespace App\Mail;

use App\Models\PendingUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $user;

    public function __construct($token)
    {
        $this->token = $token;
        $this->user = PendingUser::where('verification_token', $token)->first();
    }

    public function build()
    {
        return $this->from(config('mail.noreply.address'), config('mail.noreply.name'))
            ->subject('Verify Your Email - Hivemind')
            ->view('emails.verify')
            ->with([
                'url' => env('FRONTEND_URL') . '/auth/verify-email?token=' . $this->token,
                'user' => $this->user
            ]);
    }
}
