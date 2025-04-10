<?php

// app/Http/Controllers/ContactController.php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactConfirmation;
use App\Mail\ContactNotification;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:1000',
        ]);

        $data = $request->only('name', 'email', 'message');

        try {
            // Send confirmation email to the user
            Mail::to($data['email'])->send(new ContactConfirmation($data));

            // Send notification email to the admin
            Mail::to('hivemindnexxuscontact@gmail.com')->send(new ContactNotification($data));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send message. Please try again later.'], 500);
        }

        return response()->json(['message' => 'Form submitted successfully.'], 201);
    }
}
