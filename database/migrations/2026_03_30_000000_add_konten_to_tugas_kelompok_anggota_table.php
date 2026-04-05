<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tugas_kelompok_anggota', function (Blueprint $table) {
            $table->longText('konten')->nullable()->after('topik');
        });
    }

    public function down(): void
    {
        Schema::table('tugas_kelompok_anggota', function (Blueprint $table) {
            $table->dropColumn('konten');
        });
    }
};
