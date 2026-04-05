<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komponen_ujian_pilihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('komponen_id')->constrained('kelas_komponen_nilai')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('ujian_sesi_id')->constrained('ujian_sesi')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['komponen_id', 'mahasiswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komponen_ujian_pilihan');
    }
};
