<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::create([
            'name' => 'Owner Utama', 'username' => 'owner', 'email' => 'owner@simb.test',
            'phone' => '6281200000001', 'password' => Hash::make('owner123'), 'role' => 'owner',
        ]);

        User::create([
            'name' => 'Admin Gudang', 'username' => 'admin', 'email' => 'admin@simb.test',
            'phone' => '6281200000002', 'password' => Hash::make('admin123'), 'role' => 'admin',
            'created_by' => $owner->id,
        ]);
    }
}
