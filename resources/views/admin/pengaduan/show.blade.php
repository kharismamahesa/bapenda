@extends('layouts.admin')

@section('title', 'Detail Pengaduan')
@section('page_title', 'Detail Pengaduan')
@section('page_subtitle', 'Kode pengaduan: ' . $pengaduan->kode)

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.pengaduan.index') }}" class="btn btn-sm btn-outline-custom">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-custom p-4 mb-4">
        <div class="row g-3">
            <div class="col-md-6"><strong>Nama:</strong><br>{{ $pengaduan->nama }}</div>
            <div class="col-md-6"><strong>Email:</strong><br>{{ $pengaduan->email }}</div>
            <div class="col-md-6"><strong>Nomor HP:</strong><br>{{ $pengaduan->no_hp ?: '-' }}</div>
            <div class="col-md-6"><strong>Waktu masuk:</strong><br>{{ optional($pengaduan->created_datetime)->format('d M Y, H:i') }}</div>
            <div class="col-md-6"><strong>Kantor tujuan:</strong><br>{{ $kantorTujuan }}</div>
            <div class="col-12">
                <strong>Isi pengaduan:</strong>
                <div class="border rounded p-3 mt-2" style="white-space: pre-wrap">{{ $pengaduan->aduan }}</div>
            </div>
        </div>
    </div>

    @if ($pengaduan->balasan->isNotEmpty())
        <div class="table-custom p-4 mb-4">
            <h5 class="fw-bold mb-3">Riwayat Balasan</h5>
            @foreach ($pengaduan->balasan->sortBy('updated_datetime') as $balasan)
                <div class="border rounded p-3 mb-3">
                    <div class="small text-muted mb-2">
                        {{ optional($balasan->updated_datetime)->format('d M Y, H:i') }}
                        &middot; ID user pembalas: {{ $balasan->updated_by ?? '-' }}
                        @if ($balasan->user)
                            ({{ $balasan->user->name }})
                        @endif
                    </div>
                    <div style="white-space: pre-wrap">{{ $balasan->balasan }}</div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="table-custom p-4">
        <h5 class="fw-bold mb-3">Kirim Balasan melalui Email</h5>
        <p class="text-muted">Balasan akan dikirim ke {{ $pengaduan->email }}.</p>
        <form method="POST" action="{{ route('admin.pengaduan.reply', $pengaduan->id) }}">
            @csrf
            <div class="mb-3">
                <label for="balasan" class="form-label fw-semibold">Balasan</label>
                <textarea id="balasan" name="balasan" class="form-control @error('balasan') is-invalid @enderror" rows="6" required>{{ old('balasan') }}</textarea>
                @error('balasan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-green">
                <i class="bi bi-send"></i> Kirim Balasan
            </button>
        </form>
    </div>
@endsection
