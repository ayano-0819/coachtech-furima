<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        // 未認証ならメール認証誘導画面へ
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // 認証済みならトップ画面へ
        redirect()->intended(route('items.index'));
    }
}