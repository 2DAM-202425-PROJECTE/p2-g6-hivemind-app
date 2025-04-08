@extends('layouts.email')

@section('title', 'Suspicious Activity in Your Hive')

@section('content')
    <tr>
        <td style="padding: 30px; text-align: center;">
            <h2 style="font-size: 20px; color: #1f2937; margin: 0 0 15px;">Suspicious Activity in Your Hive, {{ $user->name ?? 'Busy Bee' }}!</h2>
            <p style="font-size: 16px; line-height: 1.5; color: #4b5563; margin: 0 0 20px;">
                We’ve detected a swarm of failed login attempts on your account tied to <strong>{{ $user->email }}</strong>.
            </p>
            <p style="font-size: 16px; color: #4b5563; margin: 0 0 10px;"><strong>Details:</strong></p>
            <ul style="font-size: 14px; color: #4b5563; text-align: left; display: inline-block; margin: 0 0 20px; padding: 0; list-style-position: inside;">
                <li>IP Address: {{ $ipAddress }}</li>
                <li>Date: {{ now()->toDateTimeString() }}</li>
            </ul>
            <p style="font-size: 16px; line-height: 1.5; color: #4b5563; margin: 0 0 20px;">
                If this wasn’t you buzzing around, here’s how to keep your hive safe:
            </p>
            <ul style="font-size: 14px; color: #4b5563; text-align: left; display: inline-block; margin: 0 0 20px; padding: 0; list-style-position: inside;">
                <li>Change your password right away by clicking <a href="{{ $frontendUrl }}/password/reset" style="color: #f59e0b;">here</a>.</li>
                <li>Check your recent activity for anything odd.</li>
                <li>Need help? Sting us a message at support.</li>
            </ul>
            <p style="font-size: 14px; color: #6b7280; margin: 0 0 10px;">
                If it was you, no worries—your account will be back in the honey flow in 15 minutes.
            </p>
            <p style="font-size: 14px; color: #6b7280; margin: 0 0 10px;">
                We take your security seriously and are here to keep your hive buzzing safely.
            </p>
            <p style="font-size: 14px; color: #4b5563; margin: 0;">
                <strong>The Hivemind Crew</strong>
            </p>
        </td>
    </tr>
@endsection
