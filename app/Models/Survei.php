<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survei extends Model
{
    protected $table = 'survei';

    protected $primaryKey = 'id_survei';

    public $timestamps = false;

    protected $fillable = [
        'no_hp',
        'email',
        'alamat',
        'nama',
        'jenis_kelamin',
        'usia',
        'pendidikan',
        'pekerjaan',
        'id_uptup',
        'tanya1',
        'tanya2',
        'tanya3',
        'tanya4',
        'tanya5',
        'tanya6',
        'tanya7',
        'tanya8',
        'tanya9',
        'tanya21',
        'tanya22',
        'tanya23',
        'tanya24',
        'tanya25',
        'created_date',
        'app',
        'spam_flagged_at',
        'spam_reason',
    ];

    protected $casts = [
        'usia' => 'integer',
        'created_date' => 'datetime',
        'spam_flagged_at' => 'datetime',
        'tanya1' => 'integer',
        'tanya2' => 'integer',
        'tanya3' => 'integer',
        'tanya4' => 'integer',
        'tanya5' => 'integer',
        'tanya6' => 'integer',
        'tanya7' => 'integer',
        'tanya8' => 'integer',
        'tanya9' => 'integer',
        'tanya21' => 'integer',
        'tanya22' => 'integer',
        'tanya23' => 'integer',
        'tanya24' => 'integer',
        'tanya25' => 'integer',
    ];
}
