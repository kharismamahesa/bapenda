<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengaduanBalas extends Model
{
    protected $table = 'pengaduan_balas';

    public $timestamps = false;

    protected $fillable = [
        'id_pengaduan',
        'balasan',
        'updated_datetime',
        'updated_by',
        'status',
    ];

    protected $casts = [
        'updated_datetime' => 'datetime',
        'status' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
