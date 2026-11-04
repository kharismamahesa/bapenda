@extends('layouts.public')

@section('title', $berita->judul)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($berita->isi), 150))

@section('content')

    <section class="page-hero">
        <div class="container">
            <h1>{{ $berita->judul }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('beranda') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('berita.index') }}">Berita</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $berita->judul }}</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <div class="row g-4">

                <div class="col-lg-8">
                    <div class="bg-white border rounded-4 p-4 p-lg-5">
                        <div class="news-date mb-3">
                            <i class="bi bi-calendar3"></i>
                            {{ $berita->kategori ? strtoupper($berita->kategori->berita_kategori) : 'INFORMASI BAPENDA' }}
                            &middot; {{ $berita->tanggal_berita ? date('d M Y, H:i', strtotime($berita->tanggal_berita)) : '' }}
                        </div>

                        @if($berita->gambar)
                            <img src="{{ $berita->gambar_url }}" alt="{{ $berita->judul }}" class="img-fluid rounded-4 mb-4" loading="lazy" decoding="async"
                                onerror="this.onerror=null;this.src='{{ asset('images/no-image.svg') }}';">
                        @endif

                        <div class="content-body">
                            {!! $berita->isi !!}
                        </div>

                        <div class="border-top mt-4 pt-4">
                            <a href="{{ route('berita.index') }}" class="service-link">
                                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Berita
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <aside class="page-sidebar">
                        <div class="sidebar-nav">
                            <h5 class="sidebar-nav-title">Berita Lainnya</h5>
                            @forelse($beritaTerkait as $item)
                                <a href="{{ route('berita.show', $item->url ?: $item->id_berita) }}">
                                    <i class="bi bi-newspaper"></i>
                                    {{ \Illuminate\Support\Str::limit($item->judul, 55) }}
                                </a>
                            @empty
                                <span class="d-block p-3 text-muted small">Belum ada berita lainnya.</span>
                            @endforelse
                        </div>
                    </aside>
                </div>

            </div>
        </div>
    </section>

@endsection
