<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'type' => 'ADMIN',
            'name' => 'Admin',
            'email' => 'admin@admin.net',
            'phone' => '0594148741',
            'otp' => '1234',
            'password' => '132456789',
            'status' => 'ACTIVE',
        ]);
    }
}
