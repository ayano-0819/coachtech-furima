@extends('layouts.app')

@section('title', 'ログイン')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('header-nav')
@endsection

@section('content')
<div class="login">
    <div class="login__inner">
        <h1 class="login__title">ログイン</h1>

        <form method="POST" action="{{ route('login') }}" novalidate class="login__form">
            @csrf

            <div class="login__group">
                <label for="email" class="login__label">メールアドレス</label>
                <input
                    id="email"
                    type="text"
                    name="email"
                    value="{{ old('email') }}"
                    class="login__input"
                >
                @error('email')
                    <p class="login__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="login__group">
                <label for="password" class="login__label">パスワード</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="login__input"
                >
                @error('password')
                    <p class="login__error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="login__button">ログインする</button>
        </form>

        <p class="login__register">
            <a href="/register" class="login__register-link">会員登録はこちら</a>
        </p>
    </div>
</div>
@endsection