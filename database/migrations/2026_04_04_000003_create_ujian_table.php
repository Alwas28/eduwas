<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instruktur_id')->constrained('instruktur')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->datetime('waktu_mulai');
            $table->datetime('waktu_selesai');
            $table->unsignedSmallInteger('durasi'); // menit
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft');

            // Essay settings
            $table->boolean('ada_essay')->default(false);
            $table->unsignedTinyInteger('jumlah_soal_essay')->nullable(); // null = semua
            $table->boolean('acak_soal_essay')->default(false);

            // Pilihan ganda settings
            $table->boolean('ada_pg')->default(false);
            $table->unsignedTinyInteger('jumlah_soal_pg')->nullable(); // null = semua
            $table->boolean('acak_soal_pg')->default(false);
            $table->boolean('acak_pilihan_pg')->default(false); // acak urutan opsi A/B/C/D

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian');
    }
};
