<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->decimal('nilai_presentasi', 5, 2)->nullable();
            $table->decimal('nilai_naskah', 5, 2)->nullable();
            $table->decimal('nilai_penguasaan', 5, 2)->nullable();
            $table->string('status_validasi_nilai', 30)->nullable()->index();
            $table->text('catatan_validasi_nilai')->nullable();
            $table->foreignId('dosen_penilai_id')->nullable()->constrained('users')->nullOnDelete();
        });

        DB::table('jadwals')
            ->whereNotNull('nilai')
            ->whereNotNull('hasil')
            ->update(['status_validasi_nilai' => 'tervalidasi']);
    }

    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropForeign(['dosen_penilai_id']);
            $table->dropIndex(['status_validasi_nilai']);
            $table->dropColumn([
                'nilai_presentasi',
                'nilai_naskah',
                'nilai_penguasaan',
                'status_validasi_nilai',
                'catatan_validasi_nilai',
                'dosen_penilai_id',
            ]);
        });
    }
};
