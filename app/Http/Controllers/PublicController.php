<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\HalamanKonten;
use App\Models\Pengaduan as PengaduanMasyarakat;
use App\Models\Regulasi;
use App\Models\RegulasiKategori;
use App\Models\Slider;
use App\Models\Survei;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class PublicController extends Controller
{
    public function beranda()
    {
        $berita = Berita::with('kategori')
            ->where('status', 1)
            ->orderByDesc('tanggal_berita')
            ->orderByDesc('id_berita')
            ->take(3)
            ->get();

        $profil = HalamanKonten::where('slug', 'profil')->first();
        $sliders = Schema::hasTable('slider')
            ? Slider::where('status', true)->orderBy('urutan')->orderBy('id')->get()
            : collect();
        $videos = collect();
        if (Schema::hasTable('video')) {
            foreach (Video::orderByDesc('id')->cursor() as $video) {
                $video->embed_url = Video::normalizeYoutubeUrl((string) $video->video);
                if ($video->embed_url !== null) {
                    $videos->push($video);
                }

                if ($videos->count() === 3) {
                    break;
                }
            }
        }

        return view('public.home', compact('berita', 'profil', 'sliders', 'videos'));
    }

    public function halaman($slug)
    {
        $halaman = HalamanKonten::where('slug', $slug)->firstOrFail();
        $halamanList = HalamanKonten::where('menu_parent', $halaman->menu_parent)
            ->orderBy('urutan')
            ->orderBy('judul')
            ->get();

        return view('public.halaman', compact('halaman', 'halamanList'));
    }

    public function beritaIndex()
    {
        $berita = Berita::with('kategori')
            ->where('status', 1)
            ->orderByDesc('tanggal_berita')
            ->orderByDesc('id_berita')
            ->paginate(9);

        return view('public.berita.index', compact('berita'));
    }

    public function videoIndex()
    {
        if (Schema::hasTable('video')) {
            $videos = Video::orderByDesc('id')
                ->paginate(9)
                ->through(function (Video $video) {
                    $video->embed_url = Video::normalizeYoutubeUrl((string) $video->video);

                    return $video;
                });
            $videos->setCollection(
                $videos->getCollection()
                    ->filter(fn (Video $video) => $video->embed_url !== null)
                    ->values()
            );
        } else {
            $videos = new LengthAwarePaginator([], 0, 9, 1, [
                'path' => route('video.index'),
            ]);
        }

        return view('public.video.index', compact('videos'));
    }

    public function pajakKendaraan()
    {
        return view('public.pajak-kendaraan');
    }

    public function survei()
    {
        $kantorPelayanan = DB::table('wilayah_samsat')
            ->select('KodeWilayah', 'NamaUPTUP', 'Status')
            ->orderBy('NamaWilayah')
            ->get();

        return view('public.survei.index', compact('kantorPelayanan'));
    }

    public function storeSurvei(Request $request)
    {
        if ($request->filled('website_url')) {
            return response()->json([
                'success' => true,
                'message' => 'Terima kasih telah berpartisipasi! Jawaban survei Anda berhasil tersimpan. Masukan Anda sangat berarti untuk membantu kami meningkatkan kualitas pelayanan Bapenda Riau.',
            ], 201);
        }

        $email = $request->input('email');
        if (is_string($email)) {
            $request->merge(['email' => Str::lower(trim($email)) ?: null]);
        }

        $questionFields = [
            'tanya1', 'tanya2', 'tanya3', 'tanya4', 'tanya5', 'tanya6', 'tanya7', 'tanya8', 'tanya9',
            'tanya21', 'tanya22', 'tanya23', 'tanya24', 'tanya25',
        ];
        $rules = [
            'id_uptup' => [
                'required',
                'string',
                'max:10',
                function ($attribute, $value, $fail) {
                    if ($value !== '10000' && ! DB::table('wilayah_samsat')->where('KodeWilayah', $value)->exists()) {
                        $fail('Kantor pelayanan yang dipilih tidak tersedia.');
                    }
                },
            ],
            'nama' => ['nullable', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:20000'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'usia' => ['required', 'integer', 'min:1', 'max:999'],
            'pendidikan' => ['required', 'in:SD,SLTP/SMP,SLTA/SMA,D1/D2/D3,S1,S2/S3'],
            'pekerjaan' => ['required', 'in:PNS/TNI/POLRI,PEGAWAI SWASTA,WIRASWASTA,MAHASISWA,LAINNYA'],
        ];
        foreach ($questionFields as $field) {
            $rules[$field] = ['required', 'integer', 'between:1,4'];
        }

        $validated = $request->validate($rules, [
            'id_uptup.required' => 'Silakan pilih kantor pelayanan.',
            'id_uptup.string' => 'Kantor pelayanan tidak valid.',
            'jenis_kelamin.required' => 'Silakan pilih jenis kelamin.',
            'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid.',
            'usia.required' => 'Usia wajib diisi.',
            'usia.integer' => 'Usia harus berupa angka.',
            'usia.min' => 'Usia harus lebih dari 0.',
            'usia.max' => 'Usia tidak valid.',
            'pendidikan.required' => 'Silakan pilih pendidikan terakhir.',
            'pendidikan.in' => 'Pilihan pendidikan tidak valid.',
            'pekerjaan.required' => 'Silakan pilih pekerjaan.',
            'pekerjaan.in' => 'Pilihan pekerjaan tidak valid.',
            'email.email' => 'Masukkan alamat email yang valid, contoh nama@email.com.',
            'email.max' => 'Email maksimal 255 karakter.',
            'tanya1.required' => 'Pertanyaan SKM 1 wajib dijawab.',
            'tanya2.required' => 'Pertanyaan SKM 2 wajib dijawab.',
            'tanya3.required' => 'Pertanyaan SKM 3 wajib dijawab.',
            'tanya4.required' => 'Pertanyaan SKM 4 wajib dijawab.',
            'tanya5.required' => 'Pertanyaan SKM 5 wajib dijawab.',
            'tanya6.required' => 'Pertanyaan SKM 6 wajib dijawab.',
            'tanya7.required' => 'Pertanyaan SKM 7 wajib dijawab.',
            'tanya8.required' => 'Pertanyaan SKM 8 wajib dijawab.',
            'tanya9.required' => 'Pertanyaan SKM 9 wajib dijawab.',
            'tanya21.required' => 'Pertanyaan SAK 1 wajib dijawab.',
            'tanya22.required' => 'Pertanyaan SAK 2 wajib dijawab.',
            'tanya23.required' => 'Pertanyaan SAK 3 wajib dijawab.',
            'tanya24.required' => 'Pertanyaan SAK 4 wajib dijawab.',
            'tanya25.required' => 'Pertanyaan SAK 5 wajib dijawab.',
            '*.integer' => 'Data wajib survei tidak valid.',
            '*.between' => 'Jawaban survei harus dipilih dari nilai yang tersedia.',
        ]);

        $isSuspectedDuplicate = false;
        if (! empty($validated['email'])) {
            $duplicateQuery = DB::table('survei')
                ->where('id_uptup', $validated['id_uptup'])
                ->whereRaw('LOWER(TRIM(email)) = ?', [$validated['email']])
                ->where('created_date', '>=', now()->subMinutes(10));

            foreach (['nama', 'no_hp', 'jenis_kelamin', 'usia', 'pendidikan', 'pekerjaan', 'alamat'] as $field) {
                $duplicateQuery->where($field, $validated[$field] ?? null);
            }

            foreach ($questionFields as $field) {
                $duplicateQuery->where($field, $validated[$field]);
            }

            $isSuspectedDuplicate = $duplicateQuery->exists();
        }

        $survei = Survei::create(array_merge($validated, [
            'created_date' => now(),
            'app' => 'website-public',
            'spam_flagged_at' => $isSuspectedDuplicate ? now() : null,
            'spam_reason' => $isSuspectedDuplicate ? 'duplikasi_email_jawaban_10_menit' : null,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih telah berpartisipasi! Jawaban survei Anda berhasil tersimpan. Masukan Anda sangat berarti untuk membantu kami meningkatkan kualitas pelayanan Bapenda Riau.',
            'id' => $survei->id_survei,
        ], 201);
    }

    public function pengaduan()
    {
        $kantorPelayanan = DB::table('wilayah_samsat')
            ->select('KodeWilayah', 'NamaUPTUP', 'Status')
            ->orderBy('NamaWilayah')
            ->get();

        return view('public.pengaduan.index', compact('kantorPelayanan'));
    }

    public function submitPengaduan(Request $request)
    {
        if ($request->filled('website_url')) {
            return redirect()->route('pengaduan.index')
                ->with('success', 'Pengaduan berhasil dikirim.');
        }

        $email = $request->input('email');
        if (is_string($email)) {
            $request->merge(['email' => Str::lower(trim($email))]);
        }

        $validated = $request->validate([
            'id_uptup' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if ((int) $value !== PengaduanMasyarakat::BAPENDA_ID
                        && ! DB::table('wilayah_samsat')->where('KodeWilayah', $value)->exists()) {
                        $fail('Kantor pelayanan yang dipilih tidak tersedia.');
                    }
                },
            ],
            'nama' => ['required', 'string', 'max:256'],
            'email' => ['required', 'email:rfc', 'max:256'],
            'no_hp' => ['nullable', 'string', 'max:15'],
            'aduan' => ['required', 'string', 'max:60000'],
        ], [
            'id_uptup.required' => 'Pilih kantor pelayanan tujuan.',
            'nama.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid, contoh nama@email.com.',
            'email.max' => 'Email maksimal 256 karakter.',
            'aduan.required' => 'Isi pengaduan wajib diisi.',
        ]);

        $normalizedAduan = Str::squish(Str::lower($validated['aduan']));
        $recentSimilarComplaints = PengaduanMasyarakat::query()
            ->where('email', $validated['email'])
            ->where('id_uptup', $validated['id_uptup'])
            ->where('created_datetime', '>=', now()->subMinutes(10))
            ->pluck('aduan');

        if ($recentSimilarComplaints->contains(
            fn (string $aduan) => Str::squish(Str::lower($aduan)) === $normalizedAduan
        )) {
            return back()
                ->withInput()
                ->withErrors(['aduan' => 'Pengaduan dengan isi yang sama baru saja dikirim. Silakan tunggu sebelum mengirim kembali.']);
        }

        $duplicateKey = 'public-complaint:'.hash(
            'sha256',
            $validated['email']."\0".$validated['id_uptup']."\0".$normalizedAduan
        );

        if (! Cache::add($duplicateKey, true, now()->addMinutes(10))) {
            return back()
                ->withInput()
                ->withErrors(['aduan' => 'Pengaduan dengan isi yang sama baru saja dikirim. Silakan tunggu sebelum mengirim kembali.']);
        }

        $createdAt = now();
        try {
            $pengaduan = DB::transaction(function () use ($validated, $createdAt) {
                $pengaduan = PengaduanMasyarakat::create([
                    'nama' => $validated['nama'],
                    'email' => $validated['email'],
                    'no_hp' => $validated['no_hp'] ?? null,
                    'aduan' => $validated['aduan'],
                    'id_uptup' => $validated['id_uptup'],
                    'created_datetime' => $createdAt,
                    'updated_datetime' => $createdAt,
                    'status' => 0,
                ]);

                $pengaduan->kode = 'P-'.$createdAt->format('Ymd').'-'.str_pad((string) $pengaduan->id, 7, '0', STR_PAD_LEFT);
                $pengaduan->save();

                return $pengaduan;
            });
        } catch (Throwable $exception) {
            Cache::forget($duplicateKey);
            throw $exception;
        }

        return redirect()->route('pengaduan.index')
            ->with('success', 'Pengaduan berhasil dikirim. Simpan kode pengaduan Anda: '.$pengaduan->kode);
    }

    public function regulasiIndex(Request $request)
    {
        $search = trim((string) $request->query('q'));
        $kategoriFilter = $request->query('kategori');

        $kategoriList = RegulasiKategori::withCount('regulasi')
            ->orderBy('regulasi_kategori', 'asc')
            ->get();

        $query = Regulasi::with('kategori');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('regulasi', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        if (is_numeric($kategoriFilter)) {
            $query->where('id_regulasi_kategori', (int) $kategoriFilter);
        }

        $regulasi = $query->orderByDesc('created_datetime')
            ->orderByDesc('id_regulasi')
            ->paginate(10)
            ->withQueryString();

        $activeKategori = is_numeric($kategoriFilter) ? $kategoriList->firstWhere('id_regulasi_kategori', (int) $kategoriFilter) : null;

        return view('public.regulasi.index', compact('regulasi', 'kategoriList', 'search', 'activeKategori'));
    }

    public function regulasiDownload($id)
    {
        $regulasi = Regulasi::findOrFail($id);

        if (! $regulasi->file) {
            abort(404, 'File regulasi tidak tersedia.');
        }

        if (preg_match('/^https?:\/\//i', $regulasi->file)) {
            return redirect()->away($regulasi->file);
        }

        $fileName = basename(str_replace('\\', '/', $regulasi->file));
        $path = public_path('uploads/regulasi/'.$fileName);

        if (! file_exists($path)) {
            $fallbackPath = public_path(ltrim(str_replace('\\', '/', $regulasi->file), '/'));

            if (file_exists($fallbackPath)) {
                $path = $fallbackPath;
                $fileName = basename($fallbackPath);
            } else {
                abort(404, 'File regulasi tidak ditemukan.');
            }
        }

        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $downloadName = Str::slug($regulasi->regulasi ?: 'regulasi');

        if ($extension !== '') {
            $downloadName .= '.'.$extension;
        }

        return response()->download($path, $downloadName);
    }

    public function pajakKendaraanApi(Request $request)
    {
        $input = [
            'nopol' => strtoupper(trim(preg_replace('/\s+/', ' ', (string) $request->query('nopol')))),
            'no_rangka' => strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $request->query('no_rangka'))),
        ];

        $validator = Validator::make($input, [
            'nopol' => ['nullable', 'string', 'max:15', 'regex:/^[A-Z]{1,2}\s*\d{1,4}\s*[A-Z]{0,3}$/'],
            'no_rangka' => ['nullable', 'string', 'max:20', 'regex:/^[A-Z0-9]{5,20}$/'],
        ], [
            'nopol.regex' => 'Format nomor polisi tidak valid. Contoh: BM 1234 XY.',
            'no_rangka.regex' => 'Format nomor rangka tidak valid (5-20 karakter alfanumerik).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $nopol = $input['nopol'];
        $noRangka = $input['no_rangka'];

        if ($nopol === '' && $noRangka === '') {
            return response()->json([
                'success' => false,
                'message' => 'Isi nomor polisi atau nomor rangka terlebih dahulu.',
            ], 422);
        }

        $na = '';
        $nb = '';
        $nc = '';

        if ($nopol !== '') {
            if (! preg_match('/^([A-Z]{1,2})\s*(\d{1,4})\s*([A-Z]{0,3})$/', $nopol, $matches)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format nomor polisi tidak valid. Contoh: BM 1234 XY.',
                ], 422);
            }

            $na = $matches[1];
            $nb = $matches[2];
            $nc = $matches[3] ?? '';
        }

        $endpoint = (string) config('services.bapenda.info_pajak_url');
        $apiKey = (string) config('services.bapenda.info_pajak_key');

        if ($endpoint === '' || $apiKey === '') {
            return response()->json([
                'success' => false,
                'message' => 'Layanan informasi pajak belum dikonfigurasi.',
            ], 500);
        }

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36',
                ])
                ->connectTimeout(5)
                ->timeout(15)
                ->post($endpoint, [
                    'api_key' => $apiKey,
                    'na' => $na,
                    'nb' => $nb,
                    'nc' => $nc,
                    'norangka' => $noRangka,
                ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan informasi pajak sedang tidak dapat diakses. Silakan coba beberapa saat lagi.',
            ], 503);
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            return response()->json([
                'success' => false,
                'message' => 'Respons layanan informasi pajak tidak valid.',
            ], 502);
        }

        $status = $payload['status'] ?? $payload['Status'] ?? null;
        $message = trim((string) ($payload['message'] ?? $payload['Message'] ?? ''));
        $rawData = $payload['data'] ?? $payload['Data'] ?? null;

        if (is_array($rawData) && isset($rawData[0]) && is_array($rawData[0])) {
            $rawData = $rawData[0];
        }

        $statusOk = $status !== false
            && ! (is_numeric($status) && (int) $status >= 400);

        $hasData = is_array($rawData) && $rawData !== [];

        if (! $response->successful() || ! $hasData || ! $statusOk) {
            $upstreamStatus = (int) $response->status();
            $code = ($upstreamStatus === 404 || (string) $status === '404') ? 404 : 502;

            return response()->json([
                'success' => false,
                'message' => $message !== '' ? $message : 'Data kendaraan tidak ditemukan.',
            ], $code);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data pajak kendaraan ditemukan.',
            'data' => $this->normalizeInfoPajak($rawData, $nopol, $noRangka),
        ])->header('Cache-Control', 'no-store, max-age=0');
    }

    private function normalizeInfoPajak(array $rawData, string $nopolInput, string $noRangkaInput): array
    {
        $data = [];

        foreach ($rawData as $key => $value) {
            $data[$this->normalizeInfoPajakKey((string) $key)] = $value;
        }

        $num = function (array $keys) use ($data) {
            return $this->toInfoPajakNumber($this->firstInfoPajakValue($data, $keys));
        };

        $nopol = $this->firstInfoPajakValue($data, ['nopol', 'nomorpolisi', 'nopolis']);
        $noRangka = $this->firstInfoPajakValue($data, ['norangka', 'nomorrangka']);
        $noMesin = $this->firstInfoPajakValue($data, ['nomesin', 'nomormesin']);
        $merk = $this->firstInfoPajakValue($data, ['merkkendaraan', 'merk']);
        $tipe = $this->firstInfoPajakValue($data, ['typekendaraan', 'tipe', 'tipekendaraan']);
        $model = $this->firstInfoPajakValue($data, ['namamodel', 'model']);
        $tahun = $this->firstInfoPajakValue($data, ['tahunpembuatan', 'tahun']);
        $warna = $this->firstInfoPajakValue($data, ['warnakendaraan', 'warna']);
        $warnaTnkb = $this->firstInfoPajakValue($data, ['warnatnkb']);
        $jenis = $this->firstInfoPajakValue($data, ['namajenis', 'jeniskendaraan', 'jenis']);
        $golongan = $this->firstInfoPajakValue($data, ['golongankendaraan', 'golongan']);
        $jatuhTempo = $this->firstInfoPajakValue($data, ['tanggaljatuhtempo', 'tanggalakhirpajak', 'jatuhtempo']);
        $lamaTunggakan = $this->firstInfoPajakValue($data, ['lamatunggakan']);
        $statusPajak = $this->firstInfoPajakValue($data, ['statuspajak', 'statuskendaraan']);

        $tanggalBayar = $this->firstInfoPajakValue($data, ['tanggalpembayaransebelumnya', 'tanggalbayarsebelumnya']);
        $totalSebelumnya = $num(['totalsebelumnya']);
        $pokokPkbSebelumnya = $num(['pokokpkbsebelumnya']);
        $dendaPkbSebelumnya = $num(['dendapkbsebelumnya']);
        $pokokPkbOpsenSebelumnya = $num(['pokokpkbopsensebelumnya']);
        $dendaPkbOpsenSebelumnya = $num(['dendapkbopsensebelumnya']);
        $pokokBbnkbSebelumnya = $num(['pokokbbnkbsebelumnya']);
        $dendaBbnkbSebelumnya = $num(['dendabbnkbsebelumnya']);
        $pokokBbnkbOpsenSebelumnya = $num(['pokokbbnkbopsensebelumnya']);
        $dendaBbnkbOpsenSebelumnya = $num(['dendabbnkbopsensebelumnya']);
        $pokokSwdklljSebelumnya = $num(['pokokswdksebelumnya', 'pokokswdklljsebelumnya']);
        $dendaSwdklljSebelumnya = $num(['dendaswdksebelumnya', 'dendaswdklljsebelumnya']);
        $stnkSebelumnya = $num(['stnksebelumnya']);
        $tnkbSebelumnya = $num(['tnkbsebelumnya']);

        $pokokPkb = $num(['totalpokokpkb', 'pokokpkb']);
        $dendaPkb = $num(['totaldendapkb', 'dendapkb']);
        $pokokPkbOpsen = $num(['totalpokokpkbopsen', 'pokokpkbopsen']);
        $dendaPkbOpsen = $num(['totaldendapkbopsen', 'dendapkbopsen']);
        $pokokBbnkb = $num(['tarifbbnkb', 'totalpokokbbnkb']);
        $dendaBbnkb = $num(['totaldendabbnkb', 'dendabbnkb']);
        $pokokBbnkbOpsen = $num(['tarifbbnkbopsen', 'totalpokokbbnkbopsen']);
        $dendaBbnkbOpsen = $num(['totaldendabbnkbopsen', 'dendabbnkbopsen']);
        $pokokSwdkllj = $num(['totalpokokswdk', 'pokokswdk', 'pokokswdkllj']);
        $dendaSwdkllj = $num(['totaldendaswdk', 'dendaswdk', 'dendaswdkllj']);
        $stnk = $num(['tarifstnk', 'stnk']);
        $tnkb = $num(['tariftnkb', 'tnkb']);
        $totalBayar = $num(['totalpajakkendaraan', 'totalbayar', 'total']);

        if ($totalBayar <= 0) {
            $totalBayar = $pokokPkb + $dendaPkb
                + $pokokPkbOpsen + $dendaPkbOpsen
                + $pokokBbnkb + $dendaBbnkb
                + $pokokBbnkbOpsen + $dendaBbnkbOpsen
                + $pokokSwdkllj + $dendaSwdkllj
                + $stnk + $tnkb;
        }

        if (($statusPajak === null || $statusPajak === '') && $jatuhTempo) {
            try {
                $statusPajak = Carbon::parse($jatuhTempo)->isPast() ? 'Menunggak' : 'Aktif';
            } catch (Throwable $e) {
                $statusPajak = 'Terdata';
            }
        }

        $formatDueDate = $this->formatInfoPajakDueDateData($jatuhTempo);

        return [
            'nopol' => $nopol ?: ($nopolInput !== '' ? $nopolInput : '-'),
            'merk' => $merk ?: '-',
            'tipe' => $tipe ?: '-',
            'model' => $model ?: '-',
            'tahun' => $tahun ?: '-',
            'warna' => $warna ?: '-',
            'warna_tnkb' => $warnaTnkb ?: '-',
            'jenis_kendaraan' => $jenis ?: '-',
            'golongan_kendaraan' => $golongan ?: '-',
            'no_rangka' => $noRangka ?: ($noRangkaInput !== '' ? $noRangkaInput : '-'),
            'no_mesin' => $noMesin ?: '-',
            'status_pajak' => $statusPajak ?: 'Terdata',
            'status_kendaraan' => $statusPajak ?: 'Terdata',
            'tanggal_jatuh_tempo' => $formatDueDate['full'],
            'tanggal_jatuh_tempo_formatted' => $formatDueDate['formatted'],
            'estimasi_jatuh_tempo' => $formatDueDate['estimasi'],
            'status_jatuh_tempo' => $formatDueDate['status'],
            'lama_tunggakan' => $lamaTunggakan ?: '0 Hari 0 Bulan 0 Tahun',
            'tanggal_bayar_sebelumnya' => $this->formatInfoPajakDate($tanggalBayar),
            'total_sebelumnya' => $totalSebelumnya,
            'pokok_pkb_sebelumnya' => $pokokPkbSebelumnya,
            'denda_pkb_sebelumnya' => $dendaPkbSebelumnya,
            'pokok_pkb_opsen_sebelumnya' => $pokokPkbOpsenSebelumnya,
            'denda_pkb_opsen_sebelumnya' => $dendaPkbOpsenSebelumnya,
            'pokok_bbnkb_sebelumnya' => $pokokBbnkbSebelumnya,
            'denda_bbnkb_sebelumnya' => $dendaBbnkbSebelumnya,
            'pokok_bbnkb_opsen_sebelumnya' => $pokokBbnkbOpsenSebelumnya,
            'denda_bbnkb_opsen_sebelumnya' => $dendaBbnkbOpsenSebelumnya,
            'pokok_swdkllj_sebelumnya' => $pokokSwdklljSebelumnya,
            'denda_swdkllj_sebelumnya' => $dendaSwdklljSebelumnya,
            'stnk_sebelumnya' => $stnkSebelumnya,
            'tnkb_sebelumnya' => $tnkbSebelumnya,
            'pokok_pkb' => $pokokPkb,
            'denda_pkb' => $dendaPkb,
            'pokok_pkb_opsen' => $pokokPkbOpsen,
            'denda_pkb_opsen' => $dendaPkbOpsen,
            'pokok_bbnkb' => $pokokBbnkb,
            'denda_bbnkb' => $dendaBbnkb,
            'pokok_bbnkb_opsen' => $pokokBbnkbOpsen,
            'denda_bbnkb_opsen' => $dendaBbnkbOpsen,
            'pokok_swdkllj' => $pokokSwdkllj,
            'denda_swdkllj' => $dendaSwdkllj,
            'stnk' => $stnk,
            'tnkb' => $tnkb,
            'total_bayar' => $totalBayar,
            'sumber' => 'Bapenda Provinsi Riau',
            'diperbarui' => now()->locale('id')->translatedFormat('d F Y H:i'),
        ];
    }

    private function normalizeInfoPajakKey(string $key): string
    {
        return strtolower(preg_replace('/[^A-Za-z0-9]/', '', $key));
    }

    private function firstInfoPajakValue(array $data, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && $data[$key] !== null && $data[$key] !== '') {
                return $data[$key];
            }
        }

        foreach ($data as $dataKey => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            foreach ($keys as $key) {
                if (str_contains($dataKey, $key)) {
                    return $value;
                }
            }
        }

        return null;
    }

    private function toInfoPajakNumber($value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $value = preg_replace('/[^0-9,\.\-]/', '', trim((string) $value));

        if ($value === '' || $value === '-') {
            return 0;
        }

        if (str_contains($value, ',')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } else {
            $parts = explode('.', $value);

            if (count($parts) > 2) {
                $value = implode('', $parts);
            } elseif (count($parts) === 2) {
                [$integer, $decimal] = $parts;

                if ($integer !== '' && $integer !== '0' && strlen($decimal) === 3) {
                    $value = $integer.$decimal;
                }
            }
        }

        return (float) $value;
    }

    private function parseIndonesianDate($value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        $val = trim((string) $value);

        if ($val === '' || $val === '-') {
            return null;
        }

        $months = [
            'januari' => '01',
            'jan' => '01',
            'februari' => '02',
            'feb' => '02',
            'maret' => '03',
            'mar' => '03',
            'april' => '04',
            'apr' => '04',
            'mei' => '05',
            'may' => '05',
            'juni' => '06',
            'jun' => '06',
            'juli' => '07',
            'jul' => '07',
            'agustus' => '08',
            'agu' => '08',
            'agt' => '08',
            'aug' => '08',
            'september' => '09',
            'sep' => '09',
            'oktober' => '10',
            'okt' => '10',
            'oct' => '10',
            'november' => '11',
            'nov' => '11',
            'desember' => '12',
            'des' => '12',
            'dec' => '12',
        ];

        $clean = preg_replace('/[,\.]/', ' ', strtolower($val));
        foreach ($months as $name => $num) {
            $clean = preg_replace('/\b'.$name.'\b/', $num, $clean);
        }
        $clean = preg_replace('/\s+/', '-', trim($clean));
        $clean = str_replace('/', '-', $clean);

        if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $clean, $m)) {
            try {
                return Carbon::createFromDate((int) $m[3], (int) $m[2], (int) $m[1])->startOfDay();
            } catch (Throwable $e) {
            }
        }

        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $clean, $m)) {
            try {
                return Carbon::createFromDate((int) $m[1], (int) $m[2], (int) $m[3])->startOfDay();
            } catch (Throwable $e) {
            }
        }

        try {
            return Carbon::parse($clean)->startOfDay();
        } catch (Throwable $e) {
        }

        try {
            return Carbon::parse($val)->startOfDay();
        } catch (Throwable $e) {
        }

        return null;
    }

    private function formatInfoPajakDate($value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        $date = $this->parseIndonesianDate($value);

        if ($date !== null) {
            return $date->locale('id')->translatedFormat('d F Y');
        }

        return trim((string) $value);
    }

    private function formatInfoPajakDueDateData($value): array
    {
        if ($value === null || $value === '') {
            return [
                'full' => '-',
                'formatted' => '-',
                'estimasi' => '',
                'status' => 'unknown',
            ];
        }

        $date = $this->parseIndonesianDate($value);

        if ($date === null) {
            $val = trim((string) $value);

            return [
                'full' => $val,
                'formatted' => $val,
                'estimasi' => '',
                'status' => 'unknown',
            ];
        }

        $today = now()->startOfDay();
        $formatted = $date->locale('id')->translatedFormat('d F Y');

        if ($date->equalTo($today)) {
            return [
                'full' => $formatted.' (Hari ini)',
                'formatted' => $formatted,
                'estimasi' => 'Hari ini (Jatuh tempo)',
                'status' => 'today',
            ];
        }

        $isPast = $date->isPast();
        $diff = $isPast ? $date->diff($today) : $today->diff($date);
        $totalDays = (int) $today->diffInDays($date);

        $parts = [];
        if ($diff->y > 0) {
            $parts[] = $diff->y.' tahun';
        }
        if ($diff->m > 0) {
            $parts[] = $diff->m.' bulan';
        }
        if ($diff->d > 0) {
            $parts[] = $diff->d.' hari';
        }

        if (empty($parts)) {
            $parts[] = '0 hari';
        }

        $duration = implode(' ', $parts);

        if ($isPast) {
            $estimasi = "Lewat {$duration} ({$totalDays} hari lalu)";

            return [
                'full' => "{$formatted} ({$estimasi})",
                'formatted' => $formatted,
                'estimasi' => $estimasi,
                'status' => 'past',
            ];
        }

        $estimasi = "Sisa {$duration} ({$totalDays} hari lagi)";

        return [
            'full' => "{$formatted} ({$estimasi})",
            'formatted' => $formatted,
            'estimasi' => $estimasi,
            'status' => 'future',
        ];
    }

    private function formatInfoPajakDueDate($value): string
    {
        return $this->formatInfoPajakDueDateData($value)['full'];
    }

    public function beritaShow($slug)
    {
        $berita = Berita::with('kategori')
            ->where('status', 1)
            ->where(function ($query) use ($slug) {
                $query->where('url', $slug);

                if (is_numeric($slug)) {
                    $query->orWhere('id_berita', (int) $slug);
                }
            })
            ->firstOrFail();

        $berita->increment('counter');

        $beritaTerkait = Berita::with('kategori')
            ->where('status', 1)
            ->where('id_berita', '!=', $berita->id_berita)
            ->orderByDesc('tanggal_berita')
            ->orderByDesc('id_berita')
            ->take(3)
            ->get();

        return view('public.berita.show', compact('berita', 'beritaTerkait'));
    }
}
