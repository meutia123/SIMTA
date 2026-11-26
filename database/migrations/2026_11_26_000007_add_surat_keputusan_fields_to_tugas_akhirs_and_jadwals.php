<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tugas_akhirs', function (Blueprint $table) {
            $table->string('sk_pembimbing_nomor')->nullable();
            $table->date('sk_pembimbing_tanggal')->nullable();
            $table->string('sk_pembimbing_file')->nullable();
            $table->timestamp('sk_pembimbing_diminta_at')->nullable();
        });

        Schema::table('jadwals', function (Blueprint $table) {
            $table->string('sk_tim_penguji_nomor')->nullable();
            $table->date('sk_tim_penguji_tanggal')->nullable();
            $table->string('sk_tim_penguji_file')->nullable();
            $table->timestamp('sk_tim_penguji_diminta_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropColumn([
                'sk_tim_penguji_nomor',
                'sk_tim_penguji_tanggal',
                'sk_tim_penguji_file',
                'sk_tim_penguji_diminta_at',
            ]);
        });

        Schema::table('tugas_akhirs', function (Blueprint $table) {
            $table->dropColumn([
                'sk_pembimbing_nomor',
                'sk_pembimbing_tanggal',
                'sk_pembimbing_file',
                'sk_pembimbing_diminta_at',
            ]);
        });
    }
};
