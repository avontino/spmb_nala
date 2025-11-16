<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nik' => '1234567890123456',
            'nisn' => '1234567890',
            'name' => 'Admin PPDB',
            'email' => 'admin@ppdb.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'nik' => '1234567890123457',
            'nisn' => '1234567891',
            'name' => 'Verifikator PPDB',
            'email' => 'verifikator@ppdb.test',
            'password' => Hash::make('password'),
            'role' => 'verifikator',
            'email_verified_at' => now(),
        ]);

        User::create([
            'nik' => '3578012345678901',
            'nisn' => '0012345678',
            'name' => 'Ahmad Pendaftar',
            'email' => 'pendaftar@test.com',
            'password' => Hash::make('password'),
            'role' => 'pendaftar',
            'email_verified_at' => now(),
        ]);
    }
}