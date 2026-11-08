@extends('layouts.admin')

@section('title', 'Slider Hero')
@section('page_title', 'Kelola Slider Hero')
@section('page_subtitle', 'Atur gambar dan tulisan pada hero halaman utama.')

@section('page_actions')
    <a href="{{ route('admin.slider.create') }}" class="btn btn-green">
        <i class="bi bi-plus-lg"></i> Tambah Slider
    </a>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-custom mb-4">
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Urutan</th>
                        <th>Gambar</th>
                        <th>Judul</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sliders as $slider)
                        <tr>
                            <td>{{ $slider->urutan }}</td>
                            <td>
                                <img src="{{ asset('uploads/slider/'.$slider->gambar) }}"
                                    alt="{{ $slider->judul_putih }} {{ $slider->judul_kuning }}"
                                    class="rounded" style="width: 120px; height: 68px; object-fit: cover;">
                            </td>
                            <td>
                                <div>{{ $slider->judul_putih }} <strong>{{ $slider->judul_kuning }}</strong></div>
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($slider->subjudul, 100) }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $slider->status ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' }}">
                                    {{ $slider->status ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="table-action-buttons">
                                    <a href="{{ route('admin.slider.edit', $slider) }}"
                                        class="btn btn-primary btn-icon-action"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Edit slider" aria-label="Edit slider">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.slider.destroy', $slider) }}"
                                        onsubmit="return confirm('Hapus slider ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-icon-action"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Hapus slider" aria-label="Hapus slider">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada slider. Tambahkan slider agar hero dapat dikelola dari dashboard.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">{{ $sliders->links() }}</div>
    </div>
@endsection
