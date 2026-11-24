<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'mahasiswa',
        ]);

        User::updateOrCreate(
            ['email' => 'dosen@example.com'],
            [
                'name' => 'Dosen SIMTA',
                'nim' => 'DOSEN001',
                'password' => Hash::make('Dosen123!'),
                'role' => 'dosen',
            ]
        );

        User::updateOrCreate(
            ['email' => 'tu@example.com'],
            [
                'name' => 'Staff TU',
                'nim' => 'TU001',
                'password' => Hash::make('Tu123456!'),
                'role' => 'tu',
            ]
        );
    }
}
