<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\PengaduanBalas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class PengaduanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $officeName = $user->role === 'uptup'
            ? DB::table('wilayah_samsat')->where('KodeWilayah', $user->id_uptup)->value('NamaUPTUP')
            : null;
        $scopeLabel = $user->role === 'uptup'
            ? 'UPT '.($officeName ?: 'ID '.$user->id_uptup)
            : 'Semua kantor pelayanan, termasuk Bapenda Provinsi Riau';

        return view('admin.pengaduan.index', [
            'scopeLabel' => $scopeLabel,
            'dataUrl' => route('admin.pengaduan.data'),
            'statsUrl' => route('admin.pengaduan.stats'),
        ]);
    }

    public function data(Request $request)
    {
        $start = max(0, (int) $request->input('start', 0));
        $requestedLength = (int) $request->input('length', 15);
        $length = $requestedLength < 0 ? 100 : min(100, max(1, $requestedLength));
        $searchValue = $request->input('search.value', '');
        $search = is_string($searchValue) ? mb_substr(trim($searchValue), 0, 100) : '';
        $query = $this->visiblePengaduan()
            ->leftJoin('wilayah_samsat', 'pengaduan.id_uptup', '=', 'wilayah_samsat.KodeWilayah')
            ->select([
                'pengaduan.id',
                'pengaduan.kode',
                'pengaduan.nama',
                'pengaduan.email',
                'pengaduan.id_uptup',
                'pengaduan.created_datetime',
                'pengaduan.status',
                'wilayah_samsat.NamaUPTUP as nama_uptup',
            ])
            ->withCount('balasan');
        $recordsTotal = (clone $query)->count('pengaduan.id');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('pengaduan.kode', 'like', "%{$search}%")
                    ->orWhere('pengaduan.nama', 'like', "%{$search}%")
                    ->orWhere('pengaduan.email', 'like', "%{$search}%")
                    ->orWhere('pengaduan.no_hp', 'like', "%{$search}%")
                    ->orWhere('pengaduan.aduan', 'like', "%{$search}%")
                    ->orWhere('wilayah_samsat.NamaUPTUP', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $query)->count('pengaduan.id');
        $orderColumns = [
            0 => 'pengaduan.kode',
            1 => 'pengaduan.nama',
            2 => 'wilayah_samsat.NamaUPTUP',
            3 => 'pengaduan.created_datetime',
            4 => 'pengaduan.status',
        ];
        $orderIndex = (int) $request->input('order.0.column', 3);
        $orderDirection = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($orderColumns[$orderIndex] ?? 'pengaduan.created_datetime', $orderDirection)
            ->orderByDesc('pengaduan.id');

        $items = $query->skip($start)->take($length)->get();
        $canDelete = in_array(auth()->user()->role, ['admin', 'superadmin'], true);
        $data = $items->map(function (Pengaduan $item, int $index) use ($start, $canDelete) {
            $office = (int) $item->id_uptup === Pengaduan::BAPENDA_ID
                ? 'BAPENDA PROVINSI RIAU'
                : ($item->nama_uptup ?: 'Kantor pelayanan');
            $status = $item->balasan_count > 0
                ? '<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Dibalas</span>'
                : '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">'.e([0 => 'Menunggu', 1 => 'Aktif', 2 => 'Publish', 3 => 'Ditangguhkan', 4 => 'Publis'][$item->status] ?? 'Status '.$item->status).'</span>';
            $actions = '<div class="table-action-buttons"><a href="'.route('admin.pengaduan.show', $item->id).'" class="btn btn-info btn-icon-action" title="Lihat dan balas pengaduan" aria-label="Lihat dan balas pengaduan"><i class="bi bi-chat-left-text"></i></a>';
            if ($canDelete) {
                $actions .= '<form method="POST" action="'.route('admin.pengaduan.destroy', $item->id).'" onsubmit="return confirm(\'Hapus pengaduan beserta seluruh riwayat balasannya? Tindakan ini tidak dapat dibatalkan.\')"><input type="hidden" name="_token" value="'.e(csrf_token()).'"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="btn btn-danger btn-icon-action" title="Hapus pengaduan" aria-label="Hapus pengaduan"><i class="bi bi-trash3"></i></button></form>';
            }
            $actions .= '</div>';

            return [
                'kode' => '<span class="fw-semibold text-dark">'.e($item->kode ?: '—').'</span>',
                'pengadu' => '<div class="fw-semibold text-dark">'.e($item->nama).'</div><small class="text-muted">'.e($item->email).'</small>',
                'kantor' => '<span class="small">'.e($office).'</span>',
                'tanggal' => '<span class="text-muted small">'.e(optional($item->created_datetime)->format('d M Y, H:i') ?: '—').'</span>',
                'status' => $status,
                'aksi' => $actions,
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function stats()
    {
        $visiblePengaduan = $this->visiblePengaduan();

        return response()->json([
            'total' => (clone $visiblePengaduan)->count(),
            'replied' => (clone $visiblePengaduan)->whereHas('balasan')->count(),
            'pending' => (clone $visiblePengaduan)->whereDoesntHave('balasan')->count(),
        ]);
    }

    public function show(int $id)
    {
        $pengaduan = $this->visiblePengaduan()
            ->with('balasan.user')
            ->findOrFail($id);

        $kantorTujuan = (int) $pengaduan->id_uptup === Pengaduan::BAPENDA_ID
            ? 'BAPENDA PROVINSI RIAU'
            : DB::table('wilayah_samsat')
                ->where('KodeWilayah', $pengaduan->id_uptup)
                ->value('NamaUPTUP');

        if (! $kantorTujuan) {
            $kantorTujuan = 'Kantor pelayanan tidak ditemukan (ID: '.$pengaduan->id_uptup.')';
        }

        return view('admin.pengaduan.show', compact('pengaduan', 'kantorTujuan'));
    }

    public function reply(Request $request, int $id)
    {
        $validated = $request->validate([
            'balasan' => ['required', 'string', 'max:20000'],
        ], [
            'balasan.required' => 'Isi balasan wajib diisi.',
        ]);

        $pengaduan = $this->visiblePengaduan()->findOrFail($id);
        $balasan = trim($validated['balasan']);

        try {
            Mail::raw(
                "Yth. {$pengaduan->nama},\n\n"
                ."Berikut balasan atas pengaduan Anda dengan kode {$pengaduan->kode}:\n\n"
                ."{$balasan}\n\n"
                ."Hormat kami,\nBadan Pendapatan Daerah Provinsi Riau",
                function ($message) use ($pengaduan) {
                    $message->to($pengaduan->email)
                        ->subject('Balasan Pengaduan '.$pengaduan->kode);
                }
            );
        } catch (Throwable $exception) {
            Log::error('Gagal mengirim balasan pengaduan melalui email.', [
                'pengaduan_id' => $pengaduan->id,
                'email' => $pengaduan->email,
                'exception' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['balasan' => 'Email balasan gagal dikirim. Periksa konfigurasi email lalu coba lagi.']);
        }

        DB::transaction(function () use ($pengaduan, $balasan) {
            PengaduanBalas::create([
                'id_pengaduan' => (string) $pengaduan->id,
                'balasan' => $balasan,
                'updated_datetime' => now(),
                'updated_by' => auth()->id(),
                'status' => 1,
            ]);

            $pengaduan->status = 1;
            $pengaduan->updated_datetime = now();
            $pengaduan->updated_by = auth()->id();
            $pengaduan->save();
        });

        return redirect()->route('admin.pengaduan.show', $pengaduan->id)
            ->with('success', 'Balasan berhasil dikirim ke email pengadu.');
    }

    public function destroy(int $id)
    {
        DB::transaction(function () use ($id) {
            $pengaduan = $this->visiblePengaduan()
                ->lockForUpdate()
                ->findOrFail($id);

            PengaduanBalas::where('id_pengaduan', (string) $pengaduan->id)->delete();
            $pengaduan->delete();
        });

        return redirect()->route('admin.pengaduan.index')
            ->with('success', 'Pengaduan dan riwayat balasannya berhasil dihapus.');
    }

    private function visiblePengaduan()
    {
        $query = Pengaduan::query();
        $user = auth()->user();

        if ($user->role === 'uptup') {
            $query->where('id_uptup', $user->id_uptup)
                ->where('id_uptup', '!=', Pengaduan::BAPENDA_ID);
        }

        return $query;
    }
}
