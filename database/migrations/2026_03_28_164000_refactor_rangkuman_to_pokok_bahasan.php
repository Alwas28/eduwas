<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus rangkuman_aktif dari materi
        Schema::table('materi', function (Blueprint $table) {
            $table->dropColumn('rangkuman_aktif');
        });

        // Tambah rangkuman_aktif ke pokok_bahasan
        Schema::table('pokok_bahasan', function (Blueprint $table) {
            $table->boolean('rangkuman_aktif')->default(false)->after('urutan');
        });

        // Ganti tabel per-materi menjadi per-PB
        Schema::dropIfExists('materi_rangkuman');

        Schema::create('pb_rangkuman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pokok_bahasan_id')->constrained('pokok_bahasan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->text('isi');
            $table->timestamps();

            $table->unique(['pokok_bahasan_id', 'user_id', 'kelas_id']);
        });
    }

    public function down(): void
    {
        Schema::table('pokok_bahasan', function (Blueprint $table) {
            $table->dropColumn('rangkuman_aktif');
        });

        Schema::table('materi', function (Blueprint $table) {
            $table->boolean('rangkuman_aktif')->default(false)->after('allow_download');
        });

        Schema::dropIfExists('pb_rangkuman');

        Schema::create('materi_rangkuman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materi_id')->constrained('materi')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->text('isi');
            $table->timestamps();

            $table->unique(['materi_id', 'user_id', 'kelas_id']);
        });
    }
};
