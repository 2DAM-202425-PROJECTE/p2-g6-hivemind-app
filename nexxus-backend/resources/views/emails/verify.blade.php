@extends('layouts.email')

@section('title', 'Welcome to Hivemind')

@section('content')
    <tr>
        <td style="padding: 30px; text-align: center;">
            <h2 style="font-size: 20px; color: #1f2937; margin: 0 0 15px;">Welcome to the Hive, {{ $user->name ?? 'Busy Bee' }}!</h2>
            <p style="font-size: 16px; line-height: 1.5; color: #4b5563; margin: 0 0 20px;">
                Thanks for buzzing in! To join the Hivemind swarm, please verify your email address by clicking the button below.
            </p>
            <a href="{{ $url }}" style="display: inline-block; padding: 12px 24px; background-color: #f59e0b; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: bold; border-radius: 5px; margin-bottom: 20px;">Verify My Email</a>
            <p style="font-size: 14px; color: #6b7280; margin: 0 0 20px;">
                Or paste this link into your browser to start buzzing: <br>
                <a href="{{ $url }}" style="color: #f59e0b; word-break: break-all;">{{ $url }}</a>
            </p>
            <p style="font-size: 14px; color: #6b7280; margin: 0;">
                This link will keep the hive buzzing for 24 hours. If you didn’t sign up for Hivemind, feel free to ignore this email.
            </p>
            <p style="font-size: 14px; color: #6b7280; margin: 0;">
                We’re excited to have you in our colony!
            </p>
            <p style="font-size: 14px; color: #4b5563; margin: 10px 0 0;">
                <strong>The Hivemind Crew</strong>
            </p>
        </td>
    </tr>
@endsection
