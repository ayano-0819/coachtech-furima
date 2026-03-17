@extends('layouts.app')

@section('title','会員登録')

@section('header-nav')
@endsection


@section('content')

<h1>会員登録</h1>

<form method="POST" action="{{ route('register') }}" novalidate>
    @csrf

    <div>
        <label>ユーザー名</label>
        <input type="text" name="name" value="{{ old('name') }}">
        @error('name')
            <div>{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label>メールアドレス</label>
        <input type="email" name="email" value="{{ old('email') }}">
        @error('email')
            <div>{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label>パスワード</label>
        <input type="password" name="password">
        @error('password')
            <div>{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label>確認用パスワード</label>
        <input type="password" name="password_confirmation">
    </div>

    <button type="submit">登録する</button>

</form>

<p>
    <a href="/login">ログインはこちら</a>
</p>

@endsection