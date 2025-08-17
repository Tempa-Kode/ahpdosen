<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubSubIndikator extends Model
{
    protected $table = 'sub_sub_indikator';
    protected $fillable = [
        'sub_indikator_id',
        'nama_sub_sub_indikator',
        'skor_kredit',
    ];

    public $timestamps = false;

    public function subIndikator()
    {
        return $this->belongsTo(SubIndikator::class, 'sub_indikator_id');
    }
}
