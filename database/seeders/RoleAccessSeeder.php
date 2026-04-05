<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleAccessSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ──────────────────────────────────────────────
        $roles = [
            ['id' => 1, 'name' => 'admin',      'display_name' => 'Administrator'],
            ['id' => 2, 'name' => 'mahasiswa',   'display_name' => 'Mahasiswa'],
            ['id' => 3, 'name' => 'instruktur',  'display_name' => 'Instruktur'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(['id' => $role['id']], $role);
        }

        // ── Accesses ───────────────────────────────────────────
        $accesses = [
            // User
            ['id' =>  1, 'name' => 'lihat.user',            'display_name' => 'Lihat User',            'group' => 'User'],
            ['id' =>  2, 'name' => 'tambah.user',           'display_name' => 'Tambah User',           'group' => 'User'],
            ['id' =>  3, 'name' => 'edit.user',             'display_name' => 'Edit User',             'group' => 'User'],
            ['id' =>  4, 'name' => 'hapus.user',            'display_name' => 'Hapus User',            'group' => 'User'],
            // Role
            ['id' =>  5, 'name' => 'lihat.role',            'display_name' => 'Lihat Role',            'group' => 'Role'],
            ['id' =>  6, 'name' => 'tambah.role',           'display_name' => 'Tambah Role',           'group' => 'Role'],
            ['id' =>  7, 'name' => 'edit.role',             'display_name' => 'Edit Role',             'group' => 'Role'],
            ['id' =>  8, 'name' => 'hapus.role',            'display_name' => 'Hapus Role',            'group' => 'Role'],
            // User-Roles
            ['id' =>  9, 'name' => 'lihat.user-roles',      'display_name' => 'Lihat User Roles',      'group' => 'User Role'],
            ['id' => 10, 'name' => 'tambah.user-roles',     'display_name' => 'Tambah User Roles',     'group' => 'User Role'],
            ['id' => 11, 'name' => 'update.user-roles',     'display_name' => 'Update User Roles',     'group' => 'User Role'],
            ['id' => 12, 'name' => 'hapus.user-roles',      'display_name' => 'Hapus User Roles',      'group' => 'User Role'],
            // Role-Access
            ['id' => 13, 'name' => 'lihat.role-access',     'display_name' => 'Lihat Role Access',     'group' => 'Role Access'],
            ['id' => 14, 'name' => 'tambah.role-access',    'display_name' => 'Tambah Role Access',    'group' => 'Role Access'],
            ['id' => 15, 'name' => 'edit.role-access',      'display_name' => 'Edit Role Access',      'group' => 'Role Access'],
            ['id' => 16, 'name' => 'hapus.role-access',     'display_name' => 'Hapus Role Access',     'group' => 'Role Access'],
            // Fakultas
            ['id' => 17, 'name' => 'lihat.fakultas',        'display_name' => 'Lihat Fakultas',        'group' => 'Fakultas'],
            ['id' => 18, 'name' => 'tambah.fakultas',       'display_name' => 'Tambah Fakultas',       'group' => 'Fakultas'],
            ['id' => 19, 'name' => 'edit.fakultas',         'display_name' => 'Edit Fakultas',         'group' => 'Fakultas'],
            ['id' => 20, 'name' => 'hapus.fakultas',        'display_name' => 'Hapus Fakultas',        'group' => 'Fakultas'],
            // Jurusan
            ['id' => 21, 'name' => 'lihat.jurusan',         'display_name' => 'Lihat Jurusan',         'group' => 'Jurusan'],
            ['id' => 22, 'name' => 'tambah.jurusan',        'display_name' => 'Tambah Jurusan',        'group' => 'Jurusan'],
            ['id' => 23, 'name' => 'edit.jurusan',          'display_name' => 'Edit Jurusan',          'group' => 'Jurusan'],
            ['id' => 24, 'name' => 'hapus.jurusan',         'display_name' => 'Hapus Jurusan',         'group' => 'Jurusan'],
            // Periode Akademik
            ['id' => 25, 'name' => 'lihat.periode-akademik',  'display_name' => 'Lihat Periode Akademik',  'group' => 'Periode Akademik'],
            ['id' => 26, 'name' => 'tambah.periode-akademik', 'display_name' => 'Tambah Periode Akademik', 'group' => 'Periode Akademik'],
            ['id' => 27, 'name' => 'edit.periode-akademik',   'display_name' => 'Edit Periode Akademik',   'group' => 'Periode Akademik'],
            ['id' => 28, 'name' => 'hapus.periode-akademik',  'display_name' => 'Hapus Periode Akademik',  'group' => 'Periode Akademik'],
            // Mata Kuliah
            ['id' => 29, 'name' => 'lihat.matakuliah',      'display_name' => 'Lihat Mata Kuliah',     'group' => 'Mata Kuliah'],
            ['id' => 30, 'name' => 'tambah.matakuliah',     'display_name' => 'Tambah Mata Kuliah',    'group' => 'Mata Kuliah'],
            ['id' => 31, 'name' => 'edit.matakuliah',       'display_name' => 'Edit Mata Kuliah',      'group' => 'Mata Kuliah'],
            ['id' => 32, 'name' => 'hapus.matakuliah',      'display_name' => 'Hapus Mata Kuliah',     'group' => 'Mata Kuliah'],
            // Peserta
            ['id' => 33, 'name' => 'lihat.peserta',         'display_name' => 'Lihat Peserta',         'group' => 'Peserta'],
            ['id' => 34, 'name' => 'tambah.peserta',        'display_name' => 'Tambah Peserta',        'group' => 'Peserta'],
            ['id' => 35, 'name' => 'edit.peserta',          'display_name' => 'Edit Peserta',          'group' => 'Peserta'],
            ['id' => 36, 'name' => 'hapus.peserta',         'display_name' => 'Hapus Peserta',         'group' => 'Peserta'],
            // Instruktur
            ['id' => 37, 'name' => 'lihat.instruktur',      'display_name' => 'Lihat Instruktur',      'group' => 'Instruktur'],
            ['id' => 38, 'name' => 'tambah.instruktur',     'display_name' => 'Tambah Instruktur',     'group' => 'Instruktur'],
            ['id' => 39, 'name' => 'edit.instruktur',       'display_name' => 'Edit Instruktur',       'group' => 'Instruktur'],
            ['id' => 40, 'name' => 'hapus.instruktur',      'display_name' => 'Hapus Instruktur',      'group' => 'Instruktur'],
            // Kelas
            ['id' => 41, 'name' => 'lihat.kelas',           'display_name' => 'Lihat Kelas',           'group' => 'Kelas'],
            ['id' => 42, 'name' => 'tambah.kelas',          'display_name' => 'Tambah Kelas',          'group' => 'Kelas'],
            ['id' => 43, 'name' => 'edit.kelas',            'display_name' => 'Edit Kelas',            'group' => 'Kelas'],
            ['id' => 44, 'name' => 'hapus.kelas',           'display_name' => 'Hapus Kelas',           'group' => 'Kelas'],
            // Enrollment
            ['id' => 45, 'name' => 'lihat.enrollment',      'display_name' => 'Lihat Enrollment',      'group' => 'Enrollment'],
            ['id' => 46, 'name' => 'tambah.enrollment',     'display_name' => 'Tambah Enrollment',     'group' => 'Enrollment'],
            ['id' => 47, 'name' => 'edit.enrollment',       'display_name' => 'Edit Enrollment',       'group' => 'Enrollment'],
            ['id' => 48, 'name' => 'hapus.enrollment',      'display_name' => 'Hapus Enrollment',      'group' => 'Enrollment'],
            // Misc
            ['id' => 49, 'name' => 'reset-password.user',   'display_name' => 'Reset Password User',   'group' => 'User'],
            ['id' => 50, 'name' => 'lihat.pengaturan',      'display_name' => 'Lihat Pengaturan',      'group' => 'Pengaturan'],
            ['id' => 51, 'name' => 'edit.pengaturan',       'display_name' => 'Edit Pengaturan',       'group' => 'Pengaturan'],
        ];

        foreach ($accesses as $access) {
            DB::table('accesses')->updateOrInsert(['id' => $access['id']], $access);
        }

        // ── Role Access (admin = semua access) ─────────────────
        $adminAccessIds = array_column($accesses, 'id');
        foreach ($adminAccessIds as $accessId) {
            DB::table('role_accesses')->updateOrInsert(
                ['role_id' => 1, 'access_id' => $accessId]
            );
        }

        // ── Admin User ─────────────────────────────────────────
        $adminUser = DB::table('users')->updateOrInsert(
            ['email' => 'alwas.muis@umkendari.ac.id'],
            [
                'name'              => 'Alwas Muis',
                'email'             => 'alwas.muis@umkendari.ac.id',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );

        // Ambil id user admin lalu assign role admin
        $userId = DB::table('users')->where('email', 'alwas.muis@umkendari.ac.id')->value('id');
        DB::table('user_roles')->updateOrInsert(
            ['user_id' => $userId, 'role_id' => 1]
        );
    }
}
