<?php

// tests/Feature/ContactControllerTest.php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Mail\ContactConfirmation;
use App\Mail\ContactNotification;
use PHPUnit\Framework\Attributes\Test;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function it_sends_emails_on_form_submission()
    {
        Mail::fake();

        $response = $this->post('/contact/submit', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'message' => 'This is a test message.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Form submitted successfully.']);

        Mail::assertSent(ContactConfirmation::class, function ($mail) {
            return $mail->hasTo('johndoe@example.com');
        });

        Mail::assertSent(ContactNotification::class, function ($mail) {
            return $mail->hasTo('hivemindnexxuscontact@gmail.com');
        });
    }
}
