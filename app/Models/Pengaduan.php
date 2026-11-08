<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengaduan extends Model
{
    public const BAPENDA_ID = 10000;

    protected $table = 'pengaduan';

    public $timestamps = false;

    protected $fillable = [
        'kode',
        'nama',
        'email',
        'no_hp',
        'aduan',
        'id_uptup',
        'created_datetime',
        'updated_datetime',
        'updated_by',
        'status',
    ];

    protected $casts = [
        'id_uptup' => 'integer',
        'created_datetime' => 'datetime',
        'updated_datetime' => 'datetime',
        'status' => 'integer',
    ];

    public function balasan(): HasMany
    {
        return $this->hasMany(PengaduanBalas::class, 'id_pengaduan');
    }
}
