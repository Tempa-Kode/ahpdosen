<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    protected $table = 'kriteria';

    protected $fillable = [
        'kd_kriteria',
        'nama_kriteria',
        'bobot',
    ];

    public $timestamps = false;
}
