<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Bapenda Riau') }}</title>
    <meta name="description" content="@yield('meta_description', 'Bapenda Admin Dashboard')">

    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- Summernote Lite -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">

    <!-- Bapenda Dashboard CSS (Green Theme) -->
    <link rel="stylesheet" href="{{ asset('css/bapenda-dashboard.css') }}">

    @stack('styles')
</head>
<body>

    <div id="wrapper">
        <!-- Sidebar Navigation -->
        @include('partials.navbar')

        <!-- Mobile Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <!-- Main Content Area -->
        <main id="content">
            <!-- Header Nav / Title -->
            @include('partials.header')

            <!-- Dynamic Page Content -->
            @yield('content')

            <!-- Footer -->
            @include('partials.footer')
        </main>
    </div>

    <!-- Core Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.sanitizeSummernoteHtml = function (html) {
            const template = document.createElement('template');
            template.innerHTML = html;

            const allowedTags = new Set([
                'A', 'B', 'BLOCKQUOTE', 'BR', 'CODE', 'DEL', 'DIV', 'EM', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6',
                'HR', 'I', 'LI', 'OL', 'P', 'PRE', 'S', 'SPAN', 'STRONG', 'SUB', 'SUP', 'TABLE', 'TBODY', 'TD',
                'TFOOT', 'TH', 'THEAD', 'TR', 'U', 'UL'
            ]);
            const removedTags = new Set(['EMBED', 'IFRAME', 'MATH', 'OBJECT', 'SCRIPT', 'STYLE', 'SVG']);

            function clean(parent) {
                Array.from(parent.childNodes).forEach(node => {
                    if (node.nodeType === Node.TEXT_NODE) return;
                    if (node.nodeType !== Node.ELEMENT_NODE || removedTags.has(node.tagName)) {
                        node.remove();
                        return;
                    }

                    clean(node);
                    if (!allowedTags.has(node.tagName)) {
                        node.replaceWith(...node.childNodes);
                        return;
                    }

                    const href = node.tagName === 'A' ? node.getAttribute('href') : null;
                    const title = node.getAttribute('title');
                    Array.from(node.attributes).forEach(attribute => node.removeAttribute(attribute.name));

                    if (href && node.tagName === 'A') {
                        const scheme = href.match(/^([a-z][a-z0-9+.-]*):/i);
                        if ((!scheme || ['http', 'https', 'mailto'].includes(scheme[1].toLowerCase())) &&
                            !/^(?:\/\/|\\\\)/.test(href)) {
                            node.setAttribute('href', href);
                        }
                    }
                    if (title) node.setAttribute('title', title);
                });
            }

            clean(template.content);
            return template.innerHTML;
        };

        window.handleSummernotePaste = function (event) {
            const clipboard = (event.originalEvent || event).clipboardData;
            if (!clipboard) return;

            const html = clipboard.getData('text/html');
            const text = clipboard.getData('text/plain');
            if (!html && !text) return;

            event.preventDefault();
            const pastedMarkup = html || (/<\/?[a-z][\s\S]*>/i.test(text) ? text : null);
            const content = pastedMarkup
                ? window.sanitizeSummernoteHtml(pastedMarkup)
                : text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\r\n|\r|\n/g, '<br>');

            document.execCommand('insertHTML', false, content);
        };
    </script>
    <script>
        new bootstrap.Tooltip(document.body, {
            selector: '[data-bs-toggle="tooltip"]',
            container: 'body'
        });
    </script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>

    @stack('scripts')
</body>
</html>
