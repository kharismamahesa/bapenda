<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SurveiController extends Controller
{
    private const QUESTION_FIELDS = [
        'tanya1', 'tanya2', 'tanya3', 'tanya4', 'tanya5', 'tanya6', 'tanya7', 'tanya8', 'tanya9',
        'tanya21', 'tanya22', 'tanya23', 'tanya24', 'tanya25',
    ];

    public function index(Request $request)
    {
        $filters = $this->validatedFilters($request);
        $startDate = $filters['start_date'] ?? today()->startOfYear()->toDateString();
        $endDate = $filters['end_date'] ?? today()->toDateString();
        $selectedOffice = $filters['id_uptup'] ?? '';
        $offices = DB::table('wilayah_samsat')
            ->where('KodeWilayah', '<>', '10000')
            ->select('KodeWilayah', 'NamaUPTUP')
            ->orderBy('NamaUPTUP')
            ->get();

        return view('admin.survei.index', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedOffice' => $selectedOffice,
            'offices' => $offices,
            'dashboardDataUrl' => route('admin.api.survei.dashboard'),
            'canReviewSpam' => auth()->user()->role === 'superadmin',
        ]);
    }

    public function dashboardData(Request $request)
    {
        $filters = $this->validatedFilters($request);
        $startDate = $filters['start_date'] ?? today()->startOfYear()->toDateString();
        $endDate = $filters['end_date'] ?? today()->toDateString();
        $selectedOffice = $filters['id_uptup'] ?? '';
        $endExclusive = Carbon::parse($endDate)->addDay()->toDateString();

        $filteredSurvei = fn () => DB::table('survei')
            ->where('created_date', '>=', $startDate)
            ->where('created_date', '<', $endExclusive)
            ->whereNull('spam_flagged_at')
            ->when($selectedOffice !== '', fn ($query) => $query->where('id_uptup', $selectedOffice));
        $totalSurvei = $filteredSurvei()->count();
        $surveiHariIni = $filteredSurvei()
            ->where('created_date', '>=', today()->toDateString())
            ->where('created_date', '<', today()->addDay()->toDateString())
            ->count();
        $suspectedSpam = auth()->user()->role === 'superadmin'
            ? DB::table('survei')
                ->where('created_date', '>=', $startDate)
                ->where('created_date', '<', $endExclusive)
                ->whereNotNull('spam_flagged_at')
                ->when($selectedOffice !== '', fn ($query) => $query->where('id_uptup', $selectedOffice))
                ->count()
            : null;

        $averageExpressions = collect(self::QUESTION_FIELDS)
            ->map(fn (string $field) => "AVG(`{$field}`) AS `{$field}`")
            ->implode(', ');
        $averageRow = DB::table('survei')
            ->where('created_date', '>=', $startDate)
            ->where('created_date', '<', $endExclusive)
            ->whereNull('spam_flagged_at')
            ->when($selectedOffice !== '', fn ($query) => $query->where('id_uptup', $selectedOffice))
            ->selectRaw($averageExpressions)
            ->first();
        $rawQuestionAverages = collect(self::QUESTION_FIELDS)
            ->map(fn (string $field) => $averageRow->{$field} !== null ? (float) $averageRow->{$field} : null)
            ->all();
        $questionAverages = collect($rawQuestionAverages)
            ->map(fn (?float $average) => round($average ?? 0, 2))
            ->all();

        $periodStart = today()->subDays(29);
        $periodEnd = today();
        $dailyCounts = DB::table('survei')
            ->where('created_date', '>=', $periodStart->toDateString())
            ->where('created_date', '<', $periodEnd->copy()->addDay()->toDateString())
            ->whereNull('spam_flagged_at')
            ->selectRaw('DATE(created_date) AS tanggal, COUNT(*) AS jumlah')
            ->groupByRaw('DATE(created_date)')
            ->orderBy('tanggal')
            ->pluck('jumlah', 'tanggal');
        $dailyLabels = [];
        $dailyValues = [];
        foreach (CarbonPeriod::create($periodStart, $periodEnd) as $date) {
            $key = $date->toDateString();
            $dailyLabels[] = $date->format('d M');
            $dailyValues[] = (int) ($dailyCounts[$key] ?? 0);
        }

        $genderCounts = $filteredSurvei()
            ->select('jenis_kelamin')
            ->selectRaw('COUNT(*) AS jumlah')
            ->groupBy('jenis_kelamin')
            ->pluck('jumlah', 'jenis_kelamin');
        $genderLabels = ['Laki-laki', 'Perempuan'];
        $genderValues = [(int) ($genderCounts['L'] ?? 0), (int) ($genderCounts['P'] ?? 0)];

        $educationCounts = $filteredSurvei()
            ->selectRaw("COALESCE(NULLIF(TRIM(TRAILING CHAR(39) FROM TRIM(pendidikan)), ''), 'Tidak diisi') AS kategori_pendidikan")
            ->selectRaw('COUNT(*) AS jumlah')
            ->groupBy('kategori_pendidikan')
            ->orderByDesc('jumlah')
            ->get();
        $ageCounts = $filteredSurvei()
            ->whereNotNull('usia')
            ->selectRaw("CASE WHEN usia <= 19 THEN '0-19' WHEN usia <= 59 THEN '20-59' ELSE '60-seterusnya' END AS rentang_usia")
            ->selectRaw('COUNT(*) AS jumlah')
            ->groupBy('rentang_usia')
            ->pluck('jumlah', 'rentang_usia');
        $ageLabels = ['0-19', '20-59', '60-seterusnya'];
        $occupationCounts = $filteredSurvei()
            ->selectRaw("COALESCE(NULLIF(TRIM(TRAILING CHAR(39) FROM TRIM(pekerjaan)), ''), 'Tidak diisi') AS kategori_pekerjaan")
            ->selectRaw('COUNT(*) AS jumlah')
            ->groupBy('kategori_pekerjaan')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get();

        $skmScore = collect($rawQuestionAverages)->take(9)->filter(fn ($average) => $average !== null)->avg();
        $sakScore = collect($rawQuestionAverages)->slice(9)->filter(fn ($average) => $average !== null)->avg();
        $averageSkm = $skmScore !== null ? round($skmScore, 2) : null;
        $averageSak = $sakScore !== null ? round($sakScore * 25, 2) : null;
        $ikmValue = $skmScore !== null ? round($skmScore * 25, 2) : null;

        return response()->json([
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedOffice' => $selectedOffice,
            'totalSurvei' => $totalSurvei,
            'surveiHariIni' => $surveiHariIni,
            'suspectedSpam' => $suspectedSpam,
            'averageSkm' => $averageSkm,
            'averageSak' => $averageSak,
            'ikmValue' => $ikmValue,
            'ikmQuality' => $this->qualityFor($ikmValue),
            'sakQuality' => $this->qualityFor($averageSak),
            'skmQuestionAverages' => array_slice($questionAverages, 0, 9),
            'sakQuestionAverages' => array_slice($questionAverages, 9),
            'dailyLabels' => $dailyLabels,
            'dailyValues' => $dailyValues,
            'genderLabels' => $genderLabels,
            'genderValues' => $genderValues,
            'educationLabels' => $educationCounts->pluck('kategori_pendidikan')->all(),
            'educationValues' => $educationCounts->pluck('jumlah')->map(fn ($count) => (int) $count)->all(),
            'ageLabels' => $ageLabels,
            'ageValues' => array_map(fn (string $range) => (int) ($ageCounts[$range] ?? 0), $ageLabels),
            'occupationLabels' => $occupationCounts->pluck('kategori_pekerjaan')->all(),
            'occupationValues' => $occupationCounts->pluck('jumlah')->map(fn ($count) => (int) $count)->all(),
        ]);
    }

    public function download(Request $request): StreamedResponse
    {
        $filters = $this->validatedFilters($request);
        $startDate = $filters['start_date'] ?? today()->startOfYear()->toDateString();
        $endDate = $filters['end_date'] ?? today()->toDateString();
        $selectedOffice = $filters['id_uptup'] ?? '';
        $isSuperadmin = auth()->user()->role === 'superadmin';
        $query = DB::table('survei')
            ->leftJoin('wilayah_samsat', 'survei.id_uptup', '=', 'wilayah_samsat.KodeWilayah')
            ->whereDate('survei.created_date', '>=', $startDate)
            ->whereDate('survei.created_date', '<=', $endDate)
            ->when(! $isSuperadmin, fn ($query) => $query->whereNull('survei.spam_flagged_at'))
            ->when($selectedOffice !== '', fn ($query) => $query->where('survei.id_uptup', $selectedOffice))
            ->select([
                'survei.id_survei',
                'survei.no_hp',
                'survei.email',
                'survei.alamat',
                'survei.nama',
                'survei.jenis_kelamin',
                'survei.usia',
                'survei.pendidikan',
                'survei.pekerjaan',
                'survei.id_uptup',
                'survei.tanya1',
                'survei.tanya2',
                'survei.tanya3',
                'survei.tanya4',
                'survei.tanya5',
                'survei.tanya6',
                'survei.tanya7',
                'survei.tanya8',
                'survei.tanya9',
                'survei.tanya21',
                'survei.tanya22',
                'survei.tanya23',
                'survei.tanya24',
                'survei.tanya25',
                'survei.created_date',
                'survei.app',
                'survei.spam_flagged_at',
                'survei.spam_reason',
            ])
            ->selectRaw("CASE WHEN survei.id_uptup = '10000' THEN 'BAPENDA PROVINSI RIAU' ELSE COALESCE(wilayah_samsat.NamaUPTUP, CONCAT('Kantor ', survei.id_uptup)) END AS nama_uptup")
            ->orderBy('survei.created_date')
            ->orderBy('survei.id_survei');

        return response()->streamDownload(function () use ($query) {
            $output = fopen('php://output', 'w');
            if ($output === false) {
                throw new \RuntimeException('Tidak dapat membuka aliran unduhan survei.');
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                'id_survei', 'no_hp', 'email', 'alamat', 'nama', 'jenis_kelamin', 'usia',
                'pendidikan', 'pekerjaan', 'id_uptup',
                'tanya1', 'tanya2', 'tanya3', 'tanya4', 'tanya5', 'tanya6', 'tanya7', 'tanya8', 'tanya9',
                'tanya21', 'tanya22', 'tanya23', 'tanya24', 'tanya25', 'created_date', 'app',
                'spam_flagged_at', 'spam_reason', 'nama_uptup',
            ]);

            foreach ($query->cursor() as $row) {
                $values = (array) $row;
                fputcsv($output, array_map(fn ($value) => $this->safeCsvValue($value), array_values($values)));
            }

            fclose($output);
        }, "data-survei-{$startDate}-sampai-{$endDate}.csv", [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function review(Request $request)
    {
        $filters = $this->validatedFilters($request);
        $startDate = $filters['start_date'] ?? today()->startOfYear()->toDateString();
        $endDate = $filters['end_date'] ?? today()->toDateString();
        $selectedOffice = $filters['id_uptup'] ?? '';

        $survei = DB::table('survei')
            ->leftJoin('wilayah_samsat', 'survei.id_uptup', '=', 'wilayah_samsat.KodeWilayah')
            ->whereNotNull('survei.spam_flagged_at')
            ->whereDate('survei.created_date', '>=', $startDate)
            ->whereDate('survei.created_date', '<=', $endDate)
            ->when($selectedOffice !== '', fn ($query) => $query->where('survei.id_uptup', $selectedOffice))
            ->select([
                'survei.id_survei',
                'survei.no_hp',
                'survei.email',
                'survei.alamat',
                'survei.nama',
                'survei.jenis_kelamin',
                'survei.usia',
                'survei.pendidikan',
                'survei.pekerjaan',
                'survei.id_uptup',
                'survei.tanya1',
                'survei.tanya2',
                'survei.tanya3',
                'survei.tanya4',
                'survei.tanya5',
                'survei.tanya6',
                'survei.tanya7',
                'survei.tanya8',
                'survei.tanya9',
                'survei.tanya21',
                'survei.tanya22',
                'survei.tanya23',
                'survei.tanya24',
                'survei.tanya25',
                'survei.created_date',
                'survei.spam_flagged_at',
                'survei.spam_reason',
            ])
            ->selectRaw("CASE WHEN survei.id_uptup = '10000' THEN 'BAPENDA PROVINSI RIAU' ELSE COALESCE(wilayah_samsat.NamaUPTUP, CONCAT('Kantor ', survei.id_uptup)) END AS nama_uptup")
            ->orderByDesc('survei.spam_flagged_at')
            ->orderByDesc('survei.id_survei')
            ->paginate(15)
            ->withQueryString();

        $offices = DB::table('wilayah_samsat')
            ->where('KodeWilayah', '<>', '10000')
            ->select('KodeWilayah', 'NamaUPTUP')
            ->orderBy('NamaUPTUP')
            ->get();

        return view('admin.survei.review', compact(
            'survei',
            'startDate',
            'endDate',
            'selectedOffice',
            'offices'
        ));
    }

    public function restore(int $id)
    {
        $restored = DB::table('survei')
            ->where('id_survei', $id)
            ->whereNotNull('spam_flagged_at')
            ->update([
                'spam_flagged_at' => null,
                'spam_reason' => null,
            ]);

        if ($restored !== 1) {
            abort(404);
        }

        return back()->with('success', 'Data survei dipulihkan dan akan dihitung kembali sebagai responden valid.');
    }

    public function destroyFlagged(int $id)
    {
        $deleted = DB::table('survei')
            ->where('id_survei', $id)
            ->whereNotNull('spam_flagged_at')
            ->delete();

        if ($deleted !== 1) {
            abort(404);
        }

        return back()->with('success', 'Data survei yang ditandai berhasil dihapus permanen.');
    }

    private function validatedFilters(Request $request): array
    {
        $validator = Validator::make($request->query(), [
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d'],
            'id_uptup' => ['nullable', 'string', 'max:10'],
        ]);
        $validator->after(function ($validator) use ($request) {
            $officeId = $request->query('id_uptup');
            if (is_string($officeId) && $officeId !== '' && $officeId !== '10000'
                && ! DB::table('wilayah_samsat')->where('KodeWilayah', $officeId)->exists()) {
                $validator->errors()->add('id_uptup', 'Kantor pelayanan yang dipilih tidak ditemukan.');
            }

            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            if (! $validator->errors()->has('start_date') && ! $validator->errors()->has('end_date')
                && is_string($startDate) && is_string($endDate) && $startDate !== '' && $endDate !== ''
                && Carbon::parse($startDate)->gt(Carbon::parse($endDate))) {
                $validator->errors()->add('end_date', 'Tanggal akhir tidak boleh sebelum tanggal mulai.');
            }
        });
        return $validator->validate();
    }

    private function safeCsvValue(mixed $value): mixed
    {
        if (! is_string($value) || $value === '') {
            return $value;
        }

        return preg_match('/^[\s]*[=+\-@]/u', $value) ? "'".$value : $value;
    }

    private function qualityFor(?float $value): ?array
    {
        return match (true) {
            $value === null => null,
            $value <= 43.75 => ['mutu' => 'D', 'kinerja' => 'Tidak baik'],
            $value <= 62.50 => ['mutu' => 'C', 'kinerja' => 'Kurang baik'],
            $value <= 81.25 => ['mutu' => 'B', 'kinerja' => 'Baik'],
            default => ['mutu' => 'A', 'kinerja' => 'Sangat baik'],
        };
    }
}
