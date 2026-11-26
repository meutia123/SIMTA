<?php

namespace Tests\Feature;

use App\Models\Jadwal;
use App\Models\Document;
use App\Models\TugasAkhir;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TuWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_tu_dashboard_and_administration_list_use_real_task_and_schedule_records(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa SIMTA']);
        $tu = User::factory()->create(['role' => 'tu']);
        $advisor = User::factory()->create(['role' => 'dosen']);
        $task = TugasAkhir::create([
            'user_id' => $student->id,
            'judul' => 'Sistem Informasi',
            'status' => 'Disetujui',
            'dosen_pembimbing_1' => $advisor->id,
            'dosen_pembimbing_2' => User::factory()->create(['role' => 'dosen'])->id,
            'sk_pembimbing_diminta_at' => now(),
        ]);
        $schedule = Jadwal::create([
            'user_id' => $student->id,
            'tugas_akhir_id' => $task->id,
            'jenis' => 'sempro',
            'status' => 'terjadwal',
            'waktu' => now()->addDay(),
            'dosen_penguji' => $advisor->name,
            'sk_tim_penguji_diminta_at' => now(),
        ]);

        $this->actingAs($tu)
            ->get(route('tu.dashboard'))
            ->assertOk()
            ->assertSee('Mahasiswa SIMTA')
            ->assertSee('Permintaan SK pembimbing')
            ->assertSee('Permintaan SK tim penguji');

        $this->get(route('tu.administrasi-sk', ['jenis' => 'pembimbing', 'status' => 'perlu']))
            ->assertOk()
            ->assertSee('Mahasiswa SIMTA')
            ->assertSee('SK Pembimbing')
            ->assertSee(route('tu.proses-sk', ['jenis' => 'pembimbing', 'id' => $task->id]))
            ->assertDontSee(route('tu.proses-sk', ['jenis' => 'penguji', 'id' => $schedule->id]));

        $this->get(route('tu.proses-sk', ['jenis' => 'pembimbing', 'id' => $task->id]))
            ->assertOk()
            ->assertSee('Nomor SK')
            ->assertSee('Unggah file SK');
    }

    public function test_tu_can_issue_both_sk_types_and_only_related_users_can_download_them(): void
    {
        Storage::fake('public');
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $otherStudent = User::factory()->create(['role' => 'mahasiswa']);
        $tu = User::factory()->create(['role' => 'tu']);
        $advisor = User::factory()->create(['role' => 'dosen']);
        $task = TugasAkhir::create([
            'user_id' => $student->id,
            'judul' => 'Sistem Informasi',
            'status' => 'Disetujui',
            'dosen_pembimbing_1' => $advisor->id,
            'dosen_pembimbing_2' => User::factory()->create(['role' => 'dosen'])->id,
            'sk_pembimbing_diminta_at' => now(),
        ]);
        $schedule = Jadwal::create([
            'user_id' => $student->id,
            'tugas_akhir_id' => $task->id,
            'jenis' => 'sempro',
            'status' => 'terjadwal',
            'waktu' => now()->addDay(),
            'dosen_penguji' => $advisor->name,
            'sk_tim_penguji_diminta_at' => now(),
        ]);

        $this->actingAs($tu)
            ->post(route('tu.proses-sk.store', ['jenis' => 'pembimbing', 'id' => $task->id]), [
                'nomor_sk' => 'SK/001/2026',
                'tanggal_sk' => now()->toDateString(),
                'file_sk' => UploadedFile::fake()->create('sk-pembimbing.pdf', 20, 'application/pdf'),
            ])
            ->assertRedirect(route('tu.administrasi-sk'));

        $this->assertDatabaseHas('tugas_akhirs', [
            'id' => $task->id,
            'sk_pembimbing_nomor' => 'SK/001/2026',
        ]);

        $this->post(route('tu.proses-sk.store', ['jenis' => 'penguji', 'id' => $schedule->id]), [
            'nomor_sk' => 'SK/002/2026',
            'tanggal_sk' => now()->toDateString(),
            'file_sk' => UploadedFile::fake()->create('sk-penguji.pdf', 20, 'application/pdf'),
        ])->assertRedirect(route('tu.administrasi-sk'));

        $this->actingAs($student)
            ->get(route('surat-keputusan.download', ['jenis' => 'pembimbing', 'id' => $task->id]))
            ->assertOk();
        $this->get(route('surat-keputusan.download', ['jenis' => 'penguji', 'id' => $schedule->id]))
            ->assertOk();
        $this->get(route('dokumen'))
            ->assertOk()
            ->assertSee('Unduh')
            ->assertSee('Unduh Sempro');

        $this->actingAs($advisor)
            ->get(route('surat-keputusan.download', ['jenis' => 'pembimbing', 'id' => $task->id]))
            ->assertOk();
        $this->get(route('surat-keputusan.download', ['jenis' => 'penguji', 'id' => $schedule->id]))
            ->assertOk();
        $this->get(route('dosen.dashboard'))
            ->assertOk()
            ->assertSee('SK Terkait')
            ->assertSee(route('surat-keputusan.download', ['jenis' => 'penguji', 'id' => $schedule->id]));

        $this->actingAs($otherStudent)
            ->get(route('surat-keputusan.download', ['jenis' => 'pembimbing', 'id' => $task->id]))
            ->assertForbidden();
    }

    public function test_student_cannot_upload_a_surat_keputusan_that_is_issued_by_tu(): void
    {
        Storage::fake('public');
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($student)
            ->post(route('dokumen.upload'), [
                'nama_dokumen' => 'SK Pembimbing',
                'jenis_dokumen' => 'Surat Keputusan',
                'file' => UploadedFile::fake()->create('sk.pdf', 20, 'application/pdf'),
            ])
            ->assertSessionHasErrors('nama_dokumen');

        $this->assertDatabaseCount('documents', 0);
    }

    public function test_tu_profile_uses_account_data_and_supports_photo_and_password_updates(): void
    {
        Storage::fake('public');
        $tu = User::factory()->create([
            'role' => 'tu',
            'password' => Hash::make('Valid-pass-123'),
        ]);

        $this->actingAs($tu)
            ->get(route('tu.profil'))
            ->assertOk()
            ->assertSee($tu->name)
            ->assertSee('Tata Usaha');

        $this->post(route('tu.profil.foto'), [
            'foto' => UploadedFile::fake()->createWithContent(
                'profil.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC')
            ),
        ])->assertRedirect(route('tu.profil'));

        $tu->refresh();
        $this->assertNotNull($tu->foto_path);
        Storage::disk('public')->assertExists($tu->foto_path);

        $this->post(route('tu.profil.password'), [
            'current_password' => 'Valid-pass-123',
            'password' => 'New-valid-123',
            'password_confirmation' => 'New-valid-123',
        ])->assertRedirect(route('tu.profil'));

        $this->assertTrue(Hash::check('New-valid-123', $tu->fresh()->password));
    }

    public function test_tu_can_upload_update_download_and_delete_student_documents(): void
    {
        Storage::fake('public');
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Pemilik Dokumen']);
        $tu = User::factory()->create(['role' => 'tu']);

        $this->actingAs($tu)
            ->post(route('tu.dokumen.store'), [
                'user_id' => $student->id,
                'nama_dokumen' => 'Lembar Persetujuan',
                'jenis_dokumen' => 'Administrasi',
                'file' => UploadedFile::fake()->createWithContent('persetujuan.pdf', 'versi pertama'),
            ])
            ->assertRedirect(route('tu.dokumen'));

        $document = Document::firstOrFail();
        $oldPath = $document->file_path;
        $this->assertEquals($tu->id, $document->uploaded_by);
        $this->assertSame('diverifikasi', $document->status);

        $this->actingAs($student)
            ->get(route('dokumen'))
            ->assertOk()
            ->assertSee('Lembar Persetujuan')
            ->assertSee('Dikelola TU');
        $this->delete(route('dokumen.hapus', $document))->assertForbidden();

        $this->actingAs($tu)
            ->put(route('tu.dokumen.update', $document), [
                'nama_dokumen' => 'Lembar Persetujuan Revisi',
                'jenis_dokumen' => 'Administrasi Tugas Akhir',
                'file' => UploadedFile::fake()->createWithContent('persetujuan-revisi.pdf', 'versi terbaru'),
            ])
            ->assertRedirect(route('tu.dokumen'));

        $document->refresh();
        $this->assertSame('Lembar Persetujuan Revisi', $document->nama_dokumen);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($document->file_path);
        $this->get(route('tu.dokumen.download', $document))->assertOk();

        $this->delete(route('tu.dokumen.destroy', $document))
            ->assertRedirect(route('tu.dokumen'));
        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
    }
}
