@extends('layouts.public')

@section('title', 'Video')
@section('meta_description', 'Video informasi dan kegiatan Badan Pendapatan Daerah Provinsi Riau.')

@section('content')
    <section class="page-hero">
        <div class="container">
            <h1>Video</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('beranda') }}">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Video</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <div class="row g-4">
                @forelse ($videos as $video)
                    @if ($video->embed_url)
                        <div class="col-md-6 col-lg-4">
                            <article class="card h-100 border-0 shadow-sm video-card">
                                <div class="ratio ratio-16x9">
                                    <iframe src="{{ $video->embed_url }}" title="{{ $video->judul }}"
                                        loading="lazy" referrerpolicy="strict-origin-when-cross-origin"
                                        sandbox="allow-scripts allow-same-origin allow-presentation"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen></iframe>
                                </div>
                                <div class="card-body">
                                    <h2 class="h5 card-title">{{ $video->judul }}</h2>
                                    @if ($video->keterangan)
                                        <p class="card-text text-muted mb-0">{{ $video->keterangan }}</p>
                                    @endif
                                </div>
                            </article>
                        </div>
                    @endif
                @empty
                    <div class="col-12">
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-play-btn fs-1 d-block mb-2"></i>
                            Belum ada video yang tersedia.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $videos->links() }}
            </div>
        </div>
    </section>
@endsection
