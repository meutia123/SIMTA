<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi massal (mass assignment)
     */
    protected $fillable = [
        'name',
        'nim',
        'email',
        'password',
    ];

    /**
     * Kolom yang disembunyikan saat di-serialize
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast kolom ke tipe tertentu
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}