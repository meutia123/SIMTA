<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'sk_tim_penguji_nomor',
        'sk_tim_penguji_tanggal',
        'sk_tim_penguji_file',
        'sk_tim_penguji_diminta_at',
        'nilai',
        'hasil',
        'nilai_presentasi',
        'nilai_naskah',
        'nilai_penguasaan',
        'status_validasi_nilai',
        'catatan_validasi_nilai',
        'dosen_penilai_id',
    ];

    protected $casts = [
        'waktu' => 'datetime',
        'sk_tim_penguji_tanggal' => 'date',
        'sk_tim_penguji_diminta_at' => 'datetime',
        'nilai' => 'decimal:2',
        'nilai_presentasi' => 'decimal:2',
        'nilai_naskah' => 'decimal:2',
        'nilai_penguasaan' => 'decimal:2',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tugasAkhir(): BelongsTo
    {
        return $this->belongsTo(TugasAkhir::class);
    }

    public function penguji(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'jadwal_penguji', 'jadwal_id', 'dosen_id');
    }

    public function dosenPenilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_penilai_id');
    }

    public function nilaiTervalidasi(): bool
    {
        return $this->nilai !== null
            && $this->hasil !== null
            && in_array($this->status_validasi_nilai, [null, 'tervalidasi'], true);
    }
}
