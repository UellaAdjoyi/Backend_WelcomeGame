<!DOCTYPE html>
<html>

<head>
    <title>Password Reset Request</title>
</head>

<body>
    <h1>Password Reset Request</h1>
    <p>Hello,</p>
    <p>We received a request to reset your password. Click the link below to reset it:</p>
    {{-- <a href="http://localhost:8100/reset-password{{ $resetLink }}">{{ $resetLink }}</a> --}}
    {{-- <a href="http://192.168.0.10:8100/reset-password{{ $resetLink }}">{{ $resetLink }}</a> --}}
    <a href="http://172.23.36.43:8100/reset-password{{ $resetLink }}">{{ $resetLink }}</a>

    <p>If you didn't request a password reset, you can safely ignore this email.</p>
    <p>Thanks,<br>Your App Team</p>
</body>

</html>
