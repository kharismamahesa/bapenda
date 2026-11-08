<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'video';

    public $timestamps = false;

    protected $fillable = [
        'judul',
        'video',
        'keterangan',
    ];

    public static function normalizeYoutubeUrl(string $url): ?string
    {
        $parts = parse_url(trim($url));
        if (! is_array($parts) || ! isset($parts['scheme'], $parts['host'], $parts['path'])) {
            return null;
        }

        if (! in_array(strtolower($parts['scheme']), ['http', 'https'], true)
            || isset($parts['user'], $parts['pass'], $parts['port'])) {
            return null;
        }

        $host = strtolower($parts['host']);
        $videoId = null;

        if (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com'], true)) {
            if ($parts['path'] === '/watch') {
                parse_str($parts['query'] ?? '', $query);
                $videoId = $query['v'] ?? null;
            } elseif (preg_match('~^/(?:embed|shorts|live)/([A-Za-z0-9_-]{11})/?$~', $parts['path'], $matches)) {
                $videoId = $matches[1];
            }
        } elseif (in_array($host, ['youtu.be', 'www.youtu.be'], true)
            && preg_match('~^/([A-Za-z0-9_-]{11})/?$~', $parts['path'], $matches)) {
            $videoId = $matches[1];
        } elseif (in_array($host, ['youtube-nocookie.com', 'www.youtube-nocookie.com'], true)
            && preg_match('~^/embed/([A-Za-z0-9_-]{11})/?$~', $parts['path'], $matches)) {
            $videoId = $matches[1];
        }

        if (! is_string($videoId) || ! preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId)) {
            return null;
        }

        return 'https://www.youtube.com/embed/'.$videoId;
    }
}
