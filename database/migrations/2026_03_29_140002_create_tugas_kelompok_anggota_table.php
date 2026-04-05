<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas_kelompok_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_id')->constrained('tugas_kelompok')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->string('topik', 300)->nullable();
            $table->enum('status_submit', ['belum', 'submitted'])->default('belum');
            $table->text('catatan_instruktur')->nullable();
            $table->unsignedTinyInteger('nilai')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['kelompok_id', 'mahasiswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas_kelompok_anggota');
    }
};
