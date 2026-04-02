<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザー情報変更画面で各項目の初期値が表示される
     */
    public function test_profile_edit_page_displays_initial_values()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストマンション101',
            'profile_image_path' => 'profiles/test-user.jpg',
        ]);

        $response = $this->actingAs($user)->get(route('mypage.profile.edit'));

        $response->assertStatus(200);

        $response->assertSee('テストユーザー');
        $response->assertSee('123-4567');
        $response->assertSee('東京都渋谷区1-1-1');
        $response->assertSee('テストマンション101');
        $response->assertSee('storage/profiles/test-user.jpg');
    }

    /**
    * ユーザー情報を更新できる
    */
    public function test_user_can_update_profile()
    {
        $user = User::factory()->create([
            'name' => '旧ユーザー',
            'postal_code' => '111-1111',
            'address' => '旧住所',
            'building' => '旧建物',
        ]);

        $response = $this->actingAs($user)->post(route('mypage.profile.update'), [
            'name' => '新ユーザー',
            'postal_code' => '222-2222',
            'address' => '新住所',
            'building' => '新建物',
        ]);

        $response->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => '新ユーザー',
            'postal_code' => '222-2222',
            'address' => '新住所',
            'building' => '新建物',
        ]);
    }
}