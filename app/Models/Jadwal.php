<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    protected $fillable = [
        'user_id',
        'tugas_akhir_id',
        'jenis',
        'waktu',
        'lokasi',
        'dosen_penguji',
        'status',
        'catatan',
    ];

    protected $casts = [
        'waktu' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tugasAkhir(): BelongsTo
    {
        return $this->belongsTo(TugasAkhir::class);
    }
}
