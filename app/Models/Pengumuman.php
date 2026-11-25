<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengumuman extends Model
{
    protected $table = 'pengumumans';

    protected $fillable = [
        'user_id',
        'judul',
        'isi',
    ];

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
