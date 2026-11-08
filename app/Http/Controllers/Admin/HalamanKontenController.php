<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HalamanKonten;
use App\Support\HtmlContentSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class HalamanKontenController extends Controller
{
    /**
     * Tampilkan halaman utama daftar Halaman Konten.
     */
    public function index()
    {
        return view('admin.halaman_konten.index');
    }

    /**
     * Endpoint DataTables JSON (Read).
     */
    public function data(Request $request)
    {
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = $request->input('search.value');

        $query = HalamanKonten::query();
        $recordsTotal = HalamanKonten::count();

        // Pencarian global
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = $query->count();

        // Ordering
        $columns = [
            0 => 'id',
            1 => 'judul',
            2 => 'menu_parent',
            3 => 'urutan',
            5 => 'updated_at',
        ];

        if ($request->filled('order.0.column')) {
            $orderColumnIndex = (int) $request->input('order.0.column');
            $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

            if ($orderColumnIndex > 0 && isset($columns[$orderColumnIndex])) {
                $query->orderBy($columns[$orderColumnIndex], $orderDir);
            } else {
                $query->orderBy('updated_at', 'desc');
            }
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $items = $query->get();

        $data = [];
        $no = $start + 1;
        foreach ($items as $item) {
            // Tentukan tipe konten
            $hasIsi = ! empty($item->isi);
            $hasGambar = ! empty($item->gambar);

            if ($hasIsi && $hasGambar) {
                $tipeBadge = '<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2 py-1">Teks & Gambar</span>';
            } elseif ($hasGambar) {
                $tipeBadge = '<span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">Gambar</span>';
            } elseif ($hasIsi) {
                $tipeBadge = '<span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle px-2 py-1">Teks</span>';
            } else {
                $tipeBadge = '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">Kosong</span>';
            }

            $tgl = $item->updated_at ? $item->updated_at->format('d M Y, H:i') : '-';

            $data[] = [
                'no' => $no++,
                'id' => $item->id,
                'judul' => '
                    <div class="d-flex align-items-center gap-3">
                        '.($hasGambar
                            ? '<img src="/uploads/halaman/'.e($item->gambar).'" alt="'.e($item->judul).'" class="rounded-2 border flex-shrink-0" style="width: 44px; height: 44px; object-fit: cover;" onerror="this.style.display=\'none\'">'
                            : '<div class="rounded-2 border flex-shrink-0 d-flex align-items-center justify-content-center bg-light" style="width: 44px; height: 44px;"><i class="bi bi-file-earmark-text text-muted"></i></div>'
                        ).'
                        <div class="min-w-0">
                            <div class="fw-semibold text-dark text-truncate" style="max-width: 280px;" title="'.e($item->judul).'">'.e($item->judul).'</div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">/'.e($item->slug).'</small>
                        </div>
                    </div>
                ',
                'menu' => $item->menu_parent
                    ? '<span class="badge bg-primary-subtle text-primary-emphasis">'.e(ucfirst($item->menu_parent)).'</span>'
                    : '<span class="badge bg-secondary-subtle text-secondary-emphasis">Tidak ditampilkan</span>',
                'urutan' => '<span class="text-muted small">'.(int) $item->urutan.'</span>',
                'tipe' => $tipeBadge,
                'updated' => '<span class="text-muted small">'.$tgl.'</span>',
                'aksi' => '
                    <div class="table-action-buttons">
                        <button type="button" class="btn btn-info btn-icon-action btn-view" data-id="'.$item->id.'" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat detail" aria-label="Lihat detail">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button type="button" class="btn btn-primary btn-icon-action btn-edit" data-id="'.$item->id.'" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit halaman" aria-label="Edit halaman">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-icon-action btn-delete" data-id="'.$item->id.'" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus halaman" aria-label="Hapus halaman">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                ',
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Simpan halaman konten baru (Create).
     */
    public function store(Request $request)
    {
        if (is_string($request->input('isi'))) {
            $request->merge(['isi' => HtmlContentSanitizer::sanitize($request->input('isi'))]);
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'isi' => 'nullable|string',
            'menu_parent' => 'nullable|in:profil,layanan',
            'urutan' => 'nullable|integer|min:0|max:4294967295',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ], [
            'judul.required' => 'Judul halaman wajib diisi.',
            'judul.max' => 'Judul halaman maksimal 255 karakter.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus jpeg, png, jpg, webp, atau svg.',
            'gambar.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        // Validasi minimal salah satu harus diisi
        $validator->after(function ($validator) use ($request) {
            $hasIsi = ! empty($request->input('isi')) && $request->input('isi') !== '<p><br></p>';
            $hasGambar = $request->hasFile('gambar');

            if (! $hasIsi && ! $hasGambar) {
                $validator->errors()->add('isi', 'Isi konten atau gambar wajib diisi (minimal salah satu).');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal, silakan periksa inputan Anda.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Upload gambar jika ada
        $gambarName = null;
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $extension = $file->getClientOriginalExtension();
            $hash = md5(time().'_'.rand(1000, 9999));
            $gambarName = $hash.'.'.$extension;

            $destination = public_path('uploads/halaman');
            if (! File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $file->move($destination, $gambarName);
        }

        $slug = HalamanKonten::generateSlug($request->judul);

        $halaman = HalamanKonten::create([
            'judul' => $request->judul,
            'slug' => $slug,
            'isi' => $request->isi,
            'gambar' => $gambarName,
            'menu_parent' => $request->input('menu_parent') ?: null,
            'urutan' => (int) $request->input('urutan', 0),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Halaman konten berhasil ditambahkan.',
            'data' => $halaman,
        ], 201);
    }

    /**
     * Ambil data spesifik halaman konten (Show).
     */
    public function show($id)
    {
        $halaman = HalamanKonten::find($id);

        if (! $halaman) {
            return response()->json([
                'status' => 'error',
                'message' => 'Halaman konten tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $halaman->id,
                'judul' => $halaman->judul,
                'slug' => $halaman->slug,
                'menu_parent' => $halaman->menu_parent,
                'urutan' => $halaman->urutan,
                'isi' => $halaman->isi,
                'gambar' => $halaman->gambar,
                'gambar_url' => $halaman->gambar ? '/uploads/halaman/'.$halaman->gambar : null,
                'updated_at' => $halaman->updated_at ? $halaman->updated_at->format('d F Y, H:i') : '-',
            ],
        ]);
    }

    /**
     * Perbarui data halaman konten (Update).
     */
    public function update(Request $request, $id)
    {
        $halaman = HalamanKonten::find($id);

        if (! $halaman) {
            return response()->json([
                'status' => 'error',
                'message' => 'Halaman konten tidak ditemukan.',
            ], 404);
        }

        if (is_string($request->input('isi'))) {
            $request->merge(['isi' => HtmlContentSanitizer::sanitize($request->input('isi'))]);
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'isi' => 'nullable|string',
            'menu_parent' => 'nullable|in:profil,layanan',
            'urutan' => 'nullable|integer|min:0|max:4294967295',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ], [
            'judul.required' => 'Judul halaman wajib diisi.',
            'judul.max' => 'Judul halaman maksimal 255 karakter.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus jpeg, png, jpg, webp, atau svg.',
            'gambar.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        // Validasi: minimal isi atau gambar harus ada (termasuk gambar lama)
        $validator->after(function ($validator) use ($request, $halaman) {
            $hasIsi = ! empty($request->input('isi')) && $request->input('isi') !== '<p><br></p>';
            $hasNewGambar = $request->hasFile('gambar');
            $hasExistingGambar = ! empty($halaman->gambar) && ! $request->boolean('hapus_gambar');

            if (! $hasIsi && ! $hasNewGambar && ! $hasExistingGambar) {
                $validator->errors()->add('isi', 'Isi konten atau gambar wajib diisi (minimal salah satu).');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal, silakan periksa inputan Anda.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $gambarName = $halaman->gambar;

        // Hapus gambar jika diminta
        if ($request->boolean('hapus_gambar')) {
            $destination = public_path('uploads/halaman');
            if ($halaman->gambar && File::exists($destination.'/'.$halaman->gambar)) {
                File::delete($destination.'/'.$halaman->gambar);
            }
            $gambarName = null;
        }

        // Upload gambar baru jika disediakan
        if ($request->hasFile('gambar')) {
            $destination = public_path('uploads/halaman');
            if (! File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            // Hapus gambar lama jika ada
            if ($halaman->gambar && File::exists($destination.'/'.$halaman->gambar)) {
                File::delete($destination.'/'.$halaman->gambar);
            }

            $file = $request->file('gambar');
            $extension = $file->getClientOriginalExtension();
            $hash = md5(time().'_'.rand(1000, 9999));
            $gambarName = $hash.'.'.$extension;

            $file->move($destination, $gambarName);
        }

        // Generate slug baru jika judul berubah
        $slug = $halaman->slug;
        if ($request->judul !== $halaman->judul) {
            $slug = HalamanKonten::generateSlug($request->judul, $id);
        }

        $halaman->update([
            'judul' => $request->judul,
            'slug' => $slug,
            'isi' => $request->isi,
            'gambar' => $gambarName,
            'menu_parent' => $request->input('menu_parent') ?: null,
            'urutan' => (int) $request->input('urutan', 0),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Halaman konten berhasil diperbarui.',
            'data' => $halaman,
        ]);
    }

    /**
     * Hapus data halaman konten (Delete).
     */
    public function destroy($id)
    {
        $halaman = HalamanKonten::find($id);

        if (! $halaman) {
            return response()->json([
                'status' => 'error',
                'message' => 'Halaman konten tidak ditemukan.',
            ], 404);
        }

        // Hapus file gambar jika ada
        $destination = public_path('uploads/halaman');
        if ($halaman->gambar && File::exists($destination.'/'.$halaman->gambar)) {
            File::delete($destination.'/'.$halaman->gambar);
        }

        $halaman->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Halaman konten berhasil dihapus.',
        ]);
    }
}
