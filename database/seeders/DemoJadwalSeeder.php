<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use App\Models\TugasAkhir;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class DemoJadwalSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Demo schedules may only be seeded in local or testing environments.');
        }

        $mahasiswa = User::where('nim', '2417020095')
            ->where('role', 'mahasiswa')
            ->first();

        if (!$mahasiswa) {
            throw new RuntimeException('The local demo student account was not found.');
        }

        $tugasAkhir = TugasAkhir::where('user_id', $mahasiswa->id)->latest()->first();
        if (!$tugasAkhir) {
            throw new RuntimeException('The local demo student must have a thesis submission before schedules are seeded.');
        }

        $jadwalContoh = [
            [
                'jenis' => 'bimbingan',
                'waktu' => now()->subDays(7)->setTime(10, 0),
                'lokasi' => 'Ruang Dosen 204',
                'dosen_penguji' => 'Dosen Pembimbing',
                'status' => 'selesai',
                'catatan' => 'DEMO: Bimbingan yang sudah selesai.',
            ],
            [
                'jenis' => 'bimbingan',
                'waktu' => now()->addDays(2)->setTime(13, 30),
                'lokasi' => 'Ruang Dosen 204',
                'dosen_penguji' => 'Dosen Pembimbing',
                'status' => 'terjadwal',
                'catatan' => 'DEMO: Bimbingan yang akan datang.',
            ],
            [
                'jenis' => 'bimbingan',
                'waktu' => now()->addDays(7)->setTime(9, 0),
                'lokasi' => 'Ruang Dosen 204',
                'dosen_penguji' => 'Dosen Pembimbing',
                'status' => 'terjadwal',
                'catatan' => 'DEMO: Bimbingan lanjutan.',
            ],
        ];

        foreach ($jadwalContoh as $jadwal) {
            Jadwal::firstOrCreate([
                'user_id' => $mahasiswa->id,
                'jenis' => $jadwal['jenis'],
                'catatan' => $jadwal['catatan'],
            ], [
                ...$jadwal,
                'tugas_akhir_id' => $tugasAkhir->id,
            ]);
        }
    }
}
