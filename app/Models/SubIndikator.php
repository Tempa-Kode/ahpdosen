<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubIndikator extends Model
{
    protected $table = 'sub_indikator';
    protected $fillable = [
        'indikator_id',
        'nama_sub_indikator',
        'skor_kredit',
    ];
    public $timestamps = false;

    public function indikator()
    {
        return $this->belongsTo(Indikator::class, 'indikator_id');
    }
}
