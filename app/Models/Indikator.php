<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indikator extends Model
{
    protected $table = 'indikator';
    protected $fillable = ['kriteria_id', 'kd_indikator', 'nama_indikator', 'bobot_indikator'];
    public $timestamps = false;

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id');
    }

    public function subIndikator()
    {
        return $this->hasMany(SubIndikator::class, 'indikator_id');
    }
}
