<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materi_akses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materi_id')->constrained('materi')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->unsignedInteger('jumlah_akses')->default(1);
            $table->timestamp('pertama_diakses_at')->nullable();
            $table->timestamp('terakhir_diakses_at')->nullable();
            $table->timestamps();
            $table->unique(['materi_id', 'user_id', 'kelas_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi_akses');
    }
};
