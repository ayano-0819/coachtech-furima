@extends('layouts.app')

@section('title', '住所の変更')

@section('header-nav')
    <form action="/" method="GET">
        <input
            type="text"
            name="keyword"
            value="{{ request('keyword') }}"
            placeholder="何をお探しですか？"
        >
    </form>

    @auth
        <form method="POST" action="/logout" style="display:inline;">
            @csrf
            <button type="submit">ログアウト</button>
        </form>
    @endauth

    <a href="{{ route('mypage') }}">マイページ</a>
    <a href="{{ route('items.create') }}">出品</a>
@endsection

@section('content')
<div class="address-edit">
    <h1>住所の変更</h1>

    <form action="{{ route('purchase.address.update', ['item_id' => $item_id]) }}" method="POST">
        @csrf

        <div>
            <label>郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', session('postal_code', $user->postal_code)) }}">
        </div>

        <div>
            <label>住所</label>
            <input type="text" name="address" value="{{ old('address', session('address', $user->address)) }}">
        </div>

        <div>
            <label>建物名</label>
            <input type="text" name="building" value="{{ old('building', session('building', $user->building)) }}">
        </div>

        <button type="submit">更新する</button>
    </form>
</div>
@endsection