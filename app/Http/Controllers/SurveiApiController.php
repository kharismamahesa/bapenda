<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SurveiApiController extends Controller
{
    private const QUESTION_FIELDS = [
        'tanya1', 'tanya2', 'tanya3', 'tanya4', 'tanya5', 'tanya6', 'tanya7', 'tanya8', 'tanya9',
        'tanya21', 'tanya22', 'tanya23', 'tanya24', 'tanya25',
    ];

    public function scores(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Format tanggal harus YYYY-MM-DD.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $startDate = $validated['start_date'] ?? now()->startOfYear()->toDateString();
        $endDate = $validated['end_date'] ?? today()->toDateString();

        if (Carbon::parse($startDate)->gt(Carbon::parse($endDate))) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal mulai tidak boleh melewati tanggal akhir.',
            ], 422);
        }

        $averages = collect(self::QUESTION_FIELDS)
            ->map(fn (string $field) => "AVG(`{$field}`) AS `{$field}`")
            ->implode(', ');
        $result = DB::table('survei')
            ->whereDate('created_date', '>=', $startDate)
            ->whereDate('created_date', '<=', $endDate)
            ->whereNull('spam_flagged_at')
            ->selectRaw('COUNT(*) AS jumlah_responden, '.$averages)
            ->first();

        $questionAverages = collect(self::QUESTION_FIELDS)
            ->map(fn (string $field) => $result->{$field} !== null ? (float) $result->{$field} : null);
        $skmScore = $questionAverages->take(9)->filter(fn ($score) => $score !== null)->avg();
        $sakScore = $questionAverages->slice(9)->filter(fn ($score) => $score !== null)->avg();
        $ikmValue = $skmScore !== null ? round($skmScore * 25, 2) : null;

        $quality = match (true) {
            $ikmValue === null => null,
            $ikmValue <= 43.75 => ['grade' => 'D', 'performance' => 'Tidak baik'],
            $ikmValue <= 62.50 => ['grade' => 'C', 'performance' => 'Kurang baik'],
            $ikmValue <= 81.25 => ['grade' => 'B', 'performance' => 'Baik'],
            default => ['grade' => 'A', 'performance' => 'Sangat baik'],
        };
        $sakValue = $sakScore !== null ? round($sakScore * 25, 2) : null;
        $sakQuality = match (true) {
            $sakValue === null => null,
            $sakValue <= 43.75 => ['grade' => 'D', 'performance' => 'Tidak baik'],
            $sakValue <= 62.50 => ['grade' => 'C', 'performance' => 'Kurang baik'],
            $sakValue <= 81.25 => ['grade' => 'B', 'performance' => 'Baik'],
            default => ['grade' => 'A', 'performance' => 'Sangat baik'],
        };

        return response()->json([
            'success' => true,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'respondents' => (int) $result->jumlah_responden,
            'skm' => [
                'average_score' => $skmScore !== null ? round($skmScore, 2) : null,
                'ikm_value' => $ikmValue,
                'quality' => $quality,
                'conversion_factor' => 25,
            ],
            'sak' => [
                'average_score' => $sakScore !== null ? round($sakScore, 2) : null,
                'converted_value' => $sakValue,
                'quality' => $sakQuality,
                'conversion_factor' => 25,
            ],
        ]);
    }
}
