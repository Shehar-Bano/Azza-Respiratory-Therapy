<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Azza Respiratory Therapy</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body {
            min-height: 100vh;
            background: #07111e;
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .card {
            width: 100%;
            max-width: 420px;
            background: #0f2238;
            border: 1px solid #183554;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
        }
        h1 { font-size: 1.35rem; font-weight: 800; margin-bottom: 0.4rem; }
        .subtitle { font-size: 0.85rem; color: #94a3b8; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem; color: #e2e8f0; }
        .form-control {
            width: 100%;
            padding: 0.7rem 0.9rem;
            background: #07111e;
            border: 1px solid #183554;
            border-radius: 10px;
            color: #f8fafc;
            font-size: 0.9rem;
            outline: none;
        }
        .form-control:focus { border-color: #00b4d8; box-shadow: 0 0 0 3px rgba(0, 180, 216, 0.25); }
        .btn-submit {
            width: 100%;
            padding: 0.75rem 1rem;
            background: #00b4d8;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 0.925rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 0.5rem;
        }
        .btn-submit:hover { background: #0096c7; }
        .alert-error {
            padding: 0.75rem 1rem;
            border-radius: 10px;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fca5a5;
            font-size: 0.825rem;
            margin-bottom: 1rem;
        }
        .alert-success {
            padding: 1rem;
            border-radius: 10px;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #6ee7b7;
            font-size: 0.9rem;
            font-weight: 600;
            text-align: center;
        }
        .brand { text-align: center; font-size: 0.75rem; color: #64748b; margin-top: 1.5rem; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Reset Password</h1>
        <p class="subtitle">Enter your new password for Azza Respiratory Therapy.</p>

        @if(session('reset_success'))
            <div class="alert-success">
                {{ session('reset_success') }}
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Updated',
                        text: @json(session('reset_success')),
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#00b4d8',
                        background: '#0f2238',
                        color: '#f8fafc'
                    });
                });
            </script>
        @else
            @if($errors->any())
                <div class="alert-error">
                    <ul style="margin-left: 1rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('reset_error'))
                <div class="alert-error">{{ session('reset_error') }}</div>
            @endif

            <form action="{{ route('password.reset.webview.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="form-group">
                    <label class="form-label" for="password">New Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Minimum 8 characters" required minlength="8">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm new password" required minlength="8">
                </div>

                <button type="submit" class="btn-submit">Reset Password</button>
            </form>
        @endif

        <div class="brand">Azza Respiratory Therapy</div>
    </div>
</body>
</html>
