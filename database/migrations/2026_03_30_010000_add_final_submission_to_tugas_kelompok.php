<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tugas_kelompok', function (Blueprint $table) {
            $table->longText('konten_final')->nullable()->after('nama_kelompok');
            $table->enum('status_submit', ['belum', 'submitted'])->default('belum')->after('konten_final');
            $table->timestamp('submitted_at')->nullable()->after('status_submit');
            $table->string('pdf_path', 500)->nullable()->after('submitted_at');
            $table->unsignedTinyInteger('nilai_kelompok')->nullable()->after('pdf_path');
            $table->text('catatan_kelompok')->nullable()->after('nilai_kelompok');
        });
    }

    public function down(): void
    {
        Schema::table('tugas_kelompok', function (Blueprint $table) {
            $table->dropColumn([
                'konten_final', 'status_submit', 'submitted_at',
                'pdf_path', 'nilai_kelompok', 'catatan_kelompok',
            ]);
        });
    }
};
