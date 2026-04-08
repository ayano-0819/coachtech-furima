<?php

namespace Tests\Feature;

use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_selected_payment_method_is_reflected_in_purchase_summary()
    {
        $user = User::factory()->create([
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストマンション101',
        ]);

        $seller = User::factory()->create();

        $condition = Condition::create([
            'name' => '良好',
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => '支払い方法確認用商品',
            'brand_name' => 'テストブランド',
            'description' => '支払い方法確認用です。',
            'price' => 3000,
            'image_path' => 'items/test.jpg',
            'is_sold' => false,
        ]);

        $response = $this->actingAs($user)->get(
            route('purchase.create', [
                'item_id' => $item->id,
                'payment_method' => 'card',
            ])
        );

        $response->assertStatus(200);

        $response->assertSee('<option value="card" selected>', false);
        $response->assertDontSee('<option value="convenience" selected>', false);
        $response->assertSee('支払い方法');
        $response->assertSee('カード支払い');
        $response->assertDontSee('未選択');
    }
}
