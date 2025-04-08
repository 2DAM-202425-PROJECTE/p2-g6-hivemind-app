<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Hivemind</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f4f4f4; color: #333;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <!-- Header -->
                <tr>
                    <td style="background: linear-gradient(to right, #fbbf24, #f59e0b); padding: 20px; text-align: center;">
                        <img src="https://i.imgur.com/zfZQrZO.png" alt="Hivemind Logo" style="max-width: 50px; height: auto;" />
                        <h1 style="color: #ffffff; font-size: 24px; margin: 10px 0 0;">Hivemind</h1>
                    </td>
                </tr>
                <!-- Content -->
                <tr>
                    <td style="padding: 30px; text-align: center;">
                        @yield('content')
                    </td>
                </tr>
                <!-- Footer -->
                <tr>
                    <td style="background-color: #f9fafb; padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
                        <p style="margin: 0 0 10px;">© 2025 Hivemind. All rights reserved.</p>
                        <p style="margin: 0;">Need a hand? Buzz us at <a href="mailto:support@hivemind.com" style="color: #f59e0b; text-decoration: none;">support@hivemind.com</a></p>
                        <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
                        <p style="font-size: 12px; color: #777;">This is an automated buzz. Please don’t reply directly.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
