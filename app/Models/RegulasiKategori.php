<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegulasiKategori extends Model
{
    use HasFactory;

    protected $table = 'regulasi_kategori';
    protected $primaryKey = 'id_regulasi_kategori';
    public $timestamps = false;

    protected $fillable = [
        'regulasi_kategori',
    ];

    public function regulasi()
    {
        return $this->hasMany(Regulasi::class, 'id_regulasi_kategori', 'id_regulasi_kategori');
    }
}
