<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jika tabel lama masih ada (dari migration sebelumnya), hapus dulu
        Schema::dropIfExists('diskusi');

        Schema::create('diskusi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pokok_bahasan_id')->constrained('pokok_bahasan')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('pesan');
            $table->timestamps();

            $table->index(['pokok_bahasan_id', 'kelas_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diskusi');
    }
};
