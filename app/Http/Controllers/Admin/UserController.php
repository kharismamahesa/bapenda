<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Tampilkan halaman utama manajemen user.
     */
    public function index()
    {
        $kantorPelayanan = DB::table('wilayah_samsat')
            ->select('KodeWilayah', 'NamaUPTUP', 'Status')
            ->orderBy('NamaWilayah')
            ->get();

        return view('admin.user.index', compact('kantorPelayanan'));
    }

    /**
     * Endpoint DataTables JSON untuk daftar user.
     */
    public function data(Request $request)
    {
        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $requestedLength = (int) $request->input('length', 10);
        $length = $requestedLength < 0 ? 100 : min(100, max(1, $requestedLength));
        $search = $request->input('search.value');

        $query = User::query();
        $recordsTotal = User::count();

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = $query->count();

        // Ordering
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'username',
            3 => 'role',
            4 => 'created_at',
        ];

        if ($request->filled('order.0.column')) {
            $orderColumnIndex = (int) $request->input('order.0.column');
            $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

            if ($orderColumnIndex > 0 && isset($columns[$orderColumnIndex])) {
                $query->orderBy($columns[$orderColumnIndex], $orderDir);
            } else {
                $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $items = $query->get();
        $currentUserId = Auth::id();

        $data = [];
        $no = $start + 1;
        foreach ($items as $item) {
            $initial = strtoupper(substr($item->name, 0, 1));
            $isSelf = ($item->id === $currentUserId);

            $roleBadge = match ($item->role) {
                'superadmin' => '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1">Superadmin</span>',
                'uptup' => '<span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2.5 py-1">UPTUP</span>',
                default => '<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2.5 py-1">Admin</span>',
            };

            $userDisplay = '
                <div class="d-flex align-items-center gap-3">
                    <div class="user-avatar-circle" style="width: 36px; height: 36px; font-size: 0.9rem;">
                        '.$initial.'
                    </div>
                    <div>
                        <div class="fw-semibold text-dark d-flex align-items-center gap-2">
                            '.e($item->name).'
                            '.($isSelf ? '<span class="badge bg-secondary-subtle text-secondary small">(Anda)</span>' : '').'
                        </div>
                        <div class="text-muted small">'.e($item->username).'</div>
                    </div>
                </div>
            ';

            $deleteButton = ! $isSelf ? '
                <button type="button" class="btn btn-danger btn-icon-action btn-delete" data-id="'.$item->id.'" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus pengguna" aria-label="Hapus pengguna">
                    <i class="bi bi-trash3"></i>
                </button>
            ' : '
                <button type="button" class="btn btn-danger btn-icon-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Tidak dapat menghapus akun sendiri" aria-label="Tidak dapat menghapus akun sendiri" disabled>
                    <i class="bi bi-trash3"></i>
                </button>
            ';

            $actions = '
                <div class="table-action-buttons">
                    <button type="button" class="btn btn-primary btn-icon-action btn-edit" data-id="'.$item->id.'" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit pengguna" aria-label="Edit pengguna">
                        <i class="bi bi-pencil"></i>
                    </button>
                    '.$deleteButton.'
                </div>
            ';

            $data[] = [
                'no' => $no++,
                'name' => $userDisplay,
                'username' => e($item->username),
                'role' => $roleBadge,
                'created_at' => $item->created_at ? $item->created_at->format('d M Y, H:i') : '-',
                'aksi' => $actions,
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
     * Simpan user baru ke database.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|alpha_dash|unique:users,username',
            'role' => 'required|in:superadmin,admin,uptup',
            'id_uptup' => 'required_if:role,uptup|nullable|integer|exists:wilayah_samsat,KodeWilayah',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama lengkap pengguna wajib diisi.',
            'username.required' => 'Username pengguna wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'username.unique' => 'Username ini sudah terdaftar dalam sistem.',
            'role.required' => 'Pilih role pengguna.',
            'role.in' => 'Role tidak valid.',
            'id_uptup.required_if' => 'Pilih kantor pelayanan untuk akun UPTUP.',
            'id_uptup.exists' => 'Kantor pelayanan tidak ditemukan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        User::create([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'role' => $request->input('role'),
            'id_uptup' => $request->input('role') === 'uptup' ? $request->input('id_uptup') : null,
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna baru berhasil ditambahkan.',
        ]);
    }

    /**
     * Ambil data detail user untuk modal edit.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'role' => $user->role,
                'id_uptup' => $user->id_uptup,
            ],
        ]);
    }

    /**
     * Perbarui data user.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|alpha_dash|unique:users,username,'.$id,
            'role' => 'required|in:superadmin,admin,uptup',
            'id_uptup' => 'required_if:role,uptup|nullable|integer|exists:wilayah_samsat,KodeWilayah',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama lengkap pengguna wajib diisi.',
            'username.required' => 'Username pengguna wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'username.unique' => 'Username ini sudah digunakan oleh pengguna lain.',
            'role.required' => 'Pilih role pengguna.',
            'role.in' => 'Role tidak valid.',
            'id_uptup.required_if' => 'Pilih kantor pelayanan untuk akun UPTUP.',
            'id_uptup.exists' => 'Kantor pelayanan tidak ditemukan.',
            'password.min' => 'Password baru minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->role = $request->input('role');
        $user->id_uptup = $request->input('role') === 'uptup' ? $request->input('id_uptup') : null;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Data pengguna berhasil diperbarui.',
        ]);
    }

    /**
     * Hapus data user.
     */
    public function destroy($id)
    {
        if (Auth::id() == $id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun yang sedang Anda gunakan.',
            ], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dihapus dari sistem.',
        ]);
    }
}
