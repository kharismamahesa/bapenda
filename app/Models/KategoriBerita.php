<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriBerita extends Model
{
    use HasFactory;

    protected $table = 't_berita_kategori';
    protected $primaryKey = 'id_berita_kategori';
    public $timestamps = false;

    protected $fillable = [
        'berita_kategori',
    ];

    public function berita()
    {
        return $this->hasMany(Berita::class, 'id_berita_kategori', 'id_berita_kategori');
    }
}
