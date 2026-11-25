<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'dosen_pembimbing_1',
        'dosen_pembimbing_2',
        'status',
        'catatan_prodi',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function calonDosen1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calon_dosen_1');
    }

    public function calonDosen2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calon_dosen_2');
    }

    public function dosenPembimbing1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing_1');
    }

    public function dosenPembimbing2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing_2');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }
}