<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ujian_sesi', function (Blueprint $table) {
            $table->unsignedSmallInteger('pelanggaran')->default(0)->after('nilai');
            $table->timestamp('last_ping_at')->nullable()->after('pelanggaran');
        });

        Schema::create('ujian_pelanggaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained('ujian_sesi')->cascadeOnDelete();
            $table->string('tipe', 50); // tab_switch, window_blur, copy_attempt, keyboard_shortcut
            $table->string('catatan', 255)->nullable();
            $table->timestamp('terjadi_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian_pelanggaran');
        Schema::table('ujian_sesi', function (Blueprint $table) {
            $table->dropColumn(['pelanggaran', 'last_ping_at']);
        });
    }
};
