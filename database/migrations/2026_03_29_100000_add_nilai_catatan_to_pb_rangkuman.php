<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pb_rangkuman', function (Blueprint $table) {
            $table->unsignedTinyInteger('nilai')->nullable()->after('isi');
            $table->string('catatan', 1000)->nullable()->after('nilai');
        });
    }

    public function down(): void
    {
        Schema::table('pb_rangkuman', function (Blueprint $table) {
            $table->dropColumn(['nilai', 'catatan']);
        });
    }
};
