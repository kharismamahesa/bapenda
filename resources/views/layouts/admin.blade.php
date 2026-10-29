<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Bapenda') }}</title>
    <meta name="description" content="@yield('meta_description', 'Bapenda Admin Dashboard')">

    <!-- Fonts & Core Icons/Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- jQuery, DataTables & SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Summernote Lite -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>

    <!-- Tailwind Theme Settings -->
    <style type="text/tailwindcss">
        @theme inline {
            --color-primary: var(--primary);
            --color-primary-hover: var(--primary-hover);
            --color-foreground: var(--foreground);
            --color-muted: var(--muted);
            --color-border: var(--border);
            --color-accent-lime: var(--accent-lime);
            --color-accent-teal: var(--accent-teal);
            --color-accent-peach: var(--accent-peach);
            --color-success: var(--success);
            --color-success-light: var(--success-light);
            --color-success-dark: var(--success-dark);
            --color-error: var(--error);
            --color-error-light: var(--error-light);
            --color-error-dark: var(--error-dark);
            --color-warning: var(--warning);
            --color-warning-light: var(--warning-light);
            --color-warning-dark: var(--warning-dark);
            --color-info: var(--info);
            --color-info-light: var(--info-light);
            --color-info-dark: var(--info-dark);
            --color-alert: var(--alert);
            --color-alert-light: var(--alert-light);
            --color-alert-dark: var(--alert-dark);
            --color-gray-50: var(--gray-50);
            --color-gray-100: var(--gray-100);
            --color-gray-200: var(--gray-200);
            --color-gray-500: var(--gray-500);
            --color-gray-600: var(--gray-600);
            --color-gray-700: var(--gray-700);
            --font-sans: var(--font-sans);
            --radius-card: 20px;
            --radius-button: 16px;
            --radius-icon: 22px;
        }
    </style>

    <!-- Custom CSS (Separated) -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    @stack('styles')
</head>
<body class="font-sans bg-white min-h-screen overflow-x-hidden">

    <!-- Mobile Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

    <div class="flex min-h-screen">
        <!-- Sidebar / Navbar Navigasi -->
        @include('partials.navbar')

        <!-- Main Content Area -->
        <main class="flex-1 lg:ml-64 p-4 md:p-5 bg-white min-h-screen flex flex-col justify-between overflow-x-hidden">
            <div>
                <!-- Mobile & Page Header -->
                @include('partials.header')

                <!-- Main Page Dynamic Content -->
                @yield('content')
            </div>

            <!-- Footer -->
            @include('partials.footer')
        </main>
    </div>

    <!-- Core Admin JS (Separated) -->
    <script src="{{ asset('js/admin.js') }}"></script>
    @stack('scripts')
</body>
</html>
