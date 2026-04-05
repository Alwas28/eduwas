<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materi_akses', function (Blueprint $table) {
            $table->tinyInteger('progress')->unsigned()->default(0)->after('jumlah_akses');
        });
    }

    public function down(): void
    {
        Schema::table('materi_akses', function (Blueprint $table) {
            $table->dropColumn('progress');
        });
    }
};
