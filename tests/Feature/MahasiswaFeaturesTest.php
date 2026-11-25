<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Jadwal;
use App\Models\TugasAkhir;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MahasiswaFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_submit_a_task_title_with_two_distinct_advisors(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $advisorOne = User::factory()->create(['role' => 'dosen']);
        $advisorTwo = User::factory()->create(['role' => 'dosen']);

        $this->actingAs($student)
            ->post(route('tugas-akhir.store'), [
                'judul' => 'Aplikasi Informasi Akademik',
                'topik' => 'Sistem Informasi',
                'deskripsi' => 'Pengembangan aplikasi akademik.',
                'calon_dosen_1' => $advisorOne->id,
                'calon_dosen_2' => $advisorTwo->id,
            ])
            ->assertRedirect(route('tugas-akhir'));

        $this->assertDatabaseHas('tugas_akhirs', [
            'user_id' => $student->id,
            'judul' => 'Aplikasi Informasi Akademik',
            'calon_dosen_1' => $advisorOne->id,
            'calon_dosen_2' => $advisorTwo->id,
            'status' => 'Menunggu',
        ]);
    }

    public function test_student_can_login_using_nim_and_logout(): void
    {
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'nim' => '2417020000',
            'password' => Hash::make('Valid-pass-123'),
        ]);

        $this->post(route('login'), [
            'login' => $student->nim,
            'password' => 'Valid-pass-123',
        ])->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))->assertOk();

        $this->post(route('logout'))
            ->assertRedirect(route('logout.success'));
        $this->assertGuest();
    }

    public function test_login_and_role_middleware_normalize_role_values(): void
    {
        $student = User::factory()->create([
            'role' => ' Mahasiswa ',
            'nim' => '2417020001',
            'password' => Hash::make('Valid-pass-123'),
        ]);

        $this->post(route('login'), [
            'login' => $student->nim,
            'password' => 'Valid-pass-123',
        ])->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))->assertOk();
    }

    public function test_wrong_dashboard_path_redirects_to_the_signed_in_role_dashboard(): void
    {
        $prodi = User::factory()->create(['role' => 'prodi']);

        $this->actingAs($prodi)
            ->get(route('dashboard'))
            ->assertRedirect(route('prodi.dashboard'));

        $this->get(route('prodi.dashboard'))->assertOk();
    }

    public function test_dashboard_displays_counts_from_the_signed_in_student_records(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $otherStudent = User::factory()->create(['role' => 'mahasiswa']);

        TugasAkhir::create([
            'user_id' => $student->id,
            'judul' => 'Sistem Informasi',
            'topik' => 'Rekayasa Perangkat Lunak',
            'status' => 'Menunggu',
        ]);
        TugasAkhir::create([
            'user_id' => $otherStudent->id,
            'judul' => 'Data Mining',
            'topik' => 'Data',
            'status' => 'Menunggu',
        ]);
        Document::create([
            'user_id' => $student->id,
            'nama_dokumen' => 'Proposal',
            'jenis_dokumen' => 'Proposal',
            'file_path' => 'documents/proposal.pdf',
            'file_name' => 'proposal.pdf',
            'file_size' => 100,
            'mime_type' => 'application/pdf',
            'status' => 'diupload',
        ]);

        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-testid="tugas-akhir-count">1</h2>', false)
            ->assertSee('data-testid="dokumen-count">1</h2>', false)
            ->assertSee('Menunggu');
    }

    public function test_student_schedule_page_shows_scheduled_records_and_demo_label(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        Jadwal::create([
            'user_id' => $student->id,
            'jenis' => 'bimbingan',
            'waktu' => now()->addDay(),
            'lokasi' => 'Ruang Dosen 204',
            'dosen_penguji' => 'Dosen Pembimbing',
            'status' => 'terjadwal',
            'catatan' => 'DEMO: Contoh jadwal.',
        ]);

        $this->actingAs($student)
            ->get(route('jadwal'))
            ->assertOk()
            ->assertSee('Ruang Dosen 204')
            ->assertSee('Data demo')
            ->assertSee('Terjadwal');
    }

    public function test_student_can_upload_an_additional_document_using_its_entered_name(): void
    {
        Storage::fake('public');
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $response = $this->actingAs($student)->post(route('dokumen.upload'), [
            'nama_dokumen' => 'Lainnya',
            'nama_dokumen_lain' => 'Surat Pernyataan',
            'jenis_dokumen' => 'Administrasi',
            'file' => UploadedFile::fake()->create('surat.pdf', 10, 'application/pdf'),
        ]);

        $response->assertRedirect(route('dokumen'));
        $document = Document::where('user_id', $student->id)->firstOrFail();
        $this->assertSame('Surat Pernyataan', $document->nama_dokumen);
        Storage::disk('public')->assertExists($document->file_path);
    }

    public function test_additional_document_requires_a_custom_name(): void
    {
        Storage::fake('public');
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($student)
            ->post(route('dokumen.upload'), [
                'nama_dokumen' => 'Lainnya',
                'jenis_dokumen' => 'Administrasi',
                'file' => UploadedFile::fake()->create('surat.pdf', 10, 'application/pdf'),
            ])
            ->assertSessionHasErrors('nama_dokumen_lain');

        $this->assertDatabaseCount('documents', 0);
    }

    public function test_student_can_request_a_seminar_and_the_program_can_set_its_schedule(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $prodi = User::factory()->create(['role' => 'prodi']);
        $advisorOne = User::factory()->create(['role' => 'dosen']);
        $advisorTwo = User::factory()->create(['role' => 'dosen']);
        $tugasAkhir = TugasAkhir::create([
            'user_id' => $student->id,
            'judul' => 'Sistem Informasi',
            'topik' => 'Rekayasa Perangkat Lunak',
            'status' => 'Disetujui',
            'dosen_pembimbing_1' => $advisorOne->id,
            'dosen_pembimbing_2' => $advisorTwo->id,
        ]);

        $this->actingAs($student)
            ->post(route('tugas-akhir.daftar-sempro.store'))
            ->assertRedirect(route('tugas-akhir.daftar-sempro'));
        $this->actingAs($student)
            ->get(route('tugas-akhir.daftar-sempro'))
            ->assertOk()
            ->assertSee('Menunggu');
        $this->actingAs($student)
            ->get(route('tugas-akhir.daftar-sidang'))
            ->assertOk()
            ->assertSee('Seminar Proposal harus ditandai selesai');
        $jadwal = Jadwal::where('user_id', $student->id)->firstOrFail();
        $this->assertSame('menunggu', $jadwal->status);

        $this->actingAs($prodi)
            ->put(route('prodi.jadwal.tetapkan', $jadwal), [
                'waktu' => now()->addDay()->format('Y-m-d\TH:i'),
                'lokasi' => 'Ruang Seminar 1',
                'dosen_penguji' => 'Tim Penguji',
            ])
            ->assertRedirect(route('prodi.jadwal'));

        $this->assertDatabaseHas('jadwals', [
            'id' => $jadwal->id,
            'tugas_akhir_id' => $tugasAkhir->id,
            'status' => 'terjadwal',
            'lokasi' => 'Ruang Seminar 1',
        ]);
        $this->assertSame(1, $student->notifications()->count());
        $this->actingAs($student)
            ->get(route('jadwal'))
            ->assertOk()
            ->assertSee('Ruang Seminar 1')
            ->assertSee('Tim Penguji');
        $this->actingAs($prodi)->get(route('prodi.jadwal'))->assertOk();
    }

    public function test_prodi_can_review_a_title_and_assign_advisors(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $prodi = User::factory()->create(['role' => 'prodi']);
        $advisorOne = User::factory()->create(['role' => 'dosen']);
        $advisorTwo = User::factory()->create(['role' => 'dosen']);
        $tugasAkhir = TugasAkhir::create([
            'user_id' => $student->id,
            'judul' => 'Sistem Informasi',
            'topik' => 'Rekayasa Perangkat Lunak',
            'status' => 'Menunggu',
        ]);

        $this->actingAs($prodi)
            ->get(route('prodi.pengajuan-tugas-akhir'))
            ->assertOk()
            ->assertSee('Simpan Review')
            ->assertSee('Sistem Informasi');

        $this->actingAs($prodi)
            ->put(route('prodi.pengajuan-tugas-akhir.update', $tugasAkhir), [
                'status' => 'Disetujui',
                'dosen_pembimbing_1' => $advisorOne->id,
                'dosen_pembimbing_2' => $advisorTwo->id,
                'catatan_prodi' => 'Judul diterima.',
            ])
            ->assertRedirect(route('prodi.pengajuan-tugas-akhir'));

        $this->assertDatabaseHas('tugas_akhirs', [
            'id' => $tugasAkhir->id,
            'status' => 'Disetujui',
            'dosen_pembimbing_1' => $advisorOne->id,
            'dosen_pembimbing_2' => $advisorTwo->id,
            'catatan_prodi' => 'Judul diterima.',
        ]);
        $this->assertSame(1, $student->notifications()->count());
    }

    public function test_prodi_cannot_create_a_sempro_schedule_without_a_student_registration(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $prodi = User::factory()->create(['role' => 'prodi']);
        TugasAkhir::create([
            'user_id' => $student->id,
            'judul' => 'Sistem Informasi',
            'topik' => 'Rekayasa Perangkat Lunak',
            'status' => 'Disetujui',
        ]);

        $this->actingAs($prodi)
            ->post(route('prodi.jadwal.store'), [
                'user_id' => $student->id,
                'jenis' => 'sempro',
                'waktu' => now()->addDay()->format('Y-m-d\TH:i'),
                'lokasi' => 'Ruang Seminar 1',
                'dosen_penguji' => 'Tim Penguji',
            ])
            ->assertSessionHasErrors('jenis');

        $this->assertDatabaseCount('jadwals', 0);
    }

    public function test_sidang_registration_requires_a_completed_sempro(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $prodi = User::factory()->create(['role' => 'prodi']);
        $advisorOne = User::factory()->create(['role' => 'dosen']);
        $advisorTwo = User::factory()->create(['role' => 'dosen']);
        $tugasAkhir = TugasAkhir::create([
            'user_id' => $student->id,
            'judul' => 'Sistem Informasi',
            'topik' => 'Rekayasa Perangkat Lunak',
            'status' => 'Disetujui',
            'dosen_pembimbing_1' => $advisorOne->id,
            'dosen_pembimbing_2' => $advisorTwo->id,
        ]);

        $this->actingAs($student)
            ->post(route('tugas-akhir.daftar-sidang.store'))
            ->assertSessionHasErrors();
        $this->assertDatabaseCount('jadwals', 0);

        $sempro = Jadwal::create([
            'user_id' => $student->id,
            'tugas_akhir_id' => $tugasAkhir->id,
            'jenis' => 'sempro',
            'waktu' => now()->subDay(),
            'lokasi' => 'Ruang Seminar 1',
            'dosen_penguji' => 'Tim Penguji',
            'status' => 'terjadwal',
        ]);

        $this->actingAs($prodi)
            ->patch(route('prodi.jadwal.selesai', $sempro))
            ->assertRedirect(route('prodi.jadwal'));
        $this->assertDatabaseHas('jadwals', [
            'id' => $sempro->id,
            'status' => 'selesai',
        ]);

        $this->actingAs($student)
            ->post(route('tugas-akhir.daftar-sidang.store'))
            ->assertRedirect(route('tugas-akhir.daftar-sidang'));
        $this->assertDatabaseHas('jadwals', [
            'user_id' => $student->id,
            'jenis' => 'sidang',
            'status' => 'menunggu',
        ]);
    }

    public function test_search_only_returns_the_signed_in_students_records(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $otherStudent = User::factory()->create(['role' => 'mahasiswa']);
        TugasAkhir::create([
            'user_id' => $student->id,
            'judul' => 'Penelitian Saya',
            'topik' => 'Sistem Informasi',
            'status' => 'Menunggu',
        ]);
        TugasAkhir::create([
            'user_id' => $otherStudent->id,
            'judul' => 'Rahasia Mahasiswa Lain',
            'topik' => 'Sistem Informasi',
            'status' => 'Menunggu',
        ]);

        $this->actingAs($student)
            ->get(route('cari', ['q' => 'Sistem Informasi']))
            ->assertOk()
            ->assertSee('Penelitian Saya')
            ->assertDontSee('Rahasia Mahasiswa Lain');
    }

    public function test_prodi_can_publish_announcements_and_students_receive_notifications(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $prodi = User::factory()->create(['role' => 'prodi']);

        $this->actingAs($prodi)
            ->post(route('prodi.pengumuman.store'), [
                'judul' => 'Pendaftaran Sempro Dibuka',
                'isi' => 'Silakan periksa persyaratan.',
            ])
            ->assertRedirect(route('prodi.pengumuman'));

        $this->assertSame(1, $student->notifications()->count());
        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pendaftaran Sempro Dibuka');
        $this->actingAs($student)
            ->get(route('notifikasi'))
            ->assertOk()
            ->assertSee('Pengumuman baru: Pendaftaran Sempro Dibuka');
        $this->assertSame(0, $student->fresh()->unreadNotifications()->count());
    }

    public function test_student_can_change_password_after_confirming_current_password(): void
    {
        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'password' => Hash::make('Current-pass-123'),
        ]);

        $response = $this->actingAs($student)->post(route('profil.password'), [
            'current_password' => 'Current-pass-123',
            'password' => 'New-pass-456',
            'password_confirmation' => 'New-pass-456',
        ]);

        $response->assertRedirect(route('profil'));
        $this->assertTrue(Hash::check('New-pass-456', $student->fresh()->password));
    }

    public function test_student_can_save_profile_details_and_keep_phone_leading_zeroes(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($student)
            ->put(route('profil.data-diri'), [
                'angkatan' => 2024,
                'program_studi' => 'Sistem Informasi',
                'no_hp' => '0895340309099',
            ])
            ->assertRedirect(route('profil'));

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'angkatan' => 2024,
            'program_studi' => 'Sistem Informasi',
            'no_hp' => '0895340309099',
        ]);
        $this->actingAs($student)
            ->get(route('profil'))
            ->assertOk()
            ->assertSee('value="2024"', false)
            ->assertSee('value="0895340309099"', false)
            ->assertSee('value="Sistem Informasi"', false);
    }

    public function test_student_can_upload_a_profile_photo(): void
    {
        Storage::fake('public');
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($student)
            ->post(route('profil.foto'), [
                'foto' => UploadedFile::fake()->createWithContent(
                    'profile.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC')
                ),
            ])
            ->assertRedirect(route('profil'));

        $this->assertNotNull($student->fresh()->foto_path);
        Storage::disk('public')->assertExists($student->fresh()->foto_path);
    }

    public function test_password_reset_link_and_reset_token_update_the_password(): void
    {
        Notification::fake();
        $student = User::factory()->create([
            'email' => 'mahasiswa@example.test',
            'password' => Hash::make('Old-pass-123'),
        ]);

        $this->post(route('password.email'), ['email' => $student->email])
            ->assertRedirect()
            ->assertSessionHas('status');
        Notification::assertSentTo($student, ResetPassword::class);

        $token = Password::createToken($student);
        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $student->email,
            'password' => 'Reset-pass-456',
            'password_confirmation' => 'Reset-pass-456',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('Reset-pass-456', $student->fresh()->password));
    }
}
