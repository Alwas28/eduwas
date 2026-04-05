<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pool soal yang instruktur pilih untuk ujian
        Schema::create('ujian_soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujian')->cascadeOnDelete();
            $table->foreignId('bank_soal_id')->constrained('bank_soal')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['ujian_id', 'bank_soal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian_soal');
    }
};
