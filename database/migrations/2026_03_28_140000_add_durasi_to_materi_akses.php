<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materi_akses', function (Blueprint $table) {
            $table->unsignedInteger('durasi_detik')->default(0)->after('progress');
        });
    }

    public function down(): void
    {
        Schema::table('materi_akses', function (Blueprint $table) {
            $table->dropColumn('durasi_detik');
        });
    }
};
