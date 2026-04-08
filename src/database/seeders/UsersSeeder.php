<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'testuser1',
            'email' => 'test1@example.com',
            'password' => Hash::make('password'),
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => '渋谷マンション101',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'testuser2',
            'email' => 'test2@example.com',
            'password' => Hash::make('password'),
            'postal_code' => '234-5678',
            'address' => '東京都新宿区2-2-2',
            'building' => null, // ←ここがポイント
            'email_verified_at' => now(),
        ]);
        
        User::create([
            'name' => 'testuser3',
            'email' => 'test3@example.com',
            'password' => Hash::make('password'),
            'postal_code' => '345-6789',
            'address' => '東京都港区3-3-3',
            'building' => '港タワー303',
            'email_verified_at' => now(),
        ]);
    }
}
