<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
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
                'name' => 'name',
            ],
            [
                'name' => 'name',
            ],
            [
                'name' => 'name',
            ],
            [
                'name' => 'name',
            ],
            [
                'name' => 'name',
            ],
            [
                'name' => 'name',
            ],
            [
                'name' => 'name',
            ],
            [
                'name' => 'name',
            ],
            [
                'name' => 'name',
            ],
        ];

        foreach ($data as $key => $value) {
            Permission::create([
                'name' => $value['name'],
            ]);
        }
    }
}
