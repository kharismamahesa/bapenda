<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $table = 'slider';

    protected $fillable = [
        'gambar',
        'judul_putih',
        'judul_kuning',
        'subjudul',
        'urutan',
        'status',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'status' => 'boolean',
    ];
}
