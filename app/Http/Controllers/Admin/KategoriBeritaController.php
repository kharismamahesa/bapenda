<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriBerita;
use Illuminate\Support\Facades\Validator;

class KategoriBeritaController extends Controller
{
    /**
     * Tampilkan halaman utama Kategori Berita.
     */
    public function index()
    {
        return view('admin.kategori_berita.index');
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

        $query = KategoriBerita::withCount('berita');
        $recordsTotal = KategoriBerita::count();

        if (!empty($search)) {
            $query->where('berita_kategori', 'like', "%{$search}%");
        }

        $recordsFiltered = $query->count();

        // Ordering (Default: urutkan data terakhir / terbaru paling atas)
        $columns = [
            0 => 'id_berita_kategori',
            1 => 'berita_kategori',
            2 => 'berita_count',
        ];

        if ($request->filled('order.0.column')) {
            $orderColumnIndex = (int) $request->input('order.0.column');
            $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

            if ($orderColumnIndex > 0 && isset($columns[$orderColumnIndex])) {
                $query->orderBy($columns[$orderColumnIndex], $orderDir);
            } else {
                $query->orderBy('id_berita_kategori', 'desc');
            }
        } else {
            $query->orderBy('id_berita_kategori', 'desc');
        }

        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $items = $query->get();

        $data = [];
        $no = $start + 1;
        foreach ($items as $item) {
            $data[] = [
                'no' => $no++,
                'id_berita_kategori' => $item->id_berita_kategori,
                'berita_kategori' => '<span class="fw-semibold text-dark">' . e($item->berita_kategori) . '</span>',
                'total_berita' => '<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2.5 py-1">' . $item->berita_count . ' Berita</span>',
                'aksi' => '
                    <div class="table-action-buttons">
                        <button type="button" class="btn btn-primary btn-icon-action btn-edit" data-id="' . $item->id_berita_kategori . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit kategori" aria-label="Edit kategori">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-icon-action btn-delete" data-id="' . $item->id_berita_kategori . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus kategori" aria-label="Hapus kategori">
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
     * Simpan data kategori baru (Create).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'berita_kategori' => 'required|string|max:200|unique:berita_kategori,berita_kategori',
        ], [
            'berita_kategori.required' => 'Nama kategori wajib diisi.',
            'berita_kategori.max' => 'Nama kategori maksimal 200 karakter.',
            'berita_kategori.unique' => 'Nama kategori sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal, silakan periksa inputan Anda.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $kategori = KategoriBerita::create([
            'berita_kategori' => $request->berita_kategori,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berita berhasil ditambahkan.',
            'data' => $kategori,
        ], 201);
    }

    /**
     * Ambil data spesifik untuk modal edit (Show).
     */
    public function show($id)
    {
        $kategori = KategoriBerita::find($id);

        if (!$kategori) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori berita tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $kategori,
        ]);
    }

    /**
     * Perbarui data kategori (Update).
     */
    public function update(Request $request, $id)
    {
        $kategori = KategoriBerita::find($id);

        if (!$kategori) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori berita tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'berita_kategori' => 'required|string|max:200|unique:berita_kategori,berita_kategori,' . $id . ',id_berita_kategori',
        ], [
            'berita_kategori.required' => 'Nama kategori wajib diisi.',
            'berita_kategori.max' => 'Nama kategori maksimal 200 karakter.',
            'berita_kategori.unique' => 'Nama kategori sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal, silakan periksa inputan Anda.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $kategori->update([
            'berita_kategori' => $request->berita_kategori,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berita berhasil diperbarui.',
            'data' => $kategori,
        ]);
    }

    /**
     * Hapus kategori berita (Delete).
     */
    public function destroy($id)
    {
        $kategori = KategoriBerita::find($id);

        if (!$kategori) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori berita tidak ditemukan.',
            ], 404);
        }

        // Cek jika kategori masih dipakai berita
        if ($kategori->berita()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak dapat dihapus karena masih memiliki berita terkait.',
            ], 422);
        }

        $kategori->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berita berhasil dihapus.',
        ]);
    }
}
