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
        $visiblePengaduan = $this->visiblePengaduan();
        $totalPengaduan = (clone $visiblePengaduan)->count();
        $pengaduanSudahDibalas = (clone $visiblePengaduan)->whereHas('balasan')->count();
        $pengaduanBelumDibalas = (clone $visiblePengaduan)->whereDoesntHave('balasan')->count();

        $pengaduan = $visiblePengaduan
            ->withCount('balasan')
            ->orderByDesc('created_datetime')
            ->paginate(15);

        $kantorById = DB::table('wilayah_samsat')
            ->pluck('NamaUPTUP', 'KodeWilayah');
        $user = auth()->user();
        $scopeLabel = $user->role === 'uptup'
            ? 'UPT '.($kantorById->get($user->id_uptup) ?? 'ID '.$user->id_uptup)
            : 'Semua kantor pelayanan, termasuk Bapenda Provinsi Riau';

        return view('admin.pengaduan.index', compact(
            'pengaduan',
            'kantorById',
            'scopeLabel',
            'totalPengaduan',
            'pengaduanSudahDibalas',
            'pengaduanBelumDibalas'
        ));
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
