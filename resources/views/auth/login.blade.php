<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name', 'Bapenda Riau') }}</title>

    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Bapenda Login Styling (Green Theme) -->
    <link rel="stylesheet" href="{{ asset('css/bapenda-login.css') }}">
</head>
<body>

    <div class="login-wrapper">
        <!-- Sisi Kiri: Visual & Hero Bapenda -->
        <div class="side-visual">
            <div class="visual-content">
                <div class="visual-badge">
                    <i class="bi bi-shield-check"></i>
                    <span>Portal Administrator Resmi</span>
                </div>

                <h1>BAPENDA RIAU</h1>
                <p>
                    Sistem Manajemen Pendapatan Asli Daerah, Monitoring Penerimaan Pajak & Retribusi, serta Pengelolaan Informasi Publik Pemerintah Provinsi Riau.
                </p>

                <div class="visual-highlights">
                    <div class="visual-item">
                        <i class="bi bi-check2-circle"></i>
                        <span>Monitoring Realisasi Pajak & Target PAD</span>
                    </div>
                    <div class="visual-item">
                        <i class="bi bi-check2-circle"></i>
                        <span>Pengelolaan Berita, Artikel, dan Pengumuman Resmi</span>
                    </div>
                    <div class="visual-item">
                        <i class="bi bi-check2-circle"></i>
                        <span>Manajemen Hak Akses Administrator & Operator</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sisi Kanan: Form Login -->
        <div class="side-form">
            <div class="login-container">
                <!-- Brand Icon & Header -->
                <div class="login-header">
                    <div class="login-brand-logo">
                        <i class="bi bi-building"></i>
                    </div>
                    <h3>Selamat Datang</h3>
                    <p>Silakan masukkan akun administrator Anda untuk mengakses sistem.</p>
                </div>

                @if ($errors->any())
                    <div class="alert-error">
                        <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p class="mb-0">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success py-2 px-3 mb-3" style="font-size: 0.85rem; border-radius: 8px;">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                    @csrf

                    <!-- Username / Email -->
                    <div class="mb-3">
                        <label class="form-label" for="username">Username atau Email</label>
                        <div class="input-group-custom">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text"
                                   id="username"
                                   name="username"
                                   value="{{ old('username') }}"
                                   class="form-control-custom"
                                   placeholder="Masukkan username atau email"
                                   required
                                   autofocus
                                   autocomplete="username">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group-custom">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="form-control-custom"
                                   placeholder="••••••••"
                                   required
                                   autocomplete="current-password">
                            <button type="button" class="toggle-password-btn" id="togglePasswordBtn" aria-label="Lihat password">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember me -->
                    <div class="d-flex align-items-center justify-content-between mt-3 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" style="accent-color: var(--primary-green);">
                            <label class="form-check-label text-muted" for="remember" style="font-size: 0.85rem; cursor: pointer;">
                                Ingat Saya
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-login" id="btnSubmit">
                        <span>Masuk Sekarang</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                <div class="footer-text">
                    <p class="mb-1 text-muted">
                        Kredensial Default: <strong class="text-dark">admin</strong> / <strong class="text-dark">password123</strong>
                    </p>
                    <small>&copy; {{ date('Y') }} Badan Pendapatan Daerah Provinsi Riau.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            if (toggleBtn && passwordInput && toggleIcon) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
                });
            }
        });
    </script>
</body>
</html>
