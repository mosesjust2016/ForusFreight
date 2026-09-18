<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Set Your Password - Forus Freight</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f4f7f6 0%, #e8f5e9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: #1e293b;
        }
        .card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,.12);
            padding: 2.5rem;
            max-width: 440px;
            width: 100%;
        }
        .logo { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; }
        .logo img { height: 40px; }
        .logo span { font-size: 1.25rem; font-weight: 800; }
        .badge {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: #fff8e1; color: #b45309; font-size: 0.72rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.05em;
            padding: 0.4rem 0.85rem; border-radius: 50px; margin-bottom: 1rem;
        }
        h1 { font-size: 1.5rem; font-weight: 900; margin-bottom: 0.5rem; }
        p.lead { color: #64748b; font-size: 0.9rem; margin-bottom: 1.75rem; }
        .form-group { margin-bottom: 1.25rem; }
        label { font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; display: block; margin-bottom: 0.4rem; }
        input {
            width: 100%; padding: 0.85rem 1.1rem; border: 2px solid #f1f5f9; border-radius: 12px;
            font-size: 0.95rem; font-weight: 600; background: #f8fafc; transition: all 0.2s;
        }
        input:focus { outline: none; border-color: #007f7f; background: white; }
        .error { color: #ef4444; font-size: 0.75rem; font-weight: 700; margin-top: 0.35rem; display: block; }
        .btn-submit {
            width: 100%; background: #007f7f; color: white; padding: 1rem; border: none;
            border-radius: 12px; font-weight: 800; font-size: 0.95rem; cursor: pointer;
            transition: all 0.2s; margin-top: 0.5rem;
        }
        .btn-submit:hover { background: #006666; transform: translateY(-1px); }
        .hint { font-size: 0.75rem; color: #94a3b8; margin-top: 1.25rem; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <img src="{{ asset('images/logo-transparent.png') }}" alt="Forus Freight">
            <span>Forus Freight</span>
        </div>
        <div class="badge"><i class="fas fa-triangle-exclamation"></i> Action required</div>
        <h1>Set your password</h1>
        <p class="lead">You're signing in for the first time with a temporary password. Choose a new password to continue to the admin portal.</p>

        <form method="POST" action="{{ route('admin.force-password-change.update') }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Temporary Password</label>
                <input type="password" name="current_password" autocomplete="current-password" placeholder="The password you were emailed" required autofocus>
                @error('current_password', 'updateForcedPassword')<span class="error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="password" autocomplete="new-password" placeholder="••••••••" required>
                @error('password', 'updateForcedPassword')<span class="error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="password_confirmation" autocomplete="new-password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-lock"></i> Set Password &amp; Continue
            </button>
        </form>

        <p class="hint">Trouble logging in? Contact your administrator.</p>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            if (e.defaultPrevented) return;
            const btn = this.querySelector('button[type="submit"]');
            btn.dataset.originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Setting password...';
            btn.disabled = true;
            btn.style.opacity = '0.7';
        });
    </script>
</body>
</html>
