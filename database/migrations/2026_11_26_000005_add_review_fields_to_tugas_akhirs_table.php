<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tugas_akhirs', function (Blueprint $table) {
            $table->foreignId('dosen_pembimbing_1')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dosen_pembimbing_2')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_prodi')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tugas_akhirs', function (Blueprint $table) {
            $table->dropForeign(['dosen_pembimbing_1']);
            $table->dropForeign(['dosen_pembimbing_2']);
            $table->dropColumn(['dosen_pembimbing_1', 'dosen_pembimbing_2', 'catatan_prodi']);
        });
    }
};
