@extends('layouts.public')

@section('title', $halaman->judul)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($halaman->isi ?? ''), 150))

@section('content')

    <section class="page-hero">
        <div class="container">
            <h1>{{ $halaman->judul }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('beranda') }}">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $halaman->judul }}</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <div class="row g-4">

                <div class="col-lg-8">
                    <div class="bg-white border rounded-4 p-4 p-lg-5">
                        <div class="content-body">
                            @if($halaman->gambar)
                                <img src="{{ asset('uploads/halaman/'.$halaman->gambar) }}" alt="{{ $halaman->judul }}" class="mb-4">
                            @endif

                            {!! $halaman->isi !!}
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <aside class="page-sidebar">
                        <div class="sidebar-nav">
                            <h5 class="sidebar-nav-title">Halaman Lainnya</h5>
                            @foreach($halamanList as $page)
                                <a href="{{ route('halaman.show', $page->slug) }}" class="{{ $page->id === $halaman->id ? 'active' : '' }}">
                                    <i class="bi bi-file-earmark-text"></i>
                                    {{ $page->judul }}
                                </a>
                            @endforeach
                        </div>
                    </aside>
                </div>

            </div>
        </div>
    </section>

@endsection
