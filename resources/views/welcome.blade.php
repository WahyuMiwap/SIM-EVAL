<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="0; url={{ route('operator.dashboard') }}">
    <title>SIM-EVAL — P2M BNN Kota Surabaya</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #F5F6FA;
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        }
        .wrap {
            text-align: center;
            color: #4B5563;
        }
        .logo {
            width: 48px; height: 48px;
            background: #4361EE;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .logo svg { width: 24px; height: 24px; color: #fff; }
        h1 { font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: .25rem; }
        p  { font-size: 0.875rem; color: #6B7280; }
        a  { color: #4361EE; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="logo">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
        </div>
        <h1>SIM-EVAL P2M</h1>
        <p>Mengalihkan ke dashboard&hellip; <a href="{{ route('operator.dashboard') }}">Klik di sini</a></p>
    </div>
</body>
</html>
