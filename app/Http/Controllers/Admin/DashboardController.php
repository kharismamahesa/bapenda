<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitorLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->role === 'uptup') {
            return redirect()->route('admin.pengaduan.index');
        }

        return view('admin.dashboard', [
            'year' => today()->year,
            'summaryApiUrl' => route('admin.api.dashboard.summary'),
            'complaintApiUrl' => route('admin.api.pengaduan.monthly'),
        ]);
    }

    public function summary()
    {
        $today = today();
        $visitorToday = Carbon::today('Asia/Jakarta');
        $startDate = $today->copy()->startOfYear()->toDateString();
        $surveyQuery = DB::table('survei')
            ->where('created_date', '>=', $startDate)
            ->where('created_date', '<', $today->copy()->addDay()->toDateString())
            ->whereNull('spam_flagged_at');
        $averageExpressions = collect([
            'tanya1', 'tanya2', 'tanya3', 'tanya4', 'tanya5', 'tanya6', 'tanya7', 'tanya8', 'tanya9',
            'tanya21', 'tanya22', 'tanya23', 'tanya24', 'tanya25',
        ])->map(fn (string $field) => "AVG(`{$field}`) AS `{$field}`")->implode(', ');
        $averages = (array) (clone $surveyQuery)->selectRaw($averageExpressions)->first();
        $averageSkm = collect(array_slice(array_values($averages), 0, 9))
            ->filter(fn ($score) => $score !== null)
            ->avg();
        $averageSak = collect(array_slice(array_values($averages), 9))
            ->filter(fn ($score) => $score !== null)
            ->avg();
        $ikmValue = $averageSkm !== null ? round($averageSkm * 25, 2) : null;
        $sakValue = $averageSak !== null ? round($averageSak * 25, 2) : null;
        $visitorStats = VisitorLog::query()
            ->selectRaw(
                'SUM(CASE WHEN visited_date = ? THEN 1 ELSE 0 END) AS today_total, '.
                'SUM(CASE WHEN visited_date >= ? AND visited_date <= ? THEN 1 ELSE 0 END) AS month_total, '.
                'SUM(CASE WHEN visited_date >= ? AND visited_date <= ? THEN 1 ELSE 0 END) AS year_total',
                [
                    $visitorToday->toDateString(),
                    $visitorToday->copy()->startOfMonth()->toDateString(),
                    $visitorToday->toDateString(),
                    $visitorToday->copy()->startOfYear()->toDateString(),
                    $visitorToday->toDateString(),
                ]
            )
            ->first();
        $visitorMapMarkers = VisitorLog::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('city', 'region', 'country')
            ->selectRaw('AVG(latitude) AS lat, AVG(longitude) AS lng, COUNT(*) AS total')
            ->groupBy('city', 'region', 'country')
            ->orderByDesc('total')
            ->limit(500)
            ->get()
            ->map(fn (VisitorLog $visitor) => [
                'city' => $visitor->city ?: 'Tidak diketahui',
                'region' => $visitor->region ?: '',
                'country' => $visitor->country ?: '',
                'lat' => (float) $visitor->lat,
                'lng' => (float) $visitor->lng,
                'total' => (int) $visitor->total,
            ]);

        return response()->json([
            'totalSurvei' => (clone $surveyQuery)->count(),
            'totalBerita' => DB::table('berita')->count(),
            'totalVideo' => DB::table('video')->count(),
            'ikmValue' => $ikmValue,
            'ikmQuality' => $this->qualityFor($ikmValue),
            'sakValue' => $sakValue,
            'sakQuality' => $this->qualityFor($sakValue),
            'visitorToday' => (int) ($visitorStats->today_total ?? 0),
            'visitorMonth' => (int) ($visitorStats->month_total ?? 0),
            'visitorYear' => (int) ($visitorStats->year_total ?? 0),
            'visitorMapMarkers' => $visitorMapMarkers,
        ]);
    }

    public function monthlyComplaints()
    {
        $year = today()->year;
        $monthlyCounts = DB::table('pengaduan')
            ->whereNotNull('created_datetime')
            ->where('created_datetime', '>=', Carbon::create($year, 1, 1)->toDateString())
            ->where('created_datetime', '<', Carbon::create($year + 1, 1, 1)->toDateString())
            ->selectRaw('MONTH(created_datetime) AS bulan, COUNT(*) AS jumlah')
            ->groupByRaw('MONTH(created_datetime)')
            ->pluck('jumlah', 'bulan');
        $monthLabels = [];
        $monthValues = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthLabels[] = Carbon::create($year, $month, 1)->locale('id')->translatedFormat('M');
            $monthValues[] = (int) ($monthlyCounts[$month] ?? 0);
        }

        return response()->json([
            'success' => true,
            'year' => $year,
            'labels' => $monthLabels,
            'values' => $monthValues,
            'total' => array_sum($monthValues),
        ]);
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
