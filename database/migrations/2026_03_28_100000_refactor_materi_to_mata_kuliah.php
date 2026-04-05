<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── pokok_bahasan: kelas_id → mata_kuliah_id ──────────────
        Schema::table('pokok_bahasan', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropIndex(['kelas_id', 'urutan']);
            $table->dropColumn('kelas_id');

            $table->foreignId('mata_kuliah_id')
                  ->after('id')
                  ->constrained('mata_kuliah')
                  ->cascadeOnDelete();

            $table->index(['mata_kuliah_id', 'instruktur_id', 'urutan']);
        });

        // ── materi: kelas_id → mata_kuliah_id ────────────────────
        Schema::table('materi', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');

            $table->foreignId('mata_kuliah_id')
                  ->after('id')
                  ->constrained('mata_kuliah')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pokok_bahasan', function (Blueprint $table) {
            $table->dropIndex(['mata_kuliah_id', 'instruktur_id', 'urutan']);
            $table->dropForeign(['mata_kuliah_id']);
            $table->dropColumn('mata_kuliah_id');

            $table->foreignId('kelas_id')->after('id')->constrained('kelas')->cascadeOnDelete();
            $table->index(['kelas_id', 'urutan']);
        });

        Schema::table('materi', function (Blueprint $table) {
            $table->dropForeign(['mata_kuliah_id']);
            $table->dropColumn('mata_kuliah_id');

            $table->foreignId('kelas_id')->after('id')->constrained('kelas')->cascadeOnDelete();
        });
    }
};
