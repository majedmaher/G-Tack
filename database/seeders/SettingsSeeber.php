<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeber extends Seeder
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
                'key' => 'privacy',
                'label' => 'سياسات وخصوصية',
                'value' => 'سياسات وخصوصية',
                'group' => 'social',
            ],
            [
                'key' => 'whats-app',
                'label' => 'واتس اب',
                'value' => '+969858747123',
                'group' => 'social',
            ],
            [
                'key' => 'gas-activation',
                'label' => 'تفعيل تطبيق الغاز ',
                'value' => 'سياسات وخصوصية',
                'group' => 'social',
            ],
            [
                'key' => 'water-activation',
                'label' => 'تفعيل تطبيق الماء',
                'value' => '+969858747123',
                'group' => 'social',
            ],
        ];

        foreach ($data as $key => $value) {
            Setting::create([
                'key' => $value['key'],
                'label' => $value['label'],
                'value' => $value['value'],
                'group' => $value['group'],
            ]);
        }
    }
}
