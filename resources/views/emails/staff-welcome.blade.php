<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, rgb(0, 127, 127), rgb(255, 98, 0)); color: white; padding: 30px; border-radius: 8px 8px 0 0; text-align: center; }
        .header h1 { margin: 0; font-size: 26px; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .section { margin: 20px 0; padding: 15px; background: white; border-left: 4px solid rgb(0, 127, 127); }
        .detail { display: flex; justify-content: space-between; margin: 10px 0; padding-bottom: 10px; border-bottom: 1px solid #eee; }
        .detail strong { color: rgb(0, 127, 127); }
        .credential { font-family: 'Courier New', monospace; font-size: 15px; font-weight: bold; background: #f1f5f9; padding: 4px 10px; border-radius: 5px; }
        .button { display: inline-block; background: rgb(0, 127, 127); color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .warning { background: #fff8e1; border-left: 4px solid #f59e0b; padding: 12px 15px; margin: 20px 0; font-size: 14px; color: #92400e; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; border-top: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔑 Admin Portal Account Created</h1>
            <p>You've been added as a system user</p>
        </div>

        <div class="content">
            <p>Hello {{ $name }},</p>

            <p>An administrator has created an account for you on the Forus Freight admin portal, with the role of <strong>{{ $roleName }}</strong>.</p>

            <div class="section">
                <h3>Your Login Credentials</h3>
                <div class="detail">
                    <strong>Email:</strong>
                    <span>{{ $email }}</span>
                </div>
                <div class="detail">
                    <strong>Temporary Password:</strong>
                    <span class="credential">{{ $temporaryPassword }}</span>
                </div>
            </div>

            <div class="warning">
                <strong>⚠️ This password is temporary.</strong> You'll be required to set your own password the first time you log in — it won't be shown again after this email.
            </div>

            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="button" style="color: #ffffff;">Log In to Admin Portal</a>
            </div>

            <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 14px;">
                Didn't expect this email or think it was sent in error? Contact your administrator at <strong>+260572788685</strong> (WhatsApp) or visit <a href="https://forusfl.co.zm" style="color: rgb(0, 127, 127);">forusfl.co.zm</a>.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Forus Freight Ltd. All rights reserved.</p>
            <p>Kafure Road, Lusaka, Zambia</p>
        </div>
    </div>
</body>
</html>
