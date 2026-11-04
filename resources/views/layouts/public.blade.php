<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bapenda Provinsi Riau') | Badan Pendapatan Daerah</title>
    <meta name="description" content="@yield('meta_description', 'Website resmi Badan Pendapatan Daerah Provinsi Riau. Mewujudkan tata kelola pendapatan daerah yang optimal, transparan, dan akuntabel.')">

    <!-- Favicon Logo -->
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon.png') }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bapenda Public CSS -->
    <link rel="stylesheet" href="{{ asset('css/bapenda-public.css') }}?v={{ filemtime(public_path('css/bapenda-public.css')) }}">

    @stack('styles')
</head>

<body>

    <!-- NAVBAR -->
    @include('partials.public_navbar')

    <!-- CONTENT -->
    @yield('content')

    <!-- FOOTER -->
    @include('partials.public_footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function () {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Close mobile navbar after clicking menu (except dropdown toggles)
        document.querySelectorAll('.navbar-nav .nav-link:not(.dropdown-toggle), .navbar-nav .dropdown-item:not(.dropdown-toggle)').forEach(link => {
            link.addEventListener('click', () => {
                const navbar = document.querySelector('.navbar-collapse');
                if (navbar.classList.contains('show')) {
                    bootstrap.Collapse.getOrCreateInstance(navbar).hide();
                }
            });
        });

        // Toggle nested submenu (Bidang Kerja) on mobile
        document.querySelectorAll('.navbar .dropdown-submenu > .dropdown-toggle').forEach(toggle => {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (window.innerWidth >= 992) return;

                const submenu = this.parentElement;
                document.querySelectorAll('.navbar .dropdown-submenu.open').forEach(item => {
                    if (item !== submenu) item.classList.remove('open');
                });
                submenu.classList.toggle('open');
            });
        });

        // Reset nested submenu when navbar is closed
        document.querySelector('.navbar-collapse')?.addEventListener('hidden.bs.collapse', () => {
            document.querySelectorAll('.navbar .dropdown-submenu.open').forEach(item => item.classList.remove('open'));
        });
    </script>

    @stack('scripts')
</body>

</html>

