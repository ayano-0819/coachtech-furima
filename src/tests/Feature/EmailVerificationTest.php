<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 会員登録後、認証メールが送信される
     */
    public function test_verification_email_is_sent_after_registration()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(302);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($user);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /**
     * メール認証誘導画面に「認証はこちらから」ボタンが表示される
     */
    public function test_verification_notice_page_displays_verification_button()
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertStatus(200);
        $response->assertSee('認証はこちらから');
    }

    /**
     * メール認証完了後、プロフィール設定画面に遷移する
     */
    public function test_verified_user_is_redirected_to_profile_edit_page()
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect(route('mypage.profile.edit'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    /**
     * 未認証ユーザーが認証必須ページへアクセスすると認証誘導画面へ遷移する
     * （要件：メール認証を行わずにログインを試みた場合の挙動）
     */
    public function test_unverified_user_is_redirected_to_verification_notice_page()
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/mypage');

        $response->assertRedirect('/email/verify');
    }
}