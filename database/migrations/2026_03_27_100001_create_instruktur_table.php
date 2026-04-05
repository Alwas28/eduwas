<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instruktur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nidn', 20)->nullable()->unique();
            $table->string('nip', 30)->nullable()->unique();
            $table->string('nama', 150);
            $table->string('email', 100)->nullable()->unique();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('bidang_keahlian', 150)->nullable();
            $table->enum('pendidikan_terakhir', ['S1', 'S2', 'S3'])->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instruktur');
    }
};
