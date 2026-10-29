<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name', 'Bapenda') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="login-card">
        <!-- Logo -->
        <div class="login-logo">
            <i data-lucide="building-2" class="w-8 h-8 text-white"></i>
        </div>

        <h1 class="login-title">BAPENDA</h1>
        <p class="login-subtitle">Masuk ke panel administrasi sistem</p>

        @if ($errors->any())
            <div class="alert-error">
                <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        @if (session('status'))
            <div class="mb-4 text-sm text-green-700 bg-green-100 p-3 rounded-xl">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <!-- Username -->
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <div class="form-input-icon">
                    <i data-lucide="user" class="icon w-4 h-4"></i>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" 
                           class="form-input" placeholder="Masukkan username" required autofocus autocomplete="username">
                </div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="form-input-icon">
                    <i data-lucide="lock" class="icon w-4 h-4"></i>
                    <input type="password" id="password" name="password" 
                           class="form-input" placeholder="••••••••" required autocomplete="current-password">
                </div>
            </div>

            <!-- Remember me -->
            <div class="form-checkbox-group">
                <label class="form-checkbox-label">
                    <input type="checkbox" name="remember" id="remember">
                    <span>Ingat Saya</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-login">
                <span>Masuk Sekarang</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
                Default: <strong>admin</strong> / <strong>password</strong>
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>

