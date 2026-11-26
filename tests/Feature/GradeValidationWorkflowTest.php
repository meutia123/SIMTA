<?php

namespace Tests\Feature;

use App\Models\Jadwal;
use App\Models\TugasAkhir;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeValidationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_prodi_can_return_a_grade_to_its_evaluator_for_correction_then_lock_it_after_validation(): void
    {
        $prodi = User::factory()->create(['role' => 'prodi']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $evaluator = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Penilai']);
        $otherExaminer = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Penguji']);
        $task = TugasAkhir::create([
            'user_id' => $student->id,
            'judul' => 'Analisis Data',
            'topik' => 'Data Mining',
            'status' => 'Disetujui',
            'dosen_pembimbing_1' => $evaluator->id,
            'dosen_pembimbing_2' => $otherExaminer->id,
        ]);
        $jadwal = Jadwal::create([
            'user_id' => $student->id,
            'tugas_akhir_id' => $task->id,
            'jenis' => 'sidang',
            'waktu' => now()->subDay(),
            'status' => 'selesai',
        ]);
        $jadwal->penguji()->attach([$evaluator->id, $otherExaminer->id]);

        $this->actingAs($evaluator)
            ->post(route('dosen.nilai.store', $jadwal), [
                'nilai_presentasi' => 80,
                'nilai_naskah' => 90,
                'nilai_penguasaan' => 85,
                'hasil' => 'Lulus',
            ])
            ->assertRedirect(route('dosen.jadwal'));

        $this->actingAs($prodi)
            ->patch(route('prodi.validasi-nilai.return', $jadwal), [])
            ->assertSessionHasErrors('catatan_validasi_nilai');

        $this->patch(route('prodi.validasi-nilai.return', $jadwal), [
            'catatan_validasi_nilai' => 'Periksa kembali penilaian naskah.',
        ])->assertRedirect(route('prodi.validasi-nilai.show', $jadwal));

        $this->assertSame('dikembalikan', $jadwal->fresh()->status_validasi_nilai);
        $this->assertSame('Periksa kembali penilaian naskah.', $jadwal->fresh()->catatan_validasi_nilai);

        $this->actingAs($otherExaminer)
            ->get(route('dosen.nilai', $jadwal))
            ->assertForbidden();

        $this->actingAs($evaluator)
            ->get(route('dosen.nilai', $jadwal))
            ->assertOk()
            ->assertSee('Periksa kembali penilaian naskah.');

        $this->post(route('dosen.nilai.store', $jadwal), [
            'nilai_presentasi' => 82,
            'nilai_naskah' => 92,
            'nilai_penguasaan' => 86,
            'hasil' => 'Lulus',
        ])->assertRedirect(route('dosen.jadwal'));

        $this->assertSame('menunggu_validasi', $jadwal->fresh()->status_validasi_nilai);
        $this->assertNull($jadwal->fresh()->catatan_validasi_nilai);

        $this->actingAs($prodi)
            ->patch(route('prodi.validasi-nilai.validate', $jadwal))
            ->assertRedirect(route('prodi.validasi-nilai.show', $jadwal));

        $this->actingAs($evaluator)
            ->get(route('dosen.nilai', $jadwal))
            ->assertForbidden();
    }

    public function test_progress_page_filters_by_stage_and_shows_completion_percentages(): void
    {
        $prodi = User::factory()->create(['role' => 'prodi']);
        $pendingStudent = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Pengajuan']);
        $semproStudent = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Sempro']);
        $task = TugasAkhir::create([
            'user_id' => $pendingStudent->id,
            'judul' => 'Judul Menunggu',
            'topik' => 'Sistem Informasi',
            'status' => 'Menunggu',
        ]);
        $semproTask = TugasAkhir::create([
            'user_id' => $semproStudent->id,
            'judul' => 'Judul Sempro',
            'topik' => 'Sistem Informasi',
            'status' => 'Disetujui',
            'dosen_pembimbing_1' => User::factory()->create(['role' => 'dosen'])->id,
            'dosen_pembimbing_2' => User::factory()->create(['role' => 'dosen'])->id,
        ]);
        Jadwal::create([
            'user_id' => $semproStudent->id,
            'tugas_akhir_id' => $semproTask->id,
            'jenis' => 'sempro',
            'status' => 'menunggu',
        ]);

        $this->actingAs($prodi)
            ->get(route('prodi.progres-mahasiswa', ['tahap' => 'pengajuan-judul']))
            ->assertOk()
            ->assertSee('Mahasiswa Pengajuan')
            ->assertDontSee('Mahasiswa Sempro')
            ->assertSee('15%');

        $this->get(route('prodi.progres-mahasiswa', ['tahap' => 'sempro']))
            ->assertOk()
            ->assertSee('Mahasiswa Sempro')
            ->assertSee('50%');
    }
}
