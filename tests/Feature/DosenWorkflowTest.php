<?php

namespace Tests\Feature;

use App\Models\Jadwal;
use App\Models\TugasAkhir;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DosenWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_dosen_can_view_assigned_schedules_advisees_and_involvement(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Pembimbing']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Bimbingan']);
        $task = TugasAkhir::create([
            'user_id' => $student->id,
            'judul' => 'Sistem Informasi Perpustakaan',
            'topik' => 'Sistem Informasi',
            'status' => 'Disetujui',
            'dosen_pembimbing_1' => $dosen->id,
            'dosen_pembimbing_2' => User::factory()->create(['role' => 'dosen'])->id,
        ]);
        $sempro = Jadwal::create([
            'user_id' => $student->id,
            'tugas_akhir_id' => $task->id,
            'jenis' => 'sempro',
            'waktu' => now()->addDay(),
            'lokasi' => 'Ruang Seminar',
            'status' => 'terjadwal',
        ]);
        $sempro->penguji()->attach($dosen->id);

        $this->actingAs($dosen)
            ->get(route('dosen.dashboard'))
            ->assertOk()
            ->assertSee('Mahasiswa Bimbingan')
            ->assertSee('Sistem Informasi Perpustakaan')
            ->assertSee('Jadwal Ujian Saya');

        $this->get(route('dosen.jadwal'))
            ->assertOk()
            ->assertSee('Ruang Seminar')
            ->assertSee('Mahasiswa Bimbingan');

        $this->get(route('dosen.keterlibatan'))
            ->assertOk()
            ->assertSee('Rekap Keterlibatan Saya')
            ->assertSee('Sistem Informasi Perpustakaan')
            ->assertSee('Penguji Sempro');
    }
}
