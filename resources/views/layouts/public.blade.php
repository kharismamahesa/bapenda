<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bapenda Provinsi Riau') | Badan Pendapatan Daerah</title>
    <meta name="description" content="@yield('meta_description', 'Website resmi Badan Pendapatan Daerah Provinsi Riau. Mewujudkan tata kelola pendapatan daerah yang optimal, transparan, dan akuntabel.')">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bapenda Public CSS -->
    <link rel="stylesheet" href="{{ asset('css/bapenda-public.css') }}">

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

        // Close mobile navbar after clicking menu
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                const navbar = document.querySelector('.navbar-collapse');
                if (navbar.classList.contains('show')) {
                    bootstrap.Collapse.getOrCreateInstance(navbar).hide();
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>

