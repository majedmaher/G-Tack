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
                'name' => 'overView_view',
            ],
            [
                'name' => 'overView_add',
            ],
            [
                'name' => 'overView_edit',
            ],
            [
                'name' => 'overView_delete',
            ],


            [
                'name' => 'order_view',
            ],
            [
                'name' => 'order_add',
            ],
            [
                'name' => 'order_edit',
            ],
            [
                'name' => 'order_delete',
            ],

            [
                'name' => 'vendor_view',
            ],
            [
                'name' => 'vendor_add',
            ],
            [
                'name' => 'vendor_edit',
            ],
            [
                'name' => 'vendor_delete',
            ],

            [
                'name' => 'customer_view',
            ],
            [
                'name' => 'customer_add',
            ],
            [
                'name' => 'customer_edit',
            ],
            [
                'name' => 'customer_delete',
            ],

            [
                'name' => 'map_view',
            ],
            [
                'name' => 'map_add',
            ],
            [
                'name' => 'map_edit',
            ],
            [
                'name' => 'map_delete',
            ],

            [
                'name' => 'reports_view',
            ],
            [
                'name' => 'reports_add',
            ],
            [
                'name' => 'reports_edit',
            ],
            [
                'name' => 'reports_delete',
            ],

            [
                'name' => 'user_view',
            ],
            [
                'name' => 'user_add',
            ],
            [
                'name' => 'user_edit',
            ],
            [
                'name' => 'user_delete',
            ],

            [
                'name' => 'complaint_view',
            ],
            [
                'name' => 'complaint_add',
            ],
            [
                'name' => 'complaint_edit',
            ],
            [
                'name' => 'complaint_delete',
            ],

            [
                'name' => 'settings_view',
            ],
            [
                'name' => 'settings_add',
            ],
            [
                'name' => 'settings_edit',
            ],
            [
                'name' => 'settings_delete',
            ],
        ];

        foreach ($data as $key => $value) {
            Permission::create([
                'name' => $value['name'],
            ]);
        }
    }
}
