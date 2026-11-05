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

                        <form>
                            <div class="mb-3">
                                <label for="id_uptup" class="form-label fw-semibold">Kantor Pelayanan</label>
                                <select id="id_uptup" name="id_uptup" class="form-select" required>
                                    <option value="">Pilih kantor pelayanan</option>
                                    @foreach ($kantorPelayanan as $kantor)
                                        @php
                                            $status = trim((string) $kantor->Status);
                                            $namaWilayah = trim((string) $kantor->NamaWilayah);
                                            $label = $status !== '' ? $status.' '.$namaWilayah : $namaWilayah;
                                        @endphp
                                        <option value="{{ $kantor->KodeWilayah }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="nama" class="form-label fw-semibold">Nama</label>
                                <input type="text" id="nama" name="nama" class="form-control" placeholder="Masukkan nama Anda">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="nama@email.com">
                            </div>

                            <div class="mb-3">
                                <label for="no_hp" class="form-label fw-semibold">Nomor HP</label>
                                <input type="tel" id="no_hp" name="no_hp" class="form-control" placeholder="Contoh: 081234567890">
                            </div>

                            <div class="mb-4">
                                <label for="aduan" class="form-label fw-semibold">Aduan</label>
                                <textarea id="aduan" name="aduan" class="form-control" rows="5" placeholder="Tuliskan pengaduan Anda"></textarea>
                            </div>

                            <button type="button" class="btn-cta border-0">
                                Kirim Pengaduan
                                <i class="bi bi-send"></i>
                            </button>
                        </form>

                        <p class="text-muted small mt-3 mb-0">
                            Formulir ini masih dalam tahap tampilan. Pengiriman pengaduan belum tersedia.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
