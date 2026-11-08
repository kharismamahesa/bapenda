@extends('layouts.admin')

@section('title', 'Tinjau Survei')
@section('page_title', 'Tinjau Survei yang Ditandai')
@section('page_subtitle', 'Periksa dugaan kiriman survei duplikat. Hanya superadmin yang dapat memulihkan atau menghapus data.')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.survei.index', ['start_date' => $startDate, 'end_date' => $endDate, 'id_uptup' => $selectedOffice]) }}"
            class="btn btn-sm btn-outline-custom">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Survei
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.survei.review') }}" class="card card-custom p-3 p-md-4 mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-3">
                <label for="start_date" class="form-label fw-semibold">Tanggal mulai</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate }}" required>
            </div>
            <div class="col-12 col-md-3">
                <label for="end_date" class="form-label fw-semibold">Tanggal akhir</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate }}" required>
            </div>
            <div class="col-12 col-md-4">
                <label for="id_uptup" class="form-label fw-semibold">Kantor Pelayanan (UPTUP)</label>
                <select id="id_uptup" name="id_uptup" class="form-select">
                    <option value="">Semua kantor</option>
                    <option value="10000" @selected($selectedOffice === '10000')>BAPENDA PROVINSI RIAU</option>
                    @foreach ($offices as $office)
                        <option value="{{ $office->KodeWilayah }}" @selected($selectedOffice === (string) $office->KodeWilayah)>
                            {{ $office->NamaUPTUP }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-green">Terapkan</button>
                <a href="{{ route('admin.survei.review') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h5 fw-bold mb-0">Data Perlu Ditinjau</h2>
        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
            {{ number_format($survei->total()) }} data
        </span>
    </div>

    @forelse ($survei as $item)
        @php
            $questions = [
                'SKM 1' => $item->tanya1,
                'SKM 2' => $item->tanya2,
                'SKM 3' => $item->tanya3,
                'SKM 4' => $item->tanya4,
                'SKM 5' => $item->tanya5,
                'SKM 6' => $item->tanya6,
                'SKM 7' => $item->tanya7,
                'SKM 8' => $item->tanya8,
                'SKM 9' => $item->tanya9,
                'SAK 1' => $item->tanya21,
                'SAK 2' => $item->tanya22,
                'SAK 3' => $item->tanya23,
                'SAK 4' => $item->tanya24,
                'SAK 5' => $item->tanya25,
            ];
        @endphp
        <article class="card card-custom p-3 p-md-4 mb-3">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                <div class="flex-grow-1">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="fw-bold">Survei #{{ $item->id_survei }}</span>
                        <span class="badge bg-warning-subtle text-warning-emphasis">Perlu ditinjau</span>
                    </div>
                    <div class="row g-2 text-muted small">
                        <div class="col-12 col-md-6"><strong class="text-dark">Waktu:</strong> {{ $item->created_date ?: '-' }}</div>
                        <div class="col-12 col-md-6"><strong class="text-dark">Ditandai:</strong> {{ $item->spam_flagged_at ?: '-' }}</div>
                        <div class="col-12 col-md-6"><strong class="text-dark">Kantor:</strong> {{ $item->nama_uptup }}</div>
                        <div class="col-12 col-md-6"><strong class="text-dark">Alasan:</strong> {{ $item->spam_reason ?: '-' }}</div>
                        <div class="col-12 col-md-6"><strong class="text-dark">Nama:</strong> {{ $item->nama ?: '-' }}</div>
                        <div class="col-12 col-md-6"><strong class="text-dark">Email:</strong> {{ $item->email ?: '-' }}</div>
                        <div class="col-12 col-md-6"><strong class="text-dark">Nomor HP:</strong> {{ $item->no_hp ?: '-' }}</div>
                        <div class="col-12 col-md-6"><strong class="text-dark">Jenis kelamin / usia:</strong> {{ $item->jenis_kelamin ?: '-' }} / {{ $item->usia ?: '-' }}</div>
                        <div class="col-12 col-md-6"><strong class="text-dark">Pendidikan / pekerjaan:</strong> {{ $item->pendidikan ?: '-' }} / {{ $item->pekerjaan ?: '-' }}</div>
                        <div class="col-12"><strong class="text-dark">Alamat:</strong> {{ $item->alamat ?: '-' }}</div>
                    </div>

                    <details class="mt-3">
                        <summary class="text-success fw-semibold">Lihat jawaban survei</summary>
                        <div class="row g-2 mt-1">
                            @foreach ($questions as $label => $answer)
                                <div class="col-6 col-md-3 col-lg-2">
                                    <span class="text-muted small">{{ $label }}:</span>
                                    <strong>{{ $answer ?? '-' }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </details>
                </div>

                <div class="d-flex flex-column flex-sm-row flex-lg-column gap-2 align-self-lg-start">
                    <form method="POST" action="{{ route('admin.survei.review.restore', $item->id_survei) }}"
                        onsubmit="return confirm('Pulihkan survei ini sebagai responden valid? Data akan masuk kembali ke hitungan dan nilai survei.')">
                        @csrf
                        <button type="submit" class="btn btn-outline-success w-100">
                            <i class="bi bi-check-circle me-1"></i> Pulihkan
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.survei.review.destroy', $item->id_survei) }}"
                        onsubmit="return confirm('Hapus survei ini secara permanen? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash3 me-1"></i> Hapus permanen
                        </button>
                    </form>
                </div>
            </div>
        </article>
    @empty
        <div class="card card-custom p-5 text-center text-muted">
            Tidak ada survei yang perlu ditinjau pada filter ini.
        </div>
    @endforelse

    <div class="mt-4">{{ $survei->links() }}</div>
@endsection
