@extends('layouts.admin')

@section('title', 'Pengaduan Masyarakat')
@section('page_title', 'Pengaduan Masyarakat')
@section('page_subtitle', 'Daftar pengaduan yang masuk ke kantor pelayanan.')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card card-custom p-4 h-100">
                <div class="stat-title">Total Pengaduan</div>
                <div class="stat-value">{{ number_format($totalPengaduan) }}</div>
                <div class="text-muted small mt-1">{{ $scopeLabel }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card card-custom p-4 h-100">
                <div class="stat-title">Sudah Dibalas</div>
                <div class="stat-value">{{ number_format($pengaduanSudahDibalas) }}</div>
                <div class="text-muted small mt-1">Pengaduan dengan balasan tercatat</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card card-custom p-4 h-100">
                <div class="stat-title">Belum Dibalas</div>
                <div class="stat-value">{{ number_format($pengaduanBelumDibalas) }}</div>
                <div class="text-muted small mt-1">Pengaduan yang masih menunggu balasan</div>
            </div>
        </div>
    </div>

    <div class="table-custom mb-4">
        <div class="p-3 border-bottom">
            <h6 class="fw-bold mb-0 text-dark">Daftar Pengaduan</h6>
        </div>
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Pengadu</th>
                        <th>Kantor Pelayanan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengaduan as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->kode }}</td>
                            <td>
                                <div>{{ $item->nama }}</div>
                                <small class="text-muted">{{ $item->email }}</small>
                            </td>
                            <td>
                                {{ (int) $item->id_uptup === \App\Models\Pengaduan::BAPENDA_ID
                                    ? 'BAPENDA PROVINSI RIAU'
                                    : ($kantorById[$item->id_uptup] ?? 'Kantor pelayanan') }}
                            </td>
                            <td>{{ optional($item->created_datetime)->format('d M Y, H:i') }}</td>
                            <td>
                                <span class="badge {{ $item->balasan_count > 0 ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' }}">
                                    {{ $item->balasan_count > 0 ? 'Dibalas' : [0 => 'Menunggu', 1 => 'Aktif', 2 => 'Publish', 3 => 'Ditangguhkan', 4 => 'Publis'][$item->status] ?? 'Status ' . $item->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('admin.pengaduan.show', $item->id) }}"
                                        class="btn btn-info btn-icon-action"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Lihat dan balas pengaduan" aria-label="Lihat dan balas pengaduan">
                                        <i class="bi bi-chat-left-text"></i>
                                    </a>
                                    @if (in_array(auth()->user()->role, ['admin', 'superadmin'], true))
                                        <form method="POST" action="{{ route('admin.pengaduan.destroy', $item->id) }}"
                                            onsubmit="return confirm('Hapus pengaduan {{ $item->kode }} beserta seluruh riwayat balasannya? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-icon-action"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Hapus pengaduan" aria-label="Hapus pengaduan">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada pengaduan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">{{ $pengaduan->links() }}</div>
    </div>
@endsection
