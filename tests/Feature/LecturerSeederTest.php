<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LecturerSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_keeps_existing_lecturer_passwords_and_creates_all_requested_demo_names(): void
    {
        $existing = User::create([
            'name' => 'Nama Lama',
            'email' => 'dosen@example.com',
            'nim' => 'DOSEN001',
            'password' => Hash::make('keep-this-password'),
            'role' => 'dosen',
        ]);
        $passwordHash = $existing->password;

        $seeder = new \Database\Seeders\DatabaseSeeder();
        $seeder->run();
        $seeder->run();

        $this->assertSame($passwordHash, $existing->fresh()->password);
        $this->assertSame(8, User::where('role', 'dosen')->count());
        $this->assertDatabaseHas('users', [
            'email' => 'haditya@gmail.com',
            'name' => 'Haditya Prasetyo, S.Kom, M.Kom',
        ]);

        foreach ([
            'Haditya Prasetyo, S.Kom, M.Kom',
            'Domi Sepri, M.Kom',
            'MHD ADHITIYA OKTA RIYANDI',
            'Dr. Yaslinda Lizar, S.Si., M.Kom.',
            'Syaflan Sandi Harta Putra, S.Pd, M.Kom',
            'Raju Wandira, M.Kom',
            'Novia Lestari, M.Kom',
            'Ozzy Secio Riza, M.Kom',
        ] as $name) {
            $this->assertDatabaseHas('users', [
                'name' => $name,
                'role' => 'dosen',
            ]);
        }
    }
}
