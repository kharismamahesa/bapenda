<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Regulasi;
use App\Models\RegulasiKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class RegulasiController extends Controller
{
    /**
     * Tampilkan halaman utama Regulasi.
     */
    public function index()
    {
        $kategoriList = RegulasiKategori::orderBy('regulasi_kategori', 'asc')->get();

        return view('admin.regulasi.index', compact('kategoriList'));
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

        $query = Regulasi::with('kategori');
        $recordsTotal = Regulasi::count();

        if (! empty($kategoriFilter) && $kategoriFilter !== 'all') {
            $query->where('id_regulasi_kategori', $kategoriFilter);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('regulasi', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('kategori', function ($qk) use ($search) {
                        $qk->where('regulasi_kategori', 'like', "%{$search}%");
                    });
            });
        }

        $recordsFiltered = $query->count();

        $columns = [
            0 => 'id_regulasi',
            1 => 'regulasi',
            2 => 'id_regulasi_kategori',
            3 => 'file',
            4 => 'created_datetime',
        ];

        if ($request->filled('order.0.column')) {
            $orderColumnIndex = (int) $request->input('order.0.column');
            $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

            if ($orderColumnIndex > 0 && isset($columns[$orderColumnIndex])) {
                $query->orderBy($columns[$orderColumnIndex], $orderDir);

                if ($columns[$orderColumnIndex] === 'created_datetime') {
                    $query->orderBy('id_regulasi', 'desc');
                }
            } else {
                $query->orderBy('created_datetime', 'desc')->orderBy('id_regulasi', 'desc');
            }
        } else {
            $query->orderBy('created_datetime', 'desc')->orderBy('id_regulasi', 'desc');
        }

        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $items = $query->get();

        $data = [];
        $no = $start + 1;
        foreach ($items as $item) {
            $kategoriNama = $item->kategori ? e($item->kategori->regulasi_kategori) : '-';
            $tgl = $item->created_datetime ? date('d M Y, H:i', strtotime($item->created_datetime)) : '-';
            $keterangan = $item->keterangan
                ? '<small class="text-muted d-block text-truncate" style="max-width: 340px;">'.e($item->keterangan).'</small>'
                : '';

            $fileBadge = $item->file
                ? '<a href="'.route('regulasi.download', $item->id_regulasi).'" target="_blank" class="btn btn-sm btn-light border text-success" title="Unduh file">
                        <i class="bi bi-file-earmark-arrow-down"></i> '.strtoupper(e($item->file_extension ?: 'FILE')).'
                    </a>'
                : '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1">Tidak ada file</span>';

            $data[] = [
                'no' => $no++,
                'id_regulasi' => $item->id_regulasi,
                'regulasi' => '
                    <div class="min-w-0">
                        <div class="fw-semibold text-dark" style="max-width: 340px;" title="'.e($item->regulasi).'">'.e($item->regulasi).'</div>
                        '.$keterangan.'
                    </div>
                ',
                'kategori' => '<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2.5 py-1">'.$kategoriNama.'</span>',
                'file' => $fileBadge,
                'tanggal' => '<span class="text-muted small">'.$tgl.'</span>',
                'aksi' => '
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-light border text-muted btn-view" data-id="'.$item->id_regulasi.'" title="Lihat Detail">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button type="button" class="btn btn-light border text-muted btn-edit" data-id="'.$item->id_regulasi.'" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-light border text-danger btn-delete" data-id="'.$item->id_regulasi.'" title="Hapus">
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
     * Simpan data regulasi baru (Create).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'regulasi' => 'required|string|max:500',
            'id_regulasi_kategori' => 'required|exists:regulasi_kategori,id_regulasi_kategori',
            'keterangan' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ], [
            'regulasi.required' => 'Nama/judul regulasi wajib diisi.',
            'regulasi.max' => 'Nama/judul regulasi maksimal 500 karakter.',
            'id_regulasi_kategori.required' => 'Kategori regulasi wajib dipilih.',
            'id_regulasi_kategori.exists' => 'Kategori regulasi tidak valid.',
            'file.file' => 'File regulasi tidak valid.',
            'file.mimes' => 'Format file harus pdf, doc, docx, xls, xlsx, ppt, atau pptx.',
            'file.max' => 'Ukuran file maksimal 10MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal, silakan periksa inputan Anda.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $fileName = null;
        if ($request->hasFile('file')) {
            $fileName = $this->uploadFile($request->file('file'));
        }

        $regulasi = Regulasi::create([
            'regulasi' => $request->regulasi,
            'id_regulasi_kategori' => $request->id_regulasi_kategori,
            'keterangan' => $request->keterangan,
            'file' => $fileName,
            'created_datetime' => now(),
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Regulasi berhasil ditambahkan.',
            'data' => $regulasi,
        ], 201);
    }

    /**
     * Ambil data spesifik regulasi (Show / Edit / View).
     */
    public function show($id)
    {
        $regulasi = Regulasi::with('kategori')->find($id);

        if (! $regulasi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Regulasi tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id_regulasi' => $regulasi->id_regulasi,
                'regulasi' => $regulasi->regulasi,
                'id_regulasi_kategori' => $regulasi->id_regulasi_kategori,
                'kategori_nama' => $regulasi->kategori ? $regulasi->kategori->regulasi_kategori : '-',
                'keterangan' => $regulasi->keterangan,
                'file' => $regulasi->file,
                'file_name' => $regulasi->file ? basename(str_replace('\\', '/', $regulasi->file)) : null,
                'file_url' => $regulasi->file_url,
                'file_extension' => $regulasi->file_extension,
                'created_datetime' => $regulasi->created_datetime ? date('d F Y, H:i', strtotime($regulasi->created_datetime)) : '-',
            ],
        ]);
    }

    /**
     * Perbarui data regulasi (Update).
     */
    public function update(Request $request, $id)
    {
        $regulasi = Regulasi::find($id);

        if (! $regulasi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Regulasi tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'regulasi' => 'required|string|max:500',
            'id_regulasi_kategori' => 'required|exists:regulasi_kategori,id_regulasi_kategori',
            'keterangan' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ], [
            'regulasi.required' => 'Nama/judul regulasi wajib diisi.',
            'regulasi.max' => 'Nama/judul regulasi maksimal 500 karakter.',
            'id_regulasi_kategori.required' => 'Kategori regulasi wajib dipilih.',
            'id_regulasi_kategori.exists' => 'Kategori regulasi tidak valid.',
            'file.file' => 'File regulasi tidak valid.',
            'file.mimes' => 'Format file harus pdf, doc, docx, xls, xlsx, ppt, atau pptx.',
            'file.max' => 'Ukuran file maksimal 10MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal, silakan periksa inputan Anda.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $fileName = $regulasi->file;

        if ($request->hasFile('file')) {
            $this->deleteFile($regulasi->file);
            $fileName = $this->uploadFile($request->file('file'));
        }

        $regulasi->update([
            'regulasi' => $request->regulasi,
            'id_regulasi_kategori' => $request->id_regulasi_kategori,
            'keterangan' => $request->keterangan,
            'file' => $fileName,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Regulasi berhasil diperbarui.',
            'data' => $regulasi,
        ]);
    }

    /**
     * Hapus data regulasi (Delete).
     */
    public function destroy($id)
    {
        $regulasi = Regulasi::find($id);

        if (! $regulasi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Regulasi tidak ditemukan.',
            ], 404);
        }

        $this->deleteFile($regulasi->file);
        $regulasi->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Regulasi berhasil dihapus.',
        ]);
    }

    private function uploadFile($file): string
    {
        $destination = public_path('uploads/regulasi');
        if (! File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $hash = md5(time().'_'.rand(1000, 9999));
        $fileName = $hash.'.'.$extension;

        $file->move($destination, $fileName);

        return $fileName;
    }

    private function deleteFile(?string $file): void
    {
        if (! $file || preg_match('/^https?:\/\//i', $file)) {
            return;
        }

        $path = public_path('uploads/regulasi/'.basename(str_replace('\\', '/', $file)));

        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
