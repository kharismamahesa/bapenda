<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegulasiKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KategoriRegulasiController extends Controller
{
    /**
     * Tampilkan halaman utama Kategori Regulasi.
     */
    public function index()
    {
        return view('admin.kategori_regulasi.index');
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

        $query = RegulasiKategori::withCount('regulasi');
        $recordsTotal = RegulasiKategori::count();

        if (! empty($search)) {
            $query->where('regulasi_kategori', 'like', "%{$search}%");
        }

        $recordsFiltered = $query->count();

        $columns = [
            0 => 'id_regulasi_kategori',
            1 => 'regulasi_kategori',
            2 => 'regulasi_count',
        ];

        if ($request->filled('order.0.column')) {
            $orderColumnIndex = (int) $request->input('order.0.column');
            $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

            if ($orderColumnIndex > 0 && isset($columns[$orderColumnIndex])) {
                $query->orderBy($columns[$orderColumnIndex], $orderDir);
            } else {
                $query->orderBy('id_regulasi_kategori', 'desc');
            }
        } else {
            $query->orderBy('id_regulasi_kategori', 'desc');
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
                'id_regulasi_kategori' => $item->id_regulasi_kategori,
                'regulasi_kategori' => '<span class="fw-semibold text-dark">'.e($item->regulasi_kategori).'</span>',
                'total_regulasi' => '<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2.5 py-1">'.$item->regulasi_count.' Regulasi</span>',
                'aksi' => '
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-light border text-muted btn-edit" data-id="'.$item->id_regulasi_kategori.'" title="Edit Kategori">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-light border text-danger btn-delete" data-id="'.$item->id_regulasi_kategori.'" title="Hapus Kategori">
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
            'regulasi_kategori' => 'required|string|max:200|unique:regulasi_kategori,regulasi_kategori',
        ], [
            'regulasi_kategori.required' => 'Nama kategori wajib diisi.',
            'regulasi_kategori.max' => 'Nama kategori maksimal 200 karakter.',
            'regulasi_kategori.unique' => 'Nama kategori sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal, silakan periksa inputan Anda.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $kategori = RegulasiKategori::create([
            'regulasi_kategori' => $request->regulasi_kategori,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori regulasi berhasil ditambahkan.',
            'data' => $kategori,
        ], 201);
    }

    /**
     * Ambil data spesifik untuk modal edit (Show).
     */
    public function show($id)
    {
        $kategori = RegulasiKategori::find($id);

        if (! $kategori) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori regulasi tidak ditemukan.',
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
        $kategori = RegulasiKategori::find($id);

        if (! $kategori) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori regulasi tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'regulasi_kategori' => 'required|string|max:200|unique:regulasi_kategori,regulasi_kategori,'.$id.',id_regulasi_kategori',
        ], [
            'regulasi_kategori.required' => 'Nama kategori wajib diisi.',
            'regulasi_kategori.max' => 'Nama kategori maksimal 200 karakter.',
            'regulasi_kategori.unique' => 'Nama kategori sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal, silakan periksa inputan Anda.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $kategori->update([
            'regulasi_kategori' => $request->regulasi_kategori,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori regulasi berhasil diperbarui.',
            'data' => $kategori,
        ]);
    }

    /**
     * Hapus kategori regulasi (Delete).
     */
    public function destroy($id)
    {
        $kategori = RegulasiKategori::find($id);

        if (! $kategori) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori regulasi tidak ditemukan.',
            ], 404);
        }

        if ($kategori->regulasi()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak dapat dihapus karena masih memiliki regulasi terkait.',
            ], 422);
        }

        $kategori->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori regulasi berhasil dihapus.',
        ]);
    }
}
