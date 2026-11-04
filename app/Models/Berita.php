<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory;

    protected $table = 'berita';
    protected $primaryKey = 'id_berita';
    public $timestamps = false;

    protected $fillable = [
        'id_berita_kategori',
        'judul',
        'isi',
        'thumbnail',
        'gambar',
        'tanggal_berita',
        'counter',
        'url',
        'status',
        'created_datetime',
        'created_by',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriBerita::class, 'id_berita_kategori', 'id_berita_kategori');
    }

    public function penulis()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->resolveImageUrl($this->thumbnail);
    }

    public function getGambarUrlAttribute()
    {
        return $this->resolveImageUrl($this->gambar);
    }

    protected function resolveImageUrl($file)
    {
        $placeholder = asset('images/no-image.svg');

        if (! $file) {
            return $placeholder;
        }

        if (preg_match('/^https?:\/\//i', $file)) {
            return $file;
        }

        $relative = ltrim($file, '/');
        $candidates = array_unique([
            $relative,
            'uploads/berita/' . $relative,
        ]);

        foreach ($candidates as $path) {
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return $placeholder;
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->status == 1) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Publish</span>';
        }
        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Draft</span>';
    }
}
