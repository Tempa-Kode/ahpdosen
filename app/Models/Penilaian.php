<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $guarded = ['id'];

    protected $table = 'penilaian';

    public $timestamps = false;

    public function penilaian()
    {
        return $this->morphTo();
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }
}
