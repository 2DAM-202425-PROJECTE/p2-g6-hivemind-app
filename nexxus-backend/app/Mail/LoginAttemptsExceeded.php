<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;

class LoginAttemptsExceeded extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $ipAddress;

    public function __construct(User $user, string $ipAddress)
    {
        $this->user = $user;
        $this->ipAddress = $ipAddress;
    }

    public function build()
    {
        return $this->from(config('mail.noreply.address'), config('mail.noreply.name'))
            ->subject('Suspicious Activity in Your Hive')
            ->view('emails.login_attempts_exceeded')
            ->with([
                'user' => $this->user,
                'ipAddress' => $this->ipAddress,
                'frontendUrl' => env('FRONTEND_URL'),
            ]);
    }
}
