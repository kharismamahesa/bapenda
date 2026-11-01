@extends('layouts.public')

@section('title', 'Berita')
@section('meta_description', 'Informasi terbaru seputar kegiatan dan kebijakan Badan Pendapatan Daerah Provinsi Riau.')

@section('content')

    <section class="page-hero">
        <div class="container">
            <h1>Berita & Kegiatan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('beranda') }}">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Berita</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="page-content">
        <div class="container">

            @forelse($berita as $item)
                <div class="berita-list-card">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <div class="berita-list-image" style="background-image:url('{{ $item->gambar_url }}')"></div>
                        </div>
                        <div class="col-md-8">
                            <div class="berita-list-body">
                                <div class="news-date">
                                    <i class="bi bi-calendar3"></i>
                                    {{ $item->kategori ? strtoupper($item->kategori->berita_kategori) : 'INFORMASI BAPENDA' }}
                                    &middot; {{ $item->tanggal_berita ? date('d M Y', strtotime($item->tanggal_berita)) : '' }}
                                </div>

                                <h5>
                                    <a href="{{ route('berita.show', $item->url ?: $item->id_berita) }}">{{ $item->judul }}</a>
                                </h5>

                                <p class="text-muted mb-0">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($item->isi), 200) }}
                                </p>

                                <a href="{{ route('berita.show', $item->url ?: $item->id_berita) }}" class="service-link d-inline-block mt-3">
                                    Baca Selengkapnya <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-newspaper fs-1 d-block mb-2"></i>
                    Belum ada berita yang dipublikasikan.
                </div>
            @endforelse

            <div class="d-flex justify-content-center mt-4">
                {{ $berita->links() }}
            </div>

        </div>
    </section>

@endsection
