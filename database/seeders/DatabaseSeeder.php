<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ProductsSeeder::class,
            AdminSeeder::class,
        ]);

        $GOVERNORATE = \App\Models\Location::create([
            'name' => 'Gaza',
            'type' => 'GOVERNORATE',
        ]);

        $REGION = \App\Models\Location::create([
            'name' => 'Remal',
            'type' => 'REGION',
            'parent_id' => $GOVERNORATE->id,
        ]);

        $Reason = \App\Models\Reason::create([
            'name' => 'Remal',
            'type' => 'CUSTOMER',
            'context' => 'REJECTION',
            'status' => 'ACTIVE',
        ]);

        $Reason = \App\Models\Reason::create([
            'name' => 'Remal',
            'type' => 'VENDOR',
            'context' => 'CANCELATION',
            'status' => 'ACTIVE',
        ]);

        for($i = 0 ; $i <= 10 ; $i++){
        $user = \App\Models\User::create([
                'name' => 'Test User',
                'type' => 'VENDOR',
                'phone' => '52314679'.$i,
                'otp' => '1234',
                'email' => 'test@example.com'.$i,
                'password' => 'test@example.com'.$i,
            ]);
            $vendor = new Vendor();
            $vendor->name = 'Test User';
            $vendor->commercial_name = 'commercial_name';
            $vendor->phone = '52314679'.$i;
            $vendor->user_id  = $user->id;
            $vendor->governorate_id = 1;
            $vendor->region_id  = 2;
            $vendor->save();
        }

    }
}
