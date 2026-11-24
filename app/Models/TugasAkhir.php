<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'status',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}