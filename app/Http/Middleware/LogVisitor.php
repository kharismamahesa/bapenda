<?php

namespace App\Http\Middleware;

use App\Models\VisitorLog;
use Carbon\Carbon;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LogVisitor
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->ajax()
            || $request->wantsJson()
            || ! $request->isMethod('GET')
            || $request->is('admin*', 'login*', 'logout*')) {
            return $next($request);
        }

        try {
            $ipAddress = $request->ip();
            if (! $ipAddress) {
                return $next($request);
            }

            $browser = $this->parseBrowser($request->userAgent());
            $visitedDate = Carbon::today('Asia/Jakarta')->toDateString();

            if (! VisitorLog::where('ip_address', $ipAddress)
                ->where('browser', $browser)
                ->where('visited_date', $visitedDate)
                ->exists()) {
                $location = $this->locationFor($ipAddress);

                VisitorLog::create([
                    'ip_address' => $ipAddress,
                    'user_agent' => $request->userAgent(),
                    'browser' => $browser,
                    'city' => $location['city'],
                    'region' => $location['region'],
                    'country' => $location['country'],
                    'latitude' => $location['latitude'],
                    'longitude' => $location['longitude'],
                    'visited_date' => $visitedDate,
                ]);
            }
        } catch (QueryException $exception) {
            if (! $this->isDuplicateDailyVisitor($exception)) {
                Log::error('Gagal menyimpan visitor log.', [
                    'exception' => $exception->getMessage(),
                ]);
            }
        } catch (Throwable $exception) {
            Log::error('Gagal memproses visitor log.', [
                'exception' => $exception->getMessage(),
            ]);
        }

        return $next($request);
    }

    private function locationFor(string $ipAddress): array
    {
        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return [
                'city' => 'Lokal',
                'region' => 'Lokal',
                'country' => 'Lokal',
                'latitude' => null,
                'longitude' => null,
            ];
        }

        return [
            'city' => null,
            'region' => null,
            'country' => null,
            'latitude' => null,
            'longitude' => null,
        ];
    }

    private function parseBrowser(?string $userAgent): string
    {
        if (! $userAgent) {
            return 'Other';
        }

        return match (true) {
            preg_match('/(?:Edg|Edge)\//i', $userAgent) === 1 => 'Microsoft Edge',
            preg_match('/(?:OPR|Opera)\//i', $userAgent) === 1 => 'Opera',
            preg_match('/(?:MSIE|Trident)/i', $userAgent) === 1 => 'Internet Explorer',
            preg_match('/Firefox\//i', $userAgent) === 1 => 'Mozilla Firefox',
            preg_match('/Chrome\//i', $userAgent) === 1 => 'Google Chrome',
            preg_match('/Safari\//i', $userAgent) === 1 => 'Safari',
            default => 'Other',
        };
    }

    private function isDuplicateDailyVisitor(QueryException $exception): bool
    {
        return $exception->getCode() === '23000'
            && str_contains($exception->getMessage(), 'visitor_logs_daily_visitor_unique');
    }
}
