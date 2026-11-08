<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HalamanKonten extends Model
{
    use HasFactory;

    protected $table = 'halaman_konten';

    protected $fillable = [
        'judul',
        'slug',
        'isi',
        'gambar',
        'menu_parent',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    /**
     * Generate slug otomatis dari judul.
     */
    public static function generateSlug(string $judul, ?int $exceptId = null): string
    {
        $slug = Str::slug($judul);
        $original = $slug;
        $count = 1;

        $query = static::where('slug', $slug);
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        while ($query->exists()) {
            $slug = $original . '-' . $count++;
            $query = static::where('slug', $slug);
            if ($exceptId) {
                $query->where('id', '!=', $exceptId);
            }
        }

        return $slug;
    }
}
