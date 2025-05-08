<!DOCTYPE html>
<html>

<head>
    <title>Password Reset Request</title>
</head>

<body>
<h1>Hello {{ $user->email }}</h1>
<p>Your password has been reset by the administrator.</p>
<p>Your new password is: <strong>{{ $newPassword }}</strong></p>
<p>You can log in and change it in your profile settings.</p>
</body>

</html>
