<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Suspicious Activity in Your Hive</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
<h2 style="color: #1a73e8;">Hello, {{ $user -> name ?? 'Busy Bee' }}</h2>

<p>We’ve detected a swarm of failed login attempts on your account tied to <strong>{{ $user->email }}</strong>.</p>

<p><strong>Details:</strong></p>
<ul>
    <li>IP Address: {{ $ipAddress }}</li>
    <li>Date: {{ now()->toDateTimeString() }}</li>
</ul>

<p>If this wasn’t you buzzing around, here’s how to keep your hive safe:</p>
<ul>
    <li>Change your password right away by clicking <a href="{{ url('/password/reset') }}" style="color: #1a73e8;">here</a>.</li>
    <li>Check your recent activity for anything odd.</li>
    <li>Need help? Sting us a message at support.</li>
</ul>

<p>If it was you, no worries—your account will be back in the honey flow in 15 minutes.</p>

<p>We take your security seriously, and we’re here to help you keep your hive buzzing safely.</p>
<p>Thanks for being part of our hive!</p>
<p>For more tips on keeping your account secure, check out our <a href="{{ url('/security-tips') }}" style="color: #1a73e8;">security tips</a>.</p>
<p>And remember, if you ever feel like your account is in danger, you can always <a href="{{ url('/contact') }}" style="color: #1a73e8;">contact us</a>.</p>

<p>Stay safe and keep buzzing!</p>
<p><strong>The HiveMind Crew</strong></p>

<hr style="border: 0; border-top: 1px solid #eee;">
<p style="font-size: 12px; color: #777;">This is an automated buzz. Please don’t reply directly.</p>
</body>
</html>
