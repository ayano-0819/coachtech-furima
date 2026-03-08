@extends('layouts.app')

@section('title','ログイン')

@section('header-nav')
@endsection


@section('content')

<h1>ログイン</h1>

<form method="POST" action="{{ route('login') }}" novalidate>
    @csrf

    <div>
        <label>メールアドレス</label>
        <input type="text" name="email" value="{{ old('email') }}">
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

    <button type="submit">ログインする</button>

</form>

<p>
    <a href="/register">会員登録はこちら</a>
</p>

@endsection