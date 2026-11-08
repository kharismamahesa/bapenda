<?php

namespace App\Providers;

use App\Models\HalamanKonten;
use App\Models\VisitorLog;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer('partials.public_navbar', function ($view) {
            $pages = Schema::hasTable('halaman_konten')
                ? HalamanKonten::whereIn('menu_parent', ['profil', 'layanan'])
                    ->orderBy('urutan')
                    ->orderBy('judul')
                    ->get(['judul', 'slug', 'menu_parent'])
                : collect();

            $view->with([
                'profilPages' => $pages->where('menu_parent', 'profil')->values(),
                'layananPages' => $pages->where('menu_parent', 'layanan')->values(),
            ]);
        });

        View::composer('partials.public_footer', function ($view) {
            $today = Carbon::today('Asia/Jakarta');
            $stats = Schema::hasTable('visitor_logs')
                ? VisitorLog::query()
                    ->selectRaw(
                        'SUM(CASE WHEN visited_date = ? THEN 1 ELSE 0 END) AS today_total, '.
                        'SUM(CASE WHEN visited_date >= ? AND visited_date <= ? THEN 1 ELSE 0 END) AS month_total, '.
                        'SUM(CASE WHEN visited_date >= ? AND visited_date <= ? THEN 1 ELSE 0 END) AS year_total',
                        [
                            $today->toDateString(),
                            $today->copy()->startOfMonth()->toDateString(),
                            $today->toDateString(),
                            $today->copy()->startOfYear()->toDateString(),
                            $today->toDateString(),
                        ]
                    )
                    ->first()
                : null;

            $browserStats = Schema::hasTable('visitor_logs')
                ? VisitorLog::query()
                    ->selectRaw('browser, COUNT(*) AS total')
                    ->whereBetween('visited_date', [
                        $today->copy()->startOfMonth()->toDateString(),
                        $today->toDateString(),
                    ])
                    ->groupBy('browser')
                    ->orderByDesc('total')
                    ->get()
                : collect();

            $view->with([
                'visitorToday' => (int) ($stats->today_total ?? 0),
                'visitorMonth' => (int) ($stats->month_total ?? 0),
                'visitorYear' => (int) ($stats->year_total ?? 0),
                'visitorBrowserStats' => $browserStats,
            ]);
        });
    }
}
