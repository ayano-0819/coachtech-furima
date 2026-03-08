@extends('layouts.app')

@section('title', '商品一覧')

@section('header-nav')
    <form action="/" method="GET">
        <input
            type="text"
            name="keyword"
            value="{{ request('keyword') }}"
            placeholder="何をお探しですか？"
        >
    </form>

    @guest
        <a href="/login">ログイン</a>
    @endguest

    @auth
        <form method="POST" action="/logout" style="display:inline;">
            @csrf
            <button type="submit">ログアウト</button>
        </form>
    @endauth

    <a href="/mypage">マイページ</a>
    <a href="/sell">出品</a>
@endsection

@section('content')

    <div class="tabs">
        <a href="/" class="{{ request('tab') !== 'mylist' ? 'active' : '' }}">
            おすすめ
        </a>
        <a href="/?tab=mylist" class="{{ request('tab') === 'mylist' ? 'active' : '' }}">
            マイリスト
        </a>
    </div>

    <div class="item-list">
        @foreach($items as $item)
            <div class="item-card">
                <a href="/item/{{ $item->id }}">
                    <div class="item-image">
                        商品画像
                    </div>

                    <p class="item-name">
                        {{ $item->name }}
                    </p>
                </a>
            </div>
        @endforeach
    </div>

@endsection