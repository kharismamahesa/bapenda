<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\KategoriBerita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    /**
     * Tampilkan halaman utama Berita.
     */
    public function index()
    {
        $kategoriList = KategoriBerita::orderBy('berita_kategori', 'asc')->get();

        return view('admin.berita.index', compact('kategoriList'));
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
        $kategoriFilter = $request->input('kategori_filter');
        $statusFilter = $request->input('status_filter');

        $query = Berita::with('kategori');
        $recordsTotal = Berita::count();

        // Filter kategori
        if (! empty($kategoriFilter) && $kategoriFilter !== 'all') {
            $query->where('id_berita_kategori', $kategoriFilter);
        }

        // Filter status
        if ($statusFilter !== null && $statusFilter !== '' && $statusFilter !== 'all') {
            $query->where('status', (int) $statusFilter);
        }

        // Pencarian global
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('isi', 'like', "%{$search}%")
                    ->orWhereHas('kategori', function ($qk) use ($search) {
                        $qk->where('berita_kategori', 'like', "%{$search}%");
                    });
            });
        }

        $recordsFiltered = $query->count();

        // Ordering (Default: urutkan data terakhir / terbaru paling atas)
        $columns = [
            0 => 'id_berita',
            1 => 'judul',
            2 => 'id_berita_kategori',
            3 => 'tanggal_berita',
            4 => 'counter',
            5 => 'status',
        ];

        if ($request->filled('order.0.column')) {
            $orderColumnIndex = (int) $request->input('order.0.column');
            $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

            // Jika user memilih kolom tertentu (selain kolom No)
            if ($orderColumnIndex > 0 && isset($columns[$orderColumnIndex])) {
                $query->orderBy($columns[$orderColumnIndex], $orderDir);
            } else {
                $query->orderBy('tanggal_berita', 'desc')->orderBy('id_berita', 'desc');
            }
        } else {
            $query->orderBy('tanggal_berita', 'desc')->orderBy('id_berita', 'desc');
        }

        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $items = $query->get();

        $data = [];
        $no = $start + 1;
        foreach ($items as $item) {
            $kategoriNama = $item->kategori ? e($item->kategori->berita_kategori) : '-';
            $tgl = $item->tanggal_berita ? date('d M Y, H:i', strtotime($item->tanggal_berita)) : '-';
            $statusBadge = $item->status == 1
                ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Publish</span>'
                : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Draft</span>';

            $data[] = [
                'no' => $no++,
                'id_berita' => $item->id_berita,
                'judul' => '
                    <div class="flex items-center gap-3">
                        <img src="'.$item->thumbnail_url.'" alt="'.e($item->judul).'" class="w-12 h-12 rounded-lg object-cover flex-shrink-0 border border-gray-100" onerror="this.src=\'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?w=100&h=100&fit=crop\'">
                        <div class="min-w-0">
                            <p class="text-foreground font-semibold line-clamp-1" title="'.e($item->judul).'">'.e($item->judul).'</p>
                            <p class="text-gray-500 text-xs">Slug: /'.e($item->url).'</p>
                        </div>
                    </div>
                ',
                'kategori' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">'.$kategoriNama.'</span>',
                'tanggal' => '<span class="text-xs text-gray-600">'.$tgl.'</span>',
                'views' => '<span class="text-xs font-medium text-gray-700">'.number_format($item->counter ?? 0).'</span>',
                'status' => $statusBadge,
                'aksi' => '
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button" class="btn-view p-2 hover:bg-gray-100 text-gray-600 rounded-lg transition-colors cursor-pointer" data-id="'.$item->id_berita.'" title="Lihat Detail">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                        <button type="button" class="btn-edit p-2 hover:bg-gray-100 text-gray-600 rounded-lg transition-colors cursor-pointer" data-id="'.$item->id_berita.'" title="Edit">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </button>
                        <button type="button" class="btn-delete p-2 hover:bg-red-50 text-error rounded-lg transition-colors cursor-pointer" data-id="'.$item->id_berita.'" title="Hapus">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
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
     * Simpan data berita baru (Create).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:250',
            'id_berita_kategori' => 'required|exists:t_berita_kategori,id_berita_kategori',
            'isi' => 'required|string',
            'status' => 'required|in:0,1',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'judul.required' => 'Judul berita wajib diisi.',
            'judul.max' => 'Judul berita maksimal 250 karakter.',
            'id_berita_kategori.required' => 'Kategori berita wajib dipilih.',
            'id_berita_kategori.exists' => 'Kategori berita tidak valid.',
            'isi.required' => 'Konten/isi berita wajib diisi.',
            'status.required' => 'Status berita wajib ditentukan.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal, silakan periksa inputan Anda.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Upload gambar jika ada
        $gambarName = null;
        $thumbnailName = null;
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $extension = $file->getClientOriginalExtension();
            $hash = md5(time().'_'.rand(1000, 9999));
            $gambarName = $hash.'.'.$extension;
            $thumbnailName = 'thumb_'.$hash.'.'.$extension;

            $destination = public_path('uploads/berita');
            if (! File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            // Simpan gambar asli & thumbnail
            $file->move($destination, $gambarName);
            // Copy untuk thumbnail
            File::copy($destination.'/'.$gambarName, $destination.'/'.$thumbnailName);
        }

        $slug = Str::slug($request->judul);
        // Pastikan slug unik
        $originalSlug = $slug;
        $count = 1;
        while (Berita::where('url', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        $berita = Berita::create([
            'id_berita_kategori' => $request->id_berita_kategori,
            'user_id' => auth()->id(),
            'judul' => $request->judul,
            'isi' => $request->isi,
            'thumbnail' => $thumbnailName,
            'gambar' => $gambarName,
            'tanggal_berita' => now(),
            'counter' => 0,
            'url' => $slug,
            'status' => (int) $request->status,
            'created_datetime' => now(),
            'created_by' => auth()->id() ?? 1,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Berita berhasil diterbitkan.',
            'data' => $berita,
        ], 201);
    }

    /**
     * Ambil data spesifik berita (Show / Edit / View).
     */
    public function show($id)
    {
        $berita = Berita::with('kategori')->find($id);

        if (! $berita) {
            return response()->json([
                'status' => 'error',
                'message' => 'Berita tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id_berita' => $berita->id_berita,
                'id_berita_kategori' => $berita->id_berita_kategori,
                'kategori_nama' => $berita->kategori ? $berita->kategori->berita_kategori : '-',
                'judul' => $berita->judul,
                'isi' => $berita->isi,
                'status' => (string) $berita->status,
                'url' => $berita->url,
                'tanggal_berita' => $berita->tanggal_berita ? date('d F Y, H:i', strtotime($berita->tanggal_berita)) : '-',
                'counter' => $berita->counter ?? 0,
                'gambar_url' => $berita->gambar_url,
                'thumbnail_url' => $berita->thumbnail_url,
            ],
        ]);
    }

    /**
     * Perbarui data berita (Update).
     */
    public function update(Request $request, $id)
    {
        $berita = Berita::find($id);

        if (! $berita) {
            return response()->json([
                'status' => 'error',
                'message' => 'Berita tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:250',
            'id_berita_kategori' => 'required|exists:t_berita_kategori,id_berita_kategori',
            'isi' => 'required|string',
            'status' => 'required|in:0,1',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'judul.required' => 'Judul berita wajib diisi.',
            'judul.max' => 'Judul berita maksimal 250 karakter.',
            'id_berita_kategori.required' => 'Kategori berita wajib dipilih.',
            'id_berita_kategori.exists' => 'Kategori berita tidak valid.',
            'isi.required' => 'Konten/isi berita wajib diisi.',
            'status.required' => 'Status berita wajib ditentukan.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal, silakan periksa inputan Anda.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $gambarName = $berita->gambar;
        $thumbnailName = $berita->thumbnail;

        // Upload gambar baru jika disediakan
        if ($request->hasFile('gambar')) {
            $destination = public_path('uploads/berita');
            if (! File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            // Hapus gambar lama jika ada
            if ($berita->gambar && File::exists($destination.'/'.$berita->gambar)) {
                File::delete($destination.'/'.$berita->gambar);
            }
            if ($berita->thumbnail && File::exists($destination.'/'.$berita->thumbnail)) {
                File::delete($destination.'/'.$berita->thumbnail);
            }

            $file = $request->file('gambar');
            $extension = $file->getClientOriginalExtension();
            $hash = md5(time().'_'.rand(1000, 9999));
            $gambarName = $hash.'.'.$extension;
            $thumbnailName = 'thumb_'.$hash.'.'.$extension;

            $file->move($destination, $gambarName);
            File::copy($destination.'/'.$gambarName, $destination.'/'.$thumbnailName);
        }

        // Generate slug baru jika judul berubah
        $slug = $berita->url;
        if ($request->judul !== $berita->judul) {
            $newSlug = Str::slug($request->judul);
            $originalSlug = $newSlug;
            $count = 1;
            while (Berita::where('url', $newSlug)->where('id_berita', '!=', $id)->exists()) {
                $newSlug = "{$originalSlug}-{$count}";
                $count++;
            }
            $slug = $newSlug;
        }

        $berita->update([
            'id_berita_kategori' => $request->id_berita_kategori,
            'judul' => $request->judul,
            'isi' => $request->isi,
            'thumbnail' => $thumbnailName,
            'gambar' => $gambarName,
            'url' => $slug,
            'status' => (int) $request->status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Berita berhasil diperbarui.',
            'data' => $berita,
        ]);
    }

    /**
     * Hapus data berita (Delete).
     */
    public function destroy($id)
    {
        $berita = Berita::find($id);

        if (! $berita) {
            return response()->json([
                'status' => 'error',
                'message' => 'Berita tidak ditemukan.',
            ], 404);
        }

        $destination = public_path('uploads/berita');
        if ($berita->gambar && File::exists($destination.'/'.$berita->gambar)) {
            File::delete($destination.'/'.$berita->gambar);
        }
        if ($berita->thumbnail && File::exists($destination.'/'.$berita->thumbnail)) {
            File::delete($destination.'/'.$berita->thumbnail);
        }

        $berita->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Berita berhasil dihapus.',
        ]);
    }
}
