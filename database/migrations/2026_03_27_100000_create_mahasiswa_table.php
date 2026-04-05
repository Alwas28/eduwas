<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('jurusan_id')->nullable()->constrained('jurusan')->nullOnDelete();
            $table->string('nim', 20)->unique();
            $table->string('nama', 150);
            $table->string('email', 100)->nullable()->unique();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->smallInteger('angkatan')->nullable();           // e.g. 2022
            $table->enum('status', ['Aktif', 'Cuti', 'Dropout', 'Lulus'])->default('Aktif');
            $table->timestamps();

            $table->index('jurusan_id');
            $table->index(['angkatan', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
