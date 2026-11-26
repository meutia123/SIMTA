<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProdiProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_prodi_profile_shows_account_information_and_updates_photo_and_password(): void
    {
        Storage::fake('public');
        $prodi = User::factory()->create([
            'role' => 'prodi',
            'name' => 'Koordinator Prodi',
            'email' => 'prodi@example.test',
            'program_studi' => 'Sistem Informasi',
            'no_hp' => '08123456789',
            'password' => Hash::make('Valid-pass-123'),
        ]);

        $this->actingAs($prodi)
            ->get(route('prodi.profil'))
            ->assertOk()
            ->assertSee('Koordinator Prodi')
            ->assertSee('Sistem Informasi')
            ->assertSee('prodi@example.test')
            ->assertSee('08123456789')
            ->assertSee('Ganti Password');

        $this->post(route('prodi.profil.foto'), [
            'foto' => UploadedFile::fake()->createWithContent(
                'prodi.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC')
            ),
        ])->assertRedirect(route('prodi.profil'));

        $this->assertNotNull($prodi->fresh()->foto_path);
        Storage::disk('public')->assertExists($prodi->fresh()->foto_path);

        $this->post(route('prodi.profil.password'), [
            'current_password' => 'Valid-pass-123',
            'password' => 'New-valid-123',
            'password_confirmation' => 'New-valid-123',
        ])->assertRedirect(route('prodi.profil'));

        $this->assertTrue(Hash::check('New-valid-123', $prodi->fresh()->password));
    }
}
