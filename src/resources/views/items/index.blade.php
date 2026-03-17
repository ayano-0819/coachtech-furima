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
        <a href="/?keyword={{ request('keyword') }}" class="{{ $tab !== 'mylist' ? 'active' : '' }}">
            おすすめ
        </a>
        <a href="/?tab=mylist&keyword={{ request('keyword') }}" class="{{ $tab === 'mylist' ? 'active' : '' }}">
            マイリスト
        </a>
    </div>

    <div class="item-list">
        @foreach($items as $item)
            <div class="item-card">
                <a href="{{ route('items.show', ['item_id' => $item->id]) }}">
                    <div class="item-image">
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                    </div>

                    <p class="item-name">
                        {{ $item->name }}
                    </p>
                </a>
            </div>
        @endforeach
    </div>

@endsection