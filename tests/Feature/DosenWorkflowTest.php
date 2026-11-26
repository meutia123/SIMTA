<?php

namespace Tests\Feature;

use App\Models\Jadwal;
use App\Models\Document;
use App\Models\TugasAkhir;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
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
            ->assertSee('Jadwal Terdekat');

        $this->get(route('dosen.jadwal'))
            ->assertOk()
            ->assertSee('Ruang Seminar')
            ->assertSee('Mahasiswa Bimbingan');

        $this->get(route('dosen.keterlibatan'))
            ->assertOk()
            ->assertSee('Rekap Keterlibatan Saya')
            ->assertSee('Sistem Informasi Perpustakaan')
            ->assertSee('Penguji Sempro');

        $this->get(route('dosen.profil'))->assertOk()->assertSee($dosen->name);
    }

    public function test_dosen_can_filter_students_by_role_and_only_open_assigned_student_details(): void
    {
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Terlibat']);
        $otherDosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Lain']);
        $advisorStudent = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Bimbingan']);
        $examStudent = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Penguji']);
        $unrelatedStudent = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Lain']);
        $advisorTask = TugasAkhir::create([
            'user_id' => $advisorStudent->id,
            'judul' => 'Judul Bimbingan',
            'topik' => 'Sistem Informasi',
            'status' => 'Disetujui',
            'dosen_pembimbing_1' => $dosen->id,
            'dosen_pembimbing_2' => $otherDosen->id,
        ]);
        $examTask = TugasAkhir::create([
            'user_id' => $examStudent->id,
            'judul' => 'Judul Penguji',
            'topik' => 'Sistem Informasi',
            'status' => 'Disetujui',
            'dosen_pembimbing_1' => $otherDosen->id,
            'dosen_pembimbing_2' => User::factory()->create(['role' => 'dosen'])->id,
        ]);
        $examSchedule = Jadwal::create([
            'user_id' => $examStudent->id,
            'tugas_akhir_id' => $examTask->id,
            'jenis' => 'sempro',
            'waktu' => now()->addDay(),
            'status' => 'terjadwal',
        ]);
        $examSchedule->penguji()->attach($dosen->id);
        $unrelatedTask = TugasAkhir::create([
            'user_id' => $unrelatedStudent->id,
            'judul' => 'Judul Tidak Terkait',
            'topik' => 'Sistem Informasi',
            'status' => 'Menunggu',
        ]);

        $this->actingAs($dosen)
            ->get(route('dosen.mahasiswa'))
            ->assertOk()
            ->assertSee('Mahasiswa Bimbingan')
            ->assertSee('Mahasiswa Penguji')
            ->assertDontSee('Mahasiswa Lain');

        $this->get(route('dosen.mahasiswa', ['jenis' => 'pembimbing']))
            ->assertOk()
            ->assertSee('Mahasiswa Bimbingan')
            ->assertDontSee('Mahasiswa Penguji');

        $this->get(route('dosen.mahasiswa', ['jenis' => 'penguji']))
            ->assertOk()
            ->assertSee('Mahasiswa Penguji')
            ->assertDontSee('Mahasiswa Bimbingan');

        $this->get(route('dosen.mahasiswa.show', $examTask))
            ->assertOk()
            ->assertSee('Dosen Pembimbing')
            ->assertSee('Penguji');

        $this->get(route('dosen.mahasiswa.show', $unrelatedTask))->assertForbidden();
    }

    public function test_dosen_can_filter_schedule_and_access_related_documents(): void
    {
        Storage::fake('public');
        $dosen = User::factory()->create(['role' => 'dosen', 'name' => 'Dosen Penilai']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Mahasiswa Ujian']);
        $task = TugasAkhir::create([
            'user_id' => $student->id,
            'judul' => 'Judul Ujian',
            'topik' => 'Sistem Informasi',
            'status' => 'Disetujui',
            'dosen_pembimbing_1' => User::factory()->create(['role' => 'dosen'])->id,
            'dosen_pembimbing_2' => User::factory()->create(['role' => 'dosen'])->id,
        ]);
        $sempro = Jadwal::create([
            'user_id' => $student->id,
            'tugas_akhir_id' => $task->id,
            'jenis' => 'sempro',
            'waktu' => now()->subHour(),
            'lokasi' => 'Ruang Seminar',
            'status' => 'selesai',
        ]);
        $sempro->penguji()->attach($dosen->id);
        $sidang = Jadwal::create([
            'user_id' => $student->id,
            'tugas_akhir_id' => $task->id,
            'jenis' => 'sidang',
            'waktu' => now()->addDay(),
            'lokasi' => 'Ruang Sidang',
            'status' => 'terjadwal',
        ]);
        $sidang->penguji()->attach($dosen->id);
        Storage::disk('public')->put('documents/student/proposal.pdf', 'proposal');
        $document = Document::create([
            'user_id' => $student->id,
            'tugas_akhir_id' => $task->id,
            'nama_dokumen' => 'Proposal',
            'jenis_dokumen' => 'Proposal',
            'file_path' => 'documents/student/proposal.pdf',
            'file_name' => 'proposal.pdf',
            'status' => 'diupload',
        ]);

        $this->actingAs($dosen)
            ->get(route('dosen.jadwal', ['jenis' => 'sempro']))
            ->assertOk()
            ->assertSee('Ruang Seminar')
            ->assertDontSee('Ruang Sidang')
            ->assertSee('Penguji');

        $this->get(route('dosen.input-nilai'))
            ->assertOk()
            ->assertSee('Mahasiswa Ujian')
            ->assertSee('Input Nilai');

        $this->get(route('dosen.dokumen'))
            ->assertOk()
            ->assertSee('Proposal')
            ->assertSee('Mahasiswa Ujian');

        $this->get(route('dosen.dokumen.download', $document))
            ->assertOk();
    }
}
