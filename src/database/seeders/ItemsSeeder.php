<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use App\Models\Condition;
use Illuminate\Database\Seeder;

class ItemsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('Usersが存在しないため、ItemsSeederをスキップしました。');
            return;
        }

        $conditionIds = [
            '良好' => Condition::where('name', '良好')->value('id'),
            '目立った傷や汚れなし' => Condition::where('name', '目立った傷や汚れなし')->value('id'),
            'やや傷や汚れあり' => Condition::where('name', 'やや傷や汚れあり')->value('id'),
            '状態が悪い' => Condition::where('name', '状態が悪い')->value('id'),
        ];

        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'brand_name' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_path' => 'items/watch.jpg',
                'condition_id' => $conditionIds['良好'],
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'brand_name' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'image_path' => 'items/hdd.jpg',
                'condition_id' => $conditionIds['目立った傷や汚れなし'],
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand_name' => null,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_path' => 'items/onion.jpg',
                'condition_id' => $conditionIds['やや傷や汚れあり'],
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'brand_name' => null,
                'description' => 'クラシックなデザインの革靴',
                'image_path' => 'items/shoes.jpg',
                'condition_id' => $conditionIds['状態が悪い'],
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'brand_name' => null,
                'description' => '高性能なノートパソコン',
                'image_path' => 'items/laptop.jpg',
                'condition_id' => $conditionIds['良好'],
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'brand_name' => null,
                'description' => '高音質のレコーディング用マイク',
                'image_path' => 'items/mic.jpg',
                'condition_id' => $conditionIds['目立った傷や汚れなし'],
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand_name' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'image_path' => 'items/bag.jpg',
                'condition_id' => $conditionIds['やや傷や汚れあり'],
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'brand_name' => null,
                'description' => '使いやすいタンブラー',
                'image_path' => 'items/tumbler.jpg',
                'condition_id' => $conditionIds['状態が悪い'],
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand_name' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'image_path' => 'items/coffee_mill.jpg',
                'condition_id' => $conditionIds['良好'],
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'brand_name' => null,
                'description' => '便利なメイクアップセット',
                'image_path' => 'items/makeup_set.jpg',
                'condition_id' => $conditionIds['目立った傷や汚れなし'],
            ],
        ];

        foreach ($items as $index => $item) {
            Item::create([
                'user_id' => $users[$index % $users->count()]->id,
                'condition_id' => $item['condition_id'],
                'name' => $item['name'],
                'brand_name' => $item['brand_name'],
                'description' => $item['description'],
                'price' => $item['price'],
                'image_path' => $item['image_path'],
                'is_sold' => false,
            ]);
        }
    }
}