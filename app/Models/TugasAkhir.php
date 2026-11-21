<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TugasAkhir extends Model
{
    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'status'
    ];
}