<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Azza Respiratory Therapy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-gradient: radial-gradient(circle at 50% 20%, #0a2540 0%, #07111e 65%, #040913 100%);
            --card-bg: rgba(15, 34, 56, 0.75);
            --card-border: rgba(0, 210, 223, 0.2);
            --primary: #00b4d8;
            --primary-hover: #00d2df;
            --primary-glow: rgba(0, 210, 223, 0.35);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --danger-bg: rgba(239, 68, 68, 0.15);
            --danger-border: rgba(239, 68, 68, 0.3);
            --danger-text: #fca5a5;
            --success-bg: rgba(16, 185, 129, 0.15);
            --success-border: rgba(16, 185, 129, 0.3);
            --success-text: #6ee7b7;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            min-height: 100vh;
            background: var(--bg-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: var(--text-primary);
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient Glow Effects matching logo cyan & ocean blue */
        body::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(0, 229, 255, 0.2) 0%, rgba(0,0,0,0) 70%);
            top: 10%;
            left: 15%;
            border-radius: 50%;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(0, 119, 182, 0.25) 0%, rgba(0,0,0,0) 70%);
            bottom: 10%;
            right: 15%;
            border-radius: 50%;
            pointer-events: none;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2.75rem 2.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7), 0 0 30px rgba(0, 210, 223, 0.15);
            z-index: 10;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.85rem;
            background: rgba(0, 210, 223, 0.12);
            border: 1px solid rgba(0, 210, 223, 0.3);
            border-radius: 100px;
            color: #38bdf8;
            font-size: 0.825rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
        }

        .brand-badge img {
            width: 22px;
            height: 22px;
            object-fit: contain;
        }

        .login-header {
            margin-bottom: 2rem;
        }

        .login-header h1 {
            font-size: 1.85rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #ffffff;
            margin-bottom: 0.4rem;
        }

        .login-header p {
            color: var(--text-secondary);
            font-size: 0.925rem;
        }

        .alert {
            padding: 0.9rem 1.1rem;
            border-radius: 14px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            line-height: 1.4;
        }

        .alert-danger {
            background: var(--danger-bg);
            border: 1px solid var(--danger-border);
            color: var(--danger-text);
        }

        .alert-success {
            background: var(--success-bg);
            border: 1px solid var(--success-border);
            color: var(--success-text);
        }

        .form-group {
            margin-bottom: 1.35rem;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            width: 18px;
            height: 18px;
            transition: color 0.2s ease;
        }

        .form-control {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.75rem;
            background: rgba(7, 17, 30, 0.65);
            border: 1px solid rgba(0, 210, 223, 0.2);
            border-radius: 14px;
            color: #ffffff;
            font-size: 0.95rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
        }

        .form-control::placeholder {
            color: #64748b;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
            background: rgba(7, 17, 30, 0.9);
        }

        .form-control:focus + .input-icon {
            color: var(--primary);
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
        }

        .remember-checkbox {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            cursor: pointer;
            user-select: none;
        }

        .remember-checkbox input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 1px solid rgba(0, 210, 223, 0.3);
            border-radius: 6px;
            background: rgba(7, 17, 30, 0.65);
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
        }

        .remember-checkbox input[type="checkbox"]:checked {
            background: var(--primary);
            border-color: var(--primary);
        }

        .remember-checkbox input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 2px;
            width: 5px;
            height: 9px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .remember-text {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .btn-submit {
            width: 100%;
            padding: 0.95rem;
            background: linear-gradient(135deg, #00d2df 0%, #0077b6 100%);
            border: none;
            border-radius: 14px;
            color: #ffffff;
            font-size: 0.975rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 25px -5px rgba(0, 210, 223, 0.4);
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(0, 210, 223, 0.55);
            background: linear-gradient(135deg, #00e5ff 0%, #0096c7 100%);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .footer-note {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.825rem;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-badge">
            <img src="{{ asset('logo/azza_respiratory_icon.png') }}" alt="AZZA Logo">
            AZZA Respiratory Therapy
        </div>

        <div class="login-header">
            <h1>Admin Portal</h1>
            <p>Sign in to access your administrative dashboard</p>
        </div>

        @if(session('status'))
            <div class="alert alert-success">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" class="form-control" placeholder="admin@example.com" value="{{ old('email') }}" required autofocus>
                    <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                    <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>

            <div class="form-options">
                <label class="remember-checkbox">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="remember-text">Remember me</span>
                </label>
            </div>

            <button type="submit" class="btn-submit">
                <span>Sign In to Dashboard</span>
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </form>

        <div class="footer-note">
            Protected Admin System • Secured with Laravel Sanctum
        </div>
    </div>
</body>
</html>
