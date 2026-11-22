<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tugas_akhirs', function (Blueprint $table) {
            $table->string('topik')->nullable();

            $table->foreignId('calon_dosen_1')
                ->nullable()->constrained('users');

            $table->foreignId('calon_dosen_2')
                ->nullable()->constrained('users');

            $table->foreignId('calon_dosen_3')
                ->nullable()->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::table('tugas_akhirs', function (Blueprint $table) {
            $table->dropForeign(['calon_dosen_1']);
            $table->dropForeign(['calon_dosen_2']);
            $table->dropForeign(['calon_dosen_3']);

            $table->dropColumn([
                'topik',
                'calon_dosen_1',
                'calon_dosen_2',
                'calon_dosen_3',
            ]);
        });
    }
};