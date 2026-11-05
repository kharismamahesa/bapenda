@extends('layouts.public')

@section('title', 'Regulasi')
@section('meta_description', 'Kumpulan dokumen peraturan dan kebijakan Badan Pendapatan Daerah Provinsi Riau.')

@section('content')

    <section class="page-hero">
        <div class="container">
            <h1>Regulasi & Kebijakan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('beranda') }}">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Regulasi</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <div class="row g-4">

                <div class="col-lg-8">
                    <div class="regulasi-toolbar">
                        <form action="{{ route('regulasi.index') }}" method="GET" class="regulasi-search">
                            @if($activeKategori)
                                <input type="hidden" name="kategori" value="{{ $activeKategori->id_regulasi_kategori }}">
                            @endif
                            <div class="bapenda-input-group">
                                <i class="bi bi-search input-icon"></i>
                                <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Cari nama atau keterangan regulasi...">
                            </div>
                            <button type="submit" class="btn-cta">
                                Cari
                            </button>
                        </form>

                        <p class="text-muted small mb-0 mt-3">
                            Menampilkan {{ $regulasi->total() }} dokumen regulasi
                            @if($activeKategori)
                                pada kategori <strong>{{ $activeKategori->regulasi_kategori }}</strong>
                            @endif
                            @if($search !== '')
                                untuk pencarian <strong>"{{ $search }}"</strong>
                            @endif
                        </p>
                    </div>

                    @forelse($regulasi as $item)
                        <div class="regulasi-card">
                            <div class="regulasi-icon">
                                <i class="bi bi-file-earmark-text"></i>
                                @if($item->file_extension)
                                    <span class="regulasi-ext">{{ strtoupper($item->file_extension) }}</span>
                                @endif
                            </div>

                            <div class="regulasi-body">
                                <div class="regulasi-meta">
                                    <span class="regulasi-category">
                                        {{ $item->kategori ? $item->kategori->regulasi_kategori : 'Regulasi' }}
                                    </span>
                                    @if($item->created_datetime)
                                        <span class="regulasi-date">
                                            <i class="bi bi-calendar3"></i>
                                            {{ date('d M Y', strtotime($item->created_datetime)) }}
                                        </span>
                                    @endif
                                </div>

                                <h5 class="regulasi-title">{{ $item->regulasi }}</h5>

                                @if($item->keterangan)
                                    <p class="regulasi-desc">
                                        {{ \Illuminate\Support\Str::limit($item->keterangan, 220) }}
                                    </p>
                                @endif

                                <div class="regulasi-actions">
                                    @if($item->file)
                                        <a href="{{ route('regulasi.download', $item->id_regulasi) }}" class="btn-regulasi-download">
                                            <i class="bi bi-download"></i>
                                            Unduh Dokumen
                                        </a>
                                    @else
                                        <span class="text-muted small">
                                            <i class="bi bi-info-circle"></i>
                                            File belum tersedia
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-folder2-open fs-1 d-block mb-2"></i>
                            Belum ada regulasi yang tersedia.
                        </div>
                    @endforelse

                    <div class="d-flex justify-content-center mt-4">
                        {{ $regulasi->links() }}
                    </div>
                </div>

                <div class="col-lg-4">
                    <aside class="page-sidebar">
                        <div class="sidebar-nav">
                            <h5 class="sidebar-nav-title">Kategori Regulasi</h5>

                            <a href="{{ route('regulasi.index', $search !== '' ? ['q' => $search] : []) }}" class="{{ ! $activeKategori ? 'active' : '' }}">
                                <i class="bi bi-grid"></i>
                                Semua Regulasi
                                <span class="regulasi-count">{{ $kategoriList->sum('regulasi_count') }}</span>
                            </a>

                            @foreach($kategoriList as $kategori)
                                <a href="{{ route('regulasi.index', array_filter(['kategori' => $kategori->id_regulasi_kategori, 'q' => $search !== '' ? $search : null])) }}"
                                    class="{{ $activeKategori && $activeKategori->id_regulasi_kategori === $kategori->id_regulasi_kategori ? 'active' : '' }}">
                                    <i class="bi bi-folder2"></i>
                                    {{ $kategori->regulasi_kategori }}
                                    <span class="regulasi-count">{{ $kategori->regulasi_count }}</span>
                                </a>
                            @endforeach
                        </div>

                        <div class="regulasi-info-box mt-4">
                            <i class="bi bi-info-circle"></i>
                            <div>
                                <h6>Informasi</h6>
                                <p>Dokumen regulasi disediakan dalam format file digital dan dapat diunduh secara gratis.</p>
                            </div>
                        </div>
                    </aside>
                </div>

            </div>
        </div>
    </section>

@endsection
