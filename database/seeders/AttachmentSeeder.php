<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttachmentSeeder extends Seeder
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
                "type" => "ALL",
                "name" => "الهوية",
                "is_required" => 1,
                "file" => "IMAGE",
                "status" => "ACTIVE",
                "slug" => "A",
                "validity" => 0,
            ],

            [
                "type" => "GAS",
                "name" => "الهوية1",
                "is_required" => 1,
                "file" => "IMAGE",
                "status" => "ACTIVE",
                "slug" => "B",
                "validity" => 0,
            ],

            [
                "type" => "WATER",
                "name" => "الهوية1",
                "is_required" => 1,
                "file" => "IMAGE",
                "status" => "ACTIVE",
                "slug" => "C",
                "validity" => 0,
            ]
        ];

        foreach ($data as $key => $value) {
            Document::create([
                "type" => $value['type'],
                "name" => $value['name'],
                "is_required" => $value['is_required'],
                "file" => $value['file'],
                "status" => $value['status'],
                "slug" => $value['slug'],
                "validity" => $value['validity'],
            ]);
        }
    }
}
