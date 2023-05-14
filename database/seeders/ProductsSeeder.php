<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
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
                'name' => 'جره 12 كيلو',
                'price' => '70',
                'size' => '12',
                'image' => 'image/image-2.png',
                'status' => 'ACTIVE',
                'type' => 'GAS',
            ],
            [
                'name' => 'جره 60 كيلو',
                'price' => '700',
                'size' => '12',
                'image' => 'image/image-1.png',
                'status' => 'ACTIVE',
                'type' => 'GAS',
            ],
            [
                'name' => 'جره 12 كيلو',
                'price' => '70',
                'size' => '12',
                'image' => 'image/image-3.png',
                'status' => 'ACTIVE',
                'type' => 'GAS',
            ],
            [
                'name' => 'جره 45 كيلو',
                'price' => '400',
                'size' => '12',
                'image' => 'image/image-4.png',
                'status' => 'ACTIVE',
                'type' => 'GAS',
            ],
            [
                'name' => 'جره 12 كيلو',
                'price' => '70',
                'size' => '12',
                'image' => 'image/image-2.png',
                'status' => 'ACTIVE',
                'type' => 'WATER',
            ],
            [
                'name' => 'جره 60 كيلو',
                'price' => '700',
                'size' => '12',
                'image' => 'image/image-1.png',
                'status' => 'ACTIVE',
                'type' => 'WATER',
            ],
            [
                'name' => 'جره 12 كيلو',
                'price' => '70',
                'size' => '12',
                'image' => 'image/image-3.png',
                'status' => 'ACTIVE',
                'type' => 'WATER',
            ],
            [
                'name' => 'جره 45 كيلو',
                'price' => '400',
                'size' => '12',
                'image' => 'image/image-4.png',
                'status' => 'ACTIVE',
                'type' => 'WATER',
            ],
        ];

        foreach ($data as $key => $value) {
            Product::create([
                'name' => $value['name'],
                'price' => $value['price'],
                'size' => $value['size'],
                'image' => $value['image'],
                'status' => $value['status'],
                'type' => $value['type'],
            ]);
        }
    }
}
