<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - GKKD Jakarta</title>
    <link rel="icon" href="{{ asset('assets/img/gkkd-yellow.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome-all.min.css') }}">
    <style>
        :root {
            --primary: #1e3a5f;
            --primary-light: #2a5298;
            --accent: #e8a838;
            --surface: #f7f8fb;
            --border: #e5e7eb;
            --text: #172033;
            --muted: #6b7280;
            --danger: #b91c1c;
        }
        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            background: var(--surface);
            color: var(--text);
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            padding: 24px;
        }
        .login-shell {
            width: min(100%, 420px);
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 16px 45px rgba(17, 24, 39, 0.08);
            padding: 30px;
        }
        .login-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 26px;
        }
        .login-brand img {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }
        .login-brand h1 {
            font-size: 1.18rem;
            line-height: 1.2;
            font-weight: 800;
            margin: 0;
        }
        .login-brand span {
            display: block;
            margin-top: 3px;
            font-size: 0.78rem;
            color: var(--muted);
            font-weight: 500;
        }
        .form-label {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--text);
        }
        .form-control {
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: 11px 13px;
            font-size: 0.9rem;
        }
        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.11);
        }
        .btn-login {
            width: 100%;
            border: 0;
            border-radius: 8px;
            padding: 11px 16px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #fff;
            font-weight: 800;
        }
        .btn-login:hover {
            color: #fff;
            filter: brightness(1.05);
        }
        .alert-danger {
            background: #fef2f2;
            color: var(--danger);
            border: 1px solid #fecaca;
            border-radius: 8px;
            font-size: 0.84rem;
        }
    </style>
</head>
<body>
    <main class="login-shell">
        <div class="login-brand">
            <img src="{{ asset('assets/img/gkkd-yellow.png') }}" alt="GKKD Jakarta">
            <div>
                <h1>GKKD Jakarta</h1>
                <span>Sistem Manajemen Gereja</span>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" autocomplete="email" required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" autocomplete="current-password" required>
            </div>

            <div class="form-check mb-4">
                <input type="checkbox" name="remember" id="remember" class="form-check-input" value="1">
                <label class="form-check-label" for="remember">Ingat sesi login</label>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </button>
        </form>
    </main>
</body>
</html>
