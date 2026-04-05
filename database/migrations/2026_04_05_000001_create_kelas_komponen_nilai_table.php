<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas_komponen_nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('instruktur_id')->constrained('instruktur')->cascadeOnDelete();
            $table->enum('tipe', ['tugas', 'ujian']);
            $table->unsignedBigInteger('sumber_id')->nullable()->comment('tugas.id or ujian.id');
            $table->string('label', 100);
            $table->unsignedTinyInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['kelas_id', 'instruktur_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas_komponen_nilai');
    }
};
