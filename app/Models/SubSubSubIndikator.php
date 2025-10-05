<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubSubSubIndikator extends Model
{
    protected $table = 'sub_sub_sub_indikator';
    protected $fillable = [
        'sub_sub_indikator_id',
        'nama_sub_sub_sub_indikator',
        'skor_kredit',
    ];

    public $timestamps = false;

    public function subSubIndikator()
    {
        return $this->belongsTo(SubSubIndikator::class, 'sub_sub_indikator_id');
    }
}
