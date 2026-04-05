<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pokok_bahasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('instruktur_id')->constrained('instruktur')->cascadeOnDelete();
            $table->unsignedTinyInteger('pertemuan');
            $table->string('judul', 200);
            $table->text('deskripsi')->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['kelas_id', 'urutan']);
        });

        Schema::table('materi', function (Blueprint $table) {
            $table->foreignId('pokok_bahasan_id')
                  ->nullable()
                  ->after('instruktur_id')
                  ->constrained('pokok_bahasan')
                  ->nullOnDelete();
        });

        Schema::table('kelas', function (Blueprint $table) {
            $table->string('rps_path')->nullable()->after('enroll_token');
            $table->string('rps_nama_file', 255)->nullable()->after('rps_path');
            $table->unsignedBigInteger('rps_ukuran')->nullable()->after('rps_nama_file');
        });
    }

    public function down(): void
    {
        Schema::table('materi', fn($t) => $t->dropConstrainedForeignId('pokok_bahasan_id'));
        Schema::table('kelas',  fn($t) => $t->dropColumn(['rps_path', 'rps_nama_file', 'rps_ukuran']));
        Schema::dropIfExists('pokok_bahasan');
    }
};
