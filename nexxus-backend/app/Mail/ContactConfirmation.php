<?php

// app/Mail/ContactConfirmation.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->from(config('mail.contact.address'), config('mail.contact.name'))
            ->view('emails.contact_confirmation')
            ->with('data', $this->data);
    }
}
