<?php

namespace Tests\Feature;

use App\Models\Jadwal;
use App\Models\TugasAkhir;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProdiReportingTest extends TestCase
{
    use RefreshDatabase;

    public function test_prodi_can_monitor_progress_report_results_and_lecturer_involvement(): void
    {
        $prodi = User::factory()->create(['role' => 'prodi']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Laporan']);
        $advisor = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Laporan']);
        $task = TugasAkhir::create([
            'user_id' => $student->id,
            'judul' => 'Sistem Informasi Akademik',
            'topik' => 'Sistem Informasi',
            'status' => 'Disetujui',
            'dosen_pembimbing_1' => $advisor->id,
            'dosen_pembimbing_2' => User::factory()->create(['role' => 'dosen'])->id,
        ]);
        $sempro = Jadwal::create([
            'user_id' => $student->id,
            'tugas_akhir_id' => $task->id,
            'jenis' => 'sempro',
            'waktu' => now()->subDay(),
            'status' => 'selesai',
            'nilai' => 82,
            'hasil' => 'Lulus',
        ]);
        $sempro->penguji()->attach($advisor->id);
        $otherStudent = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Periode Lain']);
        $otherTask = TugasAkhir::create([
            'user_id' => $otherStudent->id,
            'judul' => 'Judul Periode Lain',
            'topik' => 'Sistem Informasi',
            'status' => 'Menunggu',
        ]);
        Jadwal::create([
            'user_id' => $otherStudent->id,
            'tugas_akhir_id' => $otherTask->id,
            'jenis' => 'sempro',
            'waktu' => now()->subMonths(2),
            'status' => 'menunggu',
        ]);

        $this->actingAs($prodi)
            ->get(route('prodi.progres-mahasiswa', ['q' => 'Mahasiswa Laporan']))
            ->assertOk()
            ->assertSee('Mahasiswa Laporan')
            ->assertSee('80%');

        $this->get(route('prodi.laporan'))
            ->assertOk()
            ->assertSee('Lulus Sempro')
            ->assertSee('Lulus Sidang')
            ->assertSee('Unduh Laporan')
            ->assertSee('Wisuda');

        $export = $this->get(route('prodi.laporan.export'));
        $export->assertDownload('laporan-progres-tugas-akhir-' . now()->subDay()->format('Y-m') . '.csv');
        $this->assertStringContainsString('Mahasiswa Laporan', $export->streamedContent());
        $this->assertStringNotContainsString('Mahasiswa Periode Lain', $export->streamedContent());

        $this->get(route('prodi.rekap-dosen', ['periode' => now()->subDay()->format('Y-m')]))
            ->assertOk()
            ->assertSee('Dosen Laporan')
            ->assertSee('Penguji Sempro')
            ->assertSee('Wisuda');
    }
}
