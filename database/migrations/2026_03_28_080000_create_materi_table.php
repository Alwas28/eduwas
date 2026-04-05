<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('instruktur_id')->constrained('instruktur')->cascadeOnDelete();
            $table->string('judul', 200);
            $table->text('deskripsi')->nullable();
            $table->enum('tipe', ['dokumen', 'video', 'link', 'teks'])->default('dokumen');
            $table->string('file_path')->nullable();
            $table->string('nama_file', 255)->nullable();
            $table->unsignedBigInteger('ukuran_file')->nullable(); // bytes
            $table->string('url', 1000)->nullable();
            $table->longText('konten')->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->enum('status', ['Aktif', 'Draft'])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi');
    }
};
