<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->enum('status', ['Aktif', 'Dropout', 'Lulus'])->default('Aktif');
            $table->decimal('nilai_akhir', 5, 2)->nullable();   // 0.00 – 100.00
            $table->text('catatan')->nullable();
            $table->date('enrolled_at')->nullable();
            $table->timestamps();

            $table->unique(['kelas_id', 'mahasiswa_id']);
            $table->index(['kelas_id', 'status']);
            $table->index('mahasiswa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
