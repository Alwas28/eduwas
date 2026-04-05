<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->foreignId('periode_akademik_id')->constrained('periode_akademik')->cascadeOnDelete();
            $table->string('kode_seksi', 10)->nullable();        // A, B, Reguler, dll
            $table->unsignedSmallInteger('kapasitas')->nullable();
            $table->enum('status', ['Aktif', 'Selesai', 'Dibatalkan'])->default('Aktif');
            $table->timestamps();

            $table->index(['mata_kuliah_id', 'periode_akademik_id']);
        });

        Schema::create('kelas_instruktur', function (Blueprint $table) {
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('instruktur_id')->constrained('instruktur')->cascadeOnDelete();
            $table->primary(['kelas_id', 'instruktur_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas_instruktur');
        Schema::dropIfExists('kelas');
    }
};
