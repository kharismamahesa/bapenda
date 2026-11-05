<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regulasi extends Model
{
    use HasFactory;

    protected $table = 'regulasi';
    protected $primaryKey = 'id_regulasi';
    public $timestamps = false;

    protected $fillable = [
        'regulasi',
        'id_regulasi_kategori',
        'keterangan',
        'file',
        'created_datetime',
        'created_by',
    ];

    public function kategori()
    {
        return $this->belongsTo(RegulasiKategori::class, 'id_regulasi_kategori', 'id_regulasi_kategori');
    }

    public function penulis()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function getFileUrlAttribute()
    {
        if (! $this->file) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $this->file)) {
            return $this->file;
        }

        return asset('uploads/regulasi/'.basename(str_replace('\\', '/', $this->file)));
    }

    public function getFileExtensionAttribute()
    {
        if (! $this->file) {
            return null;
        }

        return strtolower(pathinfo(basename(str_replace('\\', '/', $this->file)), PATHINFO_EXTENSION));
    }
}
