@extends('layouts.admin')

@section('title', 'Kelola Video')
@section('page_title', 'Kelola Video')
@section('page_subtitle', 'Video yang aktif di halaman utama dan halaman informasi video.')

@section('page_actions')
    <a href="{{ route('admin.video.create') }}" class="btn btn-green">
        <i class="bi bi-plus-lg"></i> Tambah Video
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
                        <th style="width: 70px;">ID</th>
                        <th>Judul</th>
                        <th>Link YouTube tersimpan</th>
                        <th>Keterangan</th>
                        <th class="text-center" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($videos as $video)
                        <tr>
                            <td>{{ $video->id }}</td>
                            <td class="fw-semibold">{{ $video->judul }}</td>
                            <td>
                                @if ($embedUrl = \App\Models\Video::normalizeYoutubeUrl((string) $video->video))
                                    <a href="{{ $embedUrl }}" target="_blank" rel="noopener noreferrer">{{ $embedUrl }}</a>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis">Link tidak valid</span>
                                @endif
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit($video->keterangan, 100) }}</td>
                            <td class="text-center">
                                <div class="table-action-buttons">
                                    <a href="{{ route('admin.video.edit', $video) }}"
                                        class="btn btn-primary btn-icon-action"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Edit video" aria-label="Edit video">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.video.destroy', $video) }}"
                                        onsubmit="return confirm('Hapus video ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-icon-action"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Hapus video" aria-label="Hapus video">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada video.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">{{ $videos->links() }}</div>
    </div>
@endsection
