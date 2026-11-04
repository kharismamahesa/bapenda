<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\HalamanKonten;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
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

        return view('public.home', compact('berita', 'profil'));
    }

    public function halaman($slug)
    {
        $halaman = HalamanKonten::where('slug', $slug)->firstOrFail();
        $halamanList = HalamanKonten::orderBy('id')->get();

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

    public function pajakKendaraan()
    {
        return view('public.pajak-kendaraan');
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
            if (!preg_match('/^([A-Z]{1,2})\s*(\d{1,4})\s*([A-Z]{0,3})$/', $nopol, $matches)) {
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

        if (!is_array($payload)) {
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
            && !(is_numeric($status) && (int) $status >= 400);

        $hasData = is_array($rawData) && $rawData !== [];

        if (!$response->successful() || !$hasData || !$statusOk) {
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
                    $value = $integer . $decimal;
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
            'januari' => '01', 'jan' => '01',
            'februari' => '02', 'feb' => '02',
            'maret' => '03', 'mar' => '03',
            'april' => '04', 'apr' => '04',
            'mei' => '05', 'may' => '05',
            'juni' => '06', 'jun' => '06',
            'juli' => '07', 'jul' => '07',
            'agustus' => '08', 'agu' => '08', 'agt' => '08', 'aug' => '08',
            'september' => '09', 'sep' => '09',
            'oktober' => '10', 'okt' => '10', 'oct' => '10',
            'november' => '11', 'nov' => '11',
            'desember' => '12', 'des' => '12', 'dec' => '12',
        ];

        $clean = preg_replace('/[,\.]/', ' ', strtolower($val));
        foreach ($months as $name => $num) {
            $clean = preg_replace('/\b' . $name . '\b/', $num, $clean);
        }
        $clean = preg_replace('/\s+/', '-', trim($clean));
        $clean = str_replace('/', '-', $clean);

        if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $clean, $m)) {
            try {
                return Carbon::createFromDate((int) $m[3], (int) $m[2], (int) $m[1])->startOfDay();
            } catch (Throwable $e) {}
        }

        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $clean, $m)) {
            try {
                return Carbon::createFromDate((int) $m[1], (int) $m[2], (int) $m[3])->startOfDay();
            } catch (Throwable $e) {}
        }

        try {
            return Carbon::parse($clean)->startOfDay();
        } catch (Throwable $e) {}

        try {
            return Carbon::parse($val)->startOfDay();
        } catch (Throwable $e) {}

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
                'full' => $formatted . ' (Hari ini)',
                'formatted' => $formatted,
                'estimasi' => 'Hari ini (Jatuh tempo)',
                'status' => 'today',
            ];
        }

        $isPast = $date->isPast();
        $diff = $isPast ? $date->diff($today) : $today->diff($date);
        $totalDays = (int) $today->diffInDays($date);

        $parts = [];
        if ($diff->y > 0) $parts[] = $diff->y . ' tahun';
        if ($diff->m > 0) $parts[] = $diff->m . ' bulan';
        if ($diff->d > 0) $parts[] = $diff->d . ' hari';

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
