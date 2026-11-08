@extends('layouts.public')

@section('title', 'Pengaduan Masyarakat')
@section('meta_description', 'Formulir pengaduan masyarakat untuk layanan Bapenda Provinsi Riau.')

@section('content')
    <section class="page-hero">
        <div class="container">
            <h1>Pengaduan Masyarakat</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('beranda') }}">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pengaduan</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="bg-white border rounded-4 p-4 p-lg-5">
                        <div class="section-label">LAYANAN PUBLIK</div>
                        <h2 class="section-title">Sampaikan Pengaduan Anda</h2>
                        <p class="text-muted mb-4">
                            Lengkapi informasi berikut untuk menyampaikan pengaduan terkait layanan Bapenda.
                        </p>

                        @if (session('success'))
                            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('pengaduan.store') }}">
                            @csrf
                            <div aria-hidden="true" style="position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden">
                                <label for="website_url">Jangan isi kolom ini</label>
                                <input type="text" id="website_url" name="website_url" tabindex="-1" autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="id_uptup" class="form-label fw-semibold">Kantor Pelayanan</label>
                                <select id="id_uptup" name="id_uptup" class="form-select" data-searchable-select
                                    data-search-placeholder="Cari nama kantor pelayanan..." required>
                                    <option value="">Pilih kantor pelayanan</option>
                                    <option value="10000" @selected(old('id_uptup') == 10000)>BAPENDA PROVINSI RIAU</option>
                                    @foreach ($kantorPelayanan as $kantor)
                                        @php
                                            $status = trim((string) $kantor->Status);
                                            $namaWilayah = trim((string) $kantor->NamaUPTUP);
                                            $label = $status !== '' ? $status . ' ' . $namaWilayah : $namaWilayah;
                                        @endphp
                                        <option value="{{ $kantor->KodeWilayah }}" @selected(old('id_uptup') == $kantor->KodeWilayah)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="nama" class="form-label fw-semibold">Nama</label>
                                <input type="text" id="nama" name="nama" class="form-control"
                                    value="{{ old('nama') }}" placeholder="Masukkan nama Anda" maxlength="256" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="{{ old('email') }}" placeholder="nama@email.com" maxlength="256"
                                    inputmode="email" autocomplete="email" spellcheck="false" required>
                            </div>

                            <div class="mb-3">
                                <label for="no_hp" class="form-label fw-semibold">Nomor HP</label>
                                <input type="tel" id="no_hp" name="no_hp" class="form-control"
                                    value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890" maxlength="15">
                            </div>

                            <div class="mb-4">
                                <label for="aduan" class="form-label fw-semibold">Aduan</label>
                                <textarea id="aduan" name="aduan" class="form-control" rows="5" placeholder="Tuliskan pengaduan Anda" required>{{ old('aduan') }}</textarea>
                            </div>

                            <button type="submit" class="btn-cta border-0">
                                Kirim Pengaduan
                                <i class="bi bi-send"></i>
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/searchable-select.js') }}" defer></script>
@endpush
