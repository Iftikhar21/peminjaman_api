<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil role admin yang sudah dibuat oleh RoleSeeder
        $role = Role::where('role_name', 'admin')->first();

        if (! $role) {
            dd("Role 'admin' belum ada, jalankan RoleSeeder dulu!");
        }

        // Buat user admin
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
        ]);

        // Masukkan data ke tabel admins
        Admin::create([
            'user_id' => $user->id,
            'phone' => '08123456789',
        ]);
    }
}