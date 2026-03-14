<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:30px;">
    <div style="max-width:480px; margin:auto; background:#fff; padding:30px; border-radius:8px;">
        <h2 style="color:#333;">Password Reset OTP</h2>
        <p style="color:#555;">Use the OTP below to reset your password. It is valid for <strong>10 minutes</strong>.</p>
        <div style="font-size:36px; font-weight:bold; letter-spacing:8px; color:#4A90E2; margin:20px 0;">
            {{ $otp }}
        </div>
        <p style="color:#999; font-size:12px;">If you did not request this, please ignore this email.</p>
    </div>
</body>
</html>
