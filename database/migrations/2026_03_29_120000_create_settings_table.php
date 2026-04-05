<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label');
            $table->string('description', 500)->nullable();
            $table->string('type', 30)->default('text'); // text, password, select, textarea
            $table->string('group', 80)->default('Umum');
            $table->boolean('is_secret')->default(false);
            $table->timestamps();
        });

        // Seed default settings
        $now = now();
        DB::table('settings')->insert([
            ['key' => 'ai_assistant_name',  'value' => 'Tanya Asdos', 'label' => 'Nama Asisten AI',   'description' => 'Nama yang ditampilkan untuk fitur chat AI kepada mahasiswa.',      'type' => 'text',     'group' => 'Asisten AI',  'is_secret' => false, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'openrouter_api_key', 'value' => null,          'label' => 'API Key OpenRouter', 'description' => 'Kunci API dari openrouter.ai untuk mengaktifkan fitur AI Chat.', 'type' => 'password', 'group' => 'OpenRouter', 'is_secret' => true,  'created_at' => $now, 'updated_at' => $now],
            ['key' => 'openrouter_model',   'value' => 'google/gemma-3-27b-it:free', 'label' => 'Model AI', 'description' => 'Model AI yang digunakan. Pilih model gratis untuk menghemat kuota.', 'type' => 'select', 'group' => 'OpenRouter', 'is_secret' => false, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Seed access permissions for settings
        DB::table('accesses')->insertOrIgnore([
            ['name' => 'lihat.pengaturan', 'display_name' => 'Lihat Pengaturan', 'group' => 'Pengaturan', 'description' => 'Membuka halaman pengaturan sistem', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'edit.pengaturan',  'display_name' => 'Edit Pengaturan',  'group' => 'Pengaturan', 'description' => 'Mengubah nilai pengaturan sistem',   'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        DB::table('accesses')->whereIn('name', ['lihat.pengaturan', 'edit.pengaturan'])->delete();
    }
};
