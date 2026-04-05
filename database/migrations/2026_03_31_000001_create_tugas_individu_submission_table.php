<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas_individu_submission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugas')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->string('pdf_path', 500)->nullable();
            $table->enum('status_submit', ['belum', 'submitted'])->default('belum');
            $table->timestamp('submitted_at')->nullable();
            $table->tinyInteger('nilai')->unsigned()->nullable();
            $table->text('catatan_instruktur')->nullable();
            $table->text('catatan_ai')->nullable();
            $table->timestamps();

            $table->unique(['tugas_id', 'mahasiswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas_individu_submission');
    }
};
