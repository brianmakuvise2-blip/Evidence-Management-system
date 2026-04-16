<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2b4c7c 100%);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f8fafc;
            padding: 30px 20px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #1e3a5f;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background: #0f2a44;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 12px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Password Reset Request</h1>
        </div>
        
        <div class="content">
            <p>Hello,</p>
            
            <p>We received a request to reset your password for your Evidence Management System account.</p>
            
            <p><strong>Click the button below to reset your password:</strong></p>
            
            <center>
                <a href="{{ $resetUrl }}" class="button">Reset Password</a>
            </center>
            
            <p style="text-align: center; color: #666; font-size: 12px;">
                Or copy and paste this link:<br>
                <code style="background: #e2e8f0; padding: 8px; border-radius: 4px; display: block; margin: 10px 0; word-break: break-all;">{{ $resetUrl }}</code>
            </p>
            
            <div class="warning">
                <strong>⏰ Important:</strong> This password reset link will expire in 60 minutes. If you didn't request a password reset, please ignore this email. Your account is still secure.
            </div>
            
            <p>If you have any questions or need assistance, please contact your system administrator.</p>
            
            <p>Best regards,<br>Evidence Management System</p>
            
            <div class="footer">
                <p>This is an automated message. Please do not reply to this email.</p>
                <p>&copy; {{ date('Y') }} Republic of Zimbabwe. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
