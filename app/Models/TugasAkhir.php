<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TugasAkhir extends Model
{
    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'topik',
        'calon_dosen_1',
        'calon_dosen_2',
        'calon_dosen_3',
        'status'
    ];
}