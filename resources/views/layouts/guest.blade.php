<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>PICTO-RTS | Login</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/@zxing/library@latest"></script>

        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'Figtree', sans-serif; background: #eef1f5; min-height: 100vh; }

            .login-wrapper {
                display: flex;
                min-height: 100vh;
                align-items: center;
                justify-content: center;
            }

            .login-container {
                display: flex;
                max-width: 960px;
                width: 100%;
                background: transparent;
                align-items: center;
                gap: 0;
            }

            /* Left side - illustration */
            .login-left {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 40px;
            }
            .login-left svg {
                max-width: 380px;
                width: 100%;
                height: auto;
            }

            /* Right side - form */
            .login-right {
                flex: 1;
                max-width: 420px;
                padding: 20px 40px;
            }

            .login-logo {
                text-align: center;
                margin-bottom: 30px;
            }
            .login-logo .logo-title {
                font-size: 26px;
                font-weight: 800;
                line-height: 1.1;
            }
            .login-logo .logo-title .red { color: #c0392b; }
            .login-logo .logo-title .blue { color: #1a1a6c; }
            .login-logo .logo-sub {
                font-size: 11px;
                color: #c0392b;
                font-weight: 600;
                background: linear-gradient(90deg, #c0392b, #1a1a6c);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                margin-top: 4px;
                letter-spacing: 1px;
            }

            .login-form-group {
                margin-bottom: 16px;
            }
            .login-form-group input[type="email"],
            .login-form-group input[type="password"],
            .login-form-group input[type="text"] {
                width: 100%;
                padding: 12px 16px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 14px;
                outline: none;
                background: #fff;
                color: #444;
            }
            .login-form-group input:focus {
                border-color: #1a1a6c;
                box-shadow: 0 0 0 2px rgba(26,26,108,0.08);
            }
            .login-form-group input::placeholder {
                color: #aaa;
            }

            .login-options {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 20px;
                font-size: 13px;
            }
            .login-options label {
                display: flex;
                align-items: center;
                gap: 6px;
                color: #555;
                cursor: pointer;
            }
            .login-options label input[type="checkbox"] {
                accent-color: #1a1a6c;
                width: 16px;
                height: 16px;
            }
            .login-options a {
                color: #1a1a6c;
                text-decoration: none;
                font-weight: 500;
            }
            .login-options a:hover {
                text-decoration: underline;
            }

            .btn-login {
                width: 100%;
                padding: 14px;
                background: #1a1a6c;
                color: #fff;
                border: none;
                border-radius: 30px;
                font-size: 15px;
                font-weight: 700;
                cursor: pointer;
                letter-spacing: 1px;
                text-transform: uppercase;
                transition: background 0.2s;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            .btn-login:hover {
                background: #12124e;
            }

            .login-divider {
                text-align: center;
                color: #999;
                font-size: 13px;
                margin: 14px 0;
            }

            .btn-track {
                width: 100%;
                padding: 14px;
                background: #c0392b;
                color: #fff;
                border: none;
                border-radius: 30px;
                font-size: 15px;
                font-weight: 700;
                cursor: pointer;
                letter-spacing: 1px;
                text-transform: uppercase;
                transition: background 0.2s;
                text-decoration: none;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            .btn-track:hover {
                background: #a93226;
                color: #fff;
            }

            .login-footer {
                text-align: center;
                margin-top: 24px;
                font-size: 12px;
                color: #999;
            }
            .login-footer a {
                color: #c0392b;
                text-decoration: none;
            }

            .login-error {
                background: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
                padding: 10px 14px;
                border-radius: 4px;
                margin-bottom: 14px;
                font-size: 13px;
            }

            @media (max-width: 768px) {
                .login-container { flex-direction: column; }
                .login-left { display: none; }
                .login-right { max-width: 100%; padding: 30px 24px; }
            }
        </style>
    </head>
    <body>
        <div class="login-wrapper">
            <div class="login-container">
                <!-- Left - Illustration -->
                <div class="login-left">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 400">
                        <!-- Desk -->
                        <rect x="120" y="260" width="260" height="10" rx="3" fill="#2c3e50"/>
                        <!-- Desk legs -->
                        <rect x="140" y="270" width="8" height="80" fill="#34495e"/>
                        <rect x="352" y="270" width="8" height="80" fill="#34495e"/>
                        <!-- Monitor -->
                        <rect x="180" y="160" width="160" height="100" rx="6" fill="#2c3e50"/>
                        <rect x="186" y="166" width="148" height="82" rx="2" fill="#ecf0f1"/>
                        <!-- Screen content lines -->
                        <rect x="196" y="180" width="80" height="4" rx="2" fill="#3498db"/>
                        <rect x="196" y="190" width="120" height="3" rx="1" fill="#bdc3c7"/>
                        <rect x="196" y="198" width="100" height="3" rx="1" fill="#bdc3c7"/>
                        <rect x="196" y="206" width="110" height="3" rx="1" fill="#bdc3c7"/>
                        <rect x="196" y="218" width="60" height="12" rx="3" fill="#c0392b"/>
                        <!-- Monitor stand -->
                        <rect x="248" y="248" width="24" height="14" fill="#2c3e50"/>
                        <rect x="238" y="258" width="44" height="6" rx="2" fill="#34495e"/>
                        <!-- Keyboard -->
                        <rect x="200" y="264" width="100" height="8" rx="2" fill="#7f8c8d"/>
                        <!-- Chair -->
                        <ellipse cx="100" cy="340" rx="30" ry="8" fill="#2980b9"/>
                        <rect x="92" y="270" width="16" height="70" fill="#34495e"/>
                        <rect x="70" y="220" width="60" height="55" rx="8" fill="#2980b9"/>
                        <!-- Person (sitting) -->
                        <circle cx="100" cy="190" r="22" fill="#f0c8a0"/>
                        <!-- Hair -->
                        <ellipse cx="100" cy="178" rx="24" ry="16" fill="#2c3e50"/>
                        <ellipse cx="82" cy="192" rx="6" ry="12" fill="#2c3e50"/>
                        <!-- Body -->
                        <rect x="82" y="210" width="36" height="50" rx="4" fill="#3498db"/>
                        <!-- Arms -->
                        <rect x="118" y="220" width="50" height="8" rx="4" fill="#f0c8a0" transform="rotate(-10 118 220)"/>
                        <rect x="60" y="230" width="28" height="7" rx="3" fill="#f0c8a0"/>
                        <!-- Plant -->
                        <rect x="390" y="230" width="8" height="32" fill="#27ae60"/>
                        <circle cx="394" cy="222" r="16" fill="#27ae60"/>
                        <circle cx="384" cy="228" r="10" fill="#2ecc71"/>
                        <circle cx="404" cy="228" r="10" fill="#2ecc71"/>
                        <rect x="384" y="258" width="20" height="8" rx="3" fill="#8b6914"/>
                        <!-- Documents on desk -->
                        <rect x="310" y="248" width="30" height="14" rx="1" fill="#fff" stroke="#ddd"/>
                        <rect x="314" y="252" width="16" height="2" fill="#c0392b"/>
                        <rect x="314" y="256" width="20" height="1.5" fill="#ccc"/>
                    </svg>
                </div>

                <!-- Right - Login Form -->
                <div class="login-right">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
