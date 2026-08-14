<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Mubee</title>
    
    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-color: #08080a;
            --primary: #ff2a54;
            --primary-gradient: linear-gradient(135deg, #ff2a54 0%, #ff6b3d 100%);
            --text-color: #f3f4f6;
            --text-muted: #9ca3af;
            --glass-border: rgba(255, 255, 255, 0.08);
            --font-family: 'Plus Jakarta Sans', sans-serif;
            --heading-family: 'Outfit', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--font-family);
        }

        body {
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(255, 42, 84, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(255, 107, 61, 0.08) 0%, transparent 40%);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Logo Header */
        header {
            padding: 30px 6%;
        }

        .logo {
            font-family: var(--heading-family);
            font-weight: 800;
            font-size: 32px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        /* Centered login form wrapper */
        main {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            flex: 1;
        }

        .login-card {
            background: rgba(20, 20, 25, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            width: 100%;
            max-width: 450px;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.6);
            animation: fadeInUp 0.6s ease-out;
        }

        .login-title {
            font-family: var(--heading-family);
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 24px;
        }

        /* Demo credentials box */
        .demo-badge {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 24px;
            font-size: 13px;
        }

        .demo-badge h5 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .demo-badge p {
            color: var(--text-muted);
            font-family: monospace;
            line-height: 1.4;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            color: var(--text-muted);
            font-size: 16px;
        }

        .form-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            padding: 12px 16px 12px 45px;
            color: white;
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 10px rgba(255, 42, 84, 0.2);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            margin-bottom: 25px;
            color: var(--text-muted);
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-me input {
            accent-color: var(--primary);
        }

        .forgot-link {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: var(--primary);
        }

        .btn-submit {
            width: 100%;
            background: var(--primary-gradient);
            color: white;
            padding: 14px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(255, 42, 84, 0.4);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 42, 84, 0.6);
        }

        .register-prompt {
            margin-top: 24px;
            text-align: center;
            font-size: 14px;
            color: var(--text-muted);
        }

        .register-link {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
            margin-left: 5px;
            transition: all 0.2s ease;
        }

        .register-link:hover {
            text-decoration: underline;
        }

        /* Error States */
        .error-message {
            color: var(--primary);
            font-size: 12px;
            margin-top: 6px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .alert-status {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        footer {
            padding: 30px;
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
            border-top: 1px solid rgba(255,255,255,0.02);
            background: rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header>
        <a href="/" class="logo">
            <i class="fa-solid fa-play"></i> Mubee
        </a>
    </header>

    <!-- Main Container -->
    <main>
        <div class="login-card">
            <h2 class="login-title">Masuk ke Akun</h2>

            @if (session('success'))
                <div class="alert-status">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Input -->
                <div class="form-group">
                    <label class="form-label" for="email">ALAMAT EMAIL</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-envelope"></i>
                        <input class="form-input" type="email" id="email" name="email" value="{{ old('email') }}" placeholder="masukkan email Anda" required autofocus>
                    </div>
                    @error('email')
                        <div class="error-message">
                            <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <label class="form-label" for="password">PASSWORD</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock"></i>
                        <input class="form-input" type="password" id="password" name="password" placeholder="masukkan password Anda" required>
                    </div>
                    @error('password')
                        <div class="error-message">
                            <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Form Options -->
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Ingat Saya</span>
                    </label>
                    <a class="forgot-link" href="{{ route('password.request') }}">Lupa Password?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-submit">
                    Masuk ke Akun <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </button>
            </form>

            <!-- Register Prompt -->
            <div class="register-prompt">
                Belum punya akun? <a href="{{ route('register') }}" class="register-link">Buat Akun</a>
            </div>
        </div>
    </main>

    <footer>
        <p>© 2026 Mubee. Semua hak dilindungi undang-undang.</p>
    </footer>

</body>
</html>
