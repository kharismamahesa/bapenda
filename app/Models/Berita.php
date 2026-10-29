<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory;

    protected $table = 't_berita';
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
        return $this->belongsTo(TUser::class, 'created_by', 'id');
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail && file_exists(public_path('uploads/berita/' . $this->thumbnail))) {
            return asset('uploads/berita/' . $this->thumbnail);
        }
        return 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?w=300&h=200&fit=crop';
    }

    public function getGambarUrlAttribute()
    {
        if ($this->gambar && file_exists(public_path('uploads/berita/' . $this->gambar))) {
            return asset('uploads/berita/' . $this->gambar);
        }
        return 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?w=800&h=500&fit=crop';
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->status == 1) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Publish</span>';
        }
        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Draft</span>';
    }
}
