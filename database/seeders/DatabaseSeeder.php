<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'zoriaftrc@gmail.com'],
            [
                'name' => 'Zoria Fitriaca',
                'nim' => '2417020095',
                'password' => Hash::make('mhs123'),
                'role' => 'mahasiswa',
            ]
        );

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('mhs123'),
                'role' => 'mahasiswa',
            ]
        );

        $lecturers = [
            ['haditya@gmail.com', 'DOSEN001', 'Haditya Prasetyo, S.Kom, M.Kom'],
            ['domi@gmail.com', 'DOSEN002', 'Domi Sepri, M.Kom'],
            ['mhd@gmail.com', 'DOSEN003', 'MHD ADHITIYA OKTA RIYANDI'],
            ['yaslinda@gmail.com', 'DOSEN004', 'Dr. Yaslinda Lizar, S.Si., M.Kom.'],
            ['syaflan@gmail.com', 'DOSEN005', 'Syaflan Sandi Harta Putra, S.Pd, M.Kom'],
            ['raju@gmail.com', 'DOSEN006', 'Raju Wandira, M.Kom'],
            ['novia@gmail.com', 'DOSEN007', 'Novia Lestari, M.Kom'],
            ['ozzy@gmail.com', 'DOSEN008', 'Ozzy Secio Riza, M.Kom'],
        ];

        foreach ($lecturers as [$email, $nim, $name]) {
            $lecturer = User::firstOrNew(['nim' => $nim]);
            $lecturer->fill([
                'name' => $name,
                'email' => $email,
                'role' => 'dosen',
            ]);

            if (!$lecturer->exists) {
                $lecturer->password = Hash::make(bin2hex(random_bytes(32)));
            }

            $lecturer->save();
        }

        User::updateOrCreate(
            ['email' => 'tu@example.com'],
            [
                'name' => 'Staff TU',
                'nim' => 'TU001',
                'password' => Hash::make('tu123'),
                'role' => 'tu',
            ]
        );

        User::updateOrCreate(
            ['email' => 'prodi@example.com'],
            [
                'name' => 'Kordinator Prodi',
                'nim' => 'PRODI001',
                'password' => Hash::make('prodi123'),
                'role' => 'prodi',
            ]
        );
    }
}
