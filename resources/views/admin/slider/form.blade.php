@extends('layouts.admin')

@section('title', $slider->exists ? 'Edit Slider' : 'Tambah Slider')
@section('page_title', $slider->exists ? 'Edit Slider Hero' : 'Tambah Slider Hero')
@section('page_subtitle', 'Gambar hero dan teks yang tampil pada halaman utama.')

@section('content')
    <div class="table-custom p-4">
        <form method="POST"
            action="{{ $slider->exists ? route('admin.slider.update', $slider) : route('admin.slider.store') }}"
            enctype="multipart/form-data">
            @csrf
            @if ($slider->exists)
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="gambar" class="form-label fw-semibold">Gambar hero
                    @unless ($slider->exists)<span class="text-danger">*</span>@endunless
                </label>
                <input type="file" id="gambar" name="gambar"
                    class="form-control @error('gambar') is-invalid @enderror"
                    accept="image/jpeg,image/png,image/webp" {{ $slider->exists ? '' : 'required' }}>
                <div class="form-text">JPG, PNG, atau WebP. Maksimal 5 MB.</div>
                @error('gambar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if ($slider->exists)
                    <img src="{{ asset('uploads/slider/'.$slider->gambar) }}" alt="Gambar slider saat ini"
                        class="rounded mt-3" style="max-width: 360px; max-height: 200px; object-fit: cover;">
                @endif
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="judul_putih" class="form-label fw-semibold">Judul warna putih</label>
                    <input type="text" id="judul_putih" name="judul_putih"
                        class="form-control @error('judul_putih') is-invalid @enderror"
                        value="{{ old('judul_putih', $slider->judul_putih) }}" maxlength="150" required>
                    @error('judul_putih')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="judul_kuning" class="form-label fw-semibold">Judul warna kuning</label>
                    <input type="text" id="judul_kuning" name="judul_kuning"
                        class="form-control @error('judul_kuning') is-invalid @enderror"
                        value="{{ old('judul_kuning', $slider->judul_kuning) }}" maxlength="150" required>
                    @error('judul_kuning')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="subjudul" class="form-label fw-semibold">Subjudul</label>
                <textarea id="subjudul" name="subjudul" rows="4"
                    class="form-control @error('subjudul') is-invalid @enderror" maxlength="1000" required>{{ old('subjudul', $slider->subjudul) }}</textarea>
                @error('subjudul')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="urutan" class="form-label fw-semibold">Urutan tampil</label>
                    <input type="number" id="urutan" name="urutan"
                        class="form-control @error('urutan') is-invalid @enderror"
                        value="{{ old('urutan', $slider->urutan ?? 0) }}" min="0" required>
                    @error('urutan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="1" @selected((string) old('status', $slider->exists ? (int) $slider->status : 1) === '1')>Aktif</option>
                        <option value="0" @selected((string) old('status', $slider->exists ? (int) $slider->status : 1) === '0')>Nonaktif</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-green">Simpan Slider</button>
                <a href="{{ route('admin.slider.index') }}" class="btn btn-outline-custom">Batal</a>
            </div>
        </form>
    </div>
@endsection
