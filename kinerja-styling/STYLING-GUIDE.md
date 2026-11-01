# 🎨 Kinerja App - Styling Guide

Panduan styling khusus dari aplikasi Monitoring Kinerja DKP Riau (`/kinerja`).

---

## Dependensi CDN

Tambahkan di `<head>`:

```html
<!-- Bootstrap 5.3 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons 1.11 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<!-- Google Fonts: Poppins -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- DataTables (untuk halaman dashboard) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
```

Tambahkan di akhir `<body>`:

```html
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
```

---

## File CSS

| File | Keterangan |
|---|---|
| `kinerja-login.css` | Halaman login split-layout (visual kiri + form kanan) |
| `kinerja-dashboard.css` | Layout dashboard: sidebar, cards, tables, buttons, DataTables |

---

## Color Palette / Design Tokens

| Variable | Warna | Kegunaan |
|---|---|---|
| `--primary-teal` | `#49c5b6` | Primary color, buttons, active states |
| `--primary-teal-dark` | `#3ba396` | Hover states |
| `--primary-teal-light` | `#e0f2f1` | Stat icon backgrounds |
| `--sidebar-bg` / `--dark-gray` | `#1d1d1b` | Sidebar, table header |
| `--body-bg` | `#f5f6f8` | Page background |
| `--text-dark` | `#374151` | Body text |
| `--text-muted` | `#6b7280` | Secondary text |

---

## Cara Pakai

### Login Page
```html
<link rel="stylesheet" href="kinerja-login.css">
```

### Dashboard Page
```html
<link rel="stylesheet" href="kinerja-dashboard.css">
```

> **Tips:** Sesuaikan CSS variables di `:root` jika ingin mengubah warna tema untuk aplikasi lain.

