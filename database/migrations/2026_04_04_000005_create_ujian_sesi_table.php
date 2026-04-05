<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sesi per mahasiswa: soal mana saja + urutan yang mereka dapat
        Schema::create('ujian_sesi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujian')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->json('soal_ids'); // array soal IDs dalam urutan yang sudah di-random
            $table->datetime('mulai_at')->nullable();
            $table->datetime('selesai_at')->nullable();
            $table->datetime('submitted_at')->nullable();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['ujian_id', 'mahasiswa_id']);
        });

        // Jawaban mahasiswa per soal
        Schema::create('ujian_jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained('ujian_sesi')->cascadeOnDelete();
            $table->foreignId('bank_soal_id')->constrained('bank_soal')->cascadeOnDelete();
            $table->text('jawaban_essay')->nullable();
            $table->unsignedTinyInteger('jawaban_pg')->nullable(); // index pilihan yang dipilih
            $table->boolean('is_benar')->nullable(); // null = belum dinilai (essay)
            $table->decimal('nilai', 5, 2)->nullable();
            $table->text('feedback_ai')->nullable();
            $table->timestamps();

            $table->unique(['sesi_id', 'bank_soal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian_jawaban');
        Schema::dropIfExists('ujian_sesi');
    }
};
