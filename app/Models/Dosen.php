<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $table = 'dosen';

    protected $fillable = [
        'nidn',
        'nama_dosen',
        'prodi'
    ];

    public $timestamps = false;

    public function penilaians()
    {
        return $this->hasMany(Penilaian::class);
    }
}
