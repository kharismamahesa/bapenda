@extends('layouts.admin')

@section('title', $video->exists ? 'Edit Video' : 'Tambah Video')
@section('page_title', $video->exists ? 'Edit Video' : 'Tambah Video')
@section('page_subtitle', 'Masukkan link YouTube biasa; sistem akan mengubahnya menjadi link embed yang aman.')

@section('content')
    <div class="table-custom p-4">
        <form method="POST"
            action="{{ $video->exists ? route('admin.video.update', $video) : route('admin.video.store') }}">
            @csrf
            @if ($video->exists)
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="judul" class="form-label fw-semibold">Judul video</label>
                <input type="text" id="judul" name="judul"
                    class="form-control @error('judul') is-invalid @enderror"
                    value="{{ old('judul', $video->judul) }}" maxlength="255" required>
                @error('judul')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="video" class="form-label fw-semibold">Link YouTube</label>
                <input type="url" id="video" name="video"
                    class="form-control @error('video') is-invalid @enderror"
                    value="{{ old('video', $video->video) }}"
                    placeholder="https://www.youtube.com/watch?v=mBLD_2zvdWU" maxlength="2048" required>
                <div class="form-text">Diterima: link YouTube, youtu.be, Shorts, atau link embed. Hanya YouTube yang diizinkan.</div>
                @error('video')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label fw-semibold">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="5"
                    class="form-control @error('keterangan') is-invalid @enderror"
                    maxlength="5000">{{ old('keterangan', $video->keterangan) }}</textarea>
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-green">Simpan Video</button>
                <a href="{{ route('admin.video.index') }}" class="btn btn-outline-custom">Batal</a>
            </div>
        </form>
    </div>
@endsection
