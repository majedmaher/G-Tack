<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'هاني فرينة',
                'commercial_name' => 'شركة فرينة لتعبئة الغاز',
                'type' => 'VENDOR',
                'type_vendor' => 'GAS',
                'phone' => '09781956465',
                'otp' => '1234',
                'email' => 'tes1t@example.com',
                'password' => 'tes1t@example.com',
            ],
            [
                'name' => 'ناريمان زقوت',
                'commercial_name' => 'شركة زقوت لتعبئة الغاز',
                'type' => 'VENDOR',
                'type_vendor' => 'GAS',
                'phone' => '059456987',
                'otp' => '1234',
                'email' => 'nareman@example.com',
                'password' => 'nareman@example.com',
            ],
            [
                'name' => 'منى محمد',
                'commercial_name' => 'تروبة رفح لتعبة الغاز',
                'type' => 'VENDOR',
                'type_vendor' => 'GAS',
                'phone' => '0564568779',
                'otp' => '1234',
                'email' => 'mona@example.com',
                'password' => 'mona@example.com',
            ],
            [
                'name' => 'مصعب غراب',
                'commercial_name' => 'شركة غراب لتعبئة المياه',
                'type' => 'VENDOR',
                'type_vendor' => 'WATER',
                'phone' => '059132456',
                'otp' => '1234',
                'email' => 'mosad@example.com',
                'password' => 'mosad@example.com',
            ],
            [
                'name' => 'شوق الحلو',
                'commercial_name' => 'شركة الحلو لتعبئة المياه',
                'type' => 'VENDOR',
                'type_vendor' => 'WATER',
                'phone' => '0978956465',
                'otp' => '1234',
                'email' => 'test@example.com',
                'password' => 'test@example.com',
            ],
        ];

        foreach ($data as $key => $value) {
            $user = User::create([
                'name' => $value['name'],
                'type' => $value['type'],
                'phone' => $value['phone'],
                'otp' => $value['otp'],
                'email' => $value['email'],
                'password' => $value['password'],
            ]);
            Vendor::create([
                'name' => $value['name'],
                'commercial_name' => $value['commercial_name'],
                'type' => $value['type_vendor'],
                'phone' => $value['phone'],
                'user_id' => $user->id,
                'governorate_id' => 1,
                'region_id' => 2,
            ]);
        }
    }
}
