<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログイン済みユーザーはコメントを送信できる
     */
    public function test_authenticated_user_can_post_comment()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $condition = Condition::create([
            'name' => '良好',
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => '説明',
            'price' => 1000,
            'image_path' => 'items/test.jpg',
            'is_sold' => false,
        ]);

        $response = $this->actingAs($user)->post(route('comments.store', ['item_id' => $item->id]), [
            'content' => 'テストコメントです',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'テストコメントです',
        ]);

        $detailResponse = $this->actingAs($user)->get("/item/{$item->id}");
        $detailResponse->assertSee('コメント(1)');
        $detailResponse->assertSee('テストコメントです');
    }

    /**
     * 未ログインユーザーはコメントを送信できない
     */
    public function test_guest_cannot_post_comment()
    {
        $seller = User::factory()->create();

        $condition = Condition::create([
            'name' => '良好',
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => '説明',
            'price' => 1000,
            'image_path' => 'items/test.jpg',
            'is_sold' => false,
        ]);

        $response = $this->post(route('comments.store', ['item_id' => $item->id]), [
            'content' => 'テストコメントです',
        ]);

        // ログインページへリダイレクト
        $response->assertRedirect('/login');

        $this->assertDatabaseCount('comments', 0);
    }

    /**
     * コメント未入力の場合、バリデーションエラー
     */
    public function test_comment_validation_fails_when_empty()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $condition = Condition::create([
            'name' => '良好',
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => '説明',
            'price' => 1000,
            'image_path' => 'items/test.jpg',
            'is_sold' => false,
        ]);

        $response = $this->actingAs($user)->post(route('comments.store', ['item_id' => $item->id]), [
            'content' => '',
        ]);

        $response->assertSessionHasErrors('content');
    }

    /**
     * コメントが255文字以上の場合、バリデーションエラー
     */
    public function test_comment_validation_fails_when_too_long()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $condition = Condition::create([
            'name' => '良好',
        ]);

        $item = Item::create([
            'user_id' => $seller->id,
            'condition_id' => $condition->id,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => '説明',
            'price' => 1000,
            'image_path' => 'items/test.jpg',
            'is_sold' => false,
        ]);

        $longText = str_repeat('あ', 256);

        $response = $this->actingAs($user)->post(route('comments.store', ['item_id' => $item->id]), [
            'content' => $longText,
        ]);

        $response->assertSessionHasErrors('content');
    }
}