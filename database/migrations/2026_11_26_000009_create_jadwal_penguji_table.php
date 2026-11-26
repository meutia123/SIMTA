<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_penguji', function (Blueprint $table) {
            $table->foreignId('jadwal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dosen_id')->constrained('users')->cascadeOnDelete();
            $table->primary(['jadwal_id', 'dosen_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_penguji');
    }
};
