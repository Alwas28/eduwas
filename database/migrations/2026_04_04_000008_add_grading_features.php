<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ujian_sesi', function (Blueprint $table) {
            $table->enum('nilai_status', ['draft', 'public'])->default('draft')->after('nilai');
        });

        Schema::table('ujian_jawaban', function (Blueprint $table) {
            $table->text('feedback_instruktur')->nullable()->after('feedback_ai');
        });
    }

    public function down(): void
    {
        Schema::table('ujian_jawaban', function (Blueprint $table) {
            $table->dropColumn('feedback_instruktur');
        });

        Schema::table('ujian_sesi', function (Blueprint $table) {
            $table->dropColumn('nilai_status');
        });
    }
};
