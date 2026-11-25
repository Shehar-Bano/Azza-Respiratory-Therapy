<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Azza Respiratory Therapy</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
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
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
        }
        .icon-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }
        .icon-success { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.35); }
        .icon-error { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.35); }
        h1 { font-size: 1.35rem; font-weight: 800; margin-bottom: 0.75rem; }
        p { font-size: 0.925rem; color: #94a3b8; line-height: 1.6; }
        .brand { font-size: 0.75rem; color: #64748b; margin-top: 1.75rem; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        @if($verified)
            <div class="icon-circle icon-success">
                <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1>Email Verified!</h1>
            <p>{{ $message }}</p>
        @else
            <div class="icon-circle icon-error">
                <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h1>Verification Failed</h1>
            <p>{{ $message }}</p>
        @endif
        <div class="brand">Azza Respiratory Therapy</div>
    </div>
</body>
</html>
