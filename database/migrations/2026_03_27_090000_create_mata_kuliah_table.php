<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurusan_id')->constrained('jurusan')->cascadeOnDelete();
            $table->string('kode', 20)->unique();
            $table->string('nama', 150);
            $table->tinyInteger('sks')->unsigned()->default(2);       // 1-6
            $table->tinyInteger('semester')->unsigned()->nullable();   // 1-8, null = semua semester
            $table->enum('jenis', ['Wajib', 'Pilihan'])->default('Wajib');
            $table->string('deskripsi')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->index('jurusan_id');
            $table->index(['jurusan_id', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah');
    }
};
