@extends('layouts.app')

@section('title', '商品一覧')

@section('header-nav')
    <div class="header__center">
        <form action="/" method="GET" class="header__search-form">
            <input
                type="text"
                name="keyword"
                value="{{ request('keyword') }}"
                placeholder="なにをお探しですか？"
                class="header__search-input"
            >
        </form>
    </div>

    <div class="header__right">
        @guest
            <a href="/login" class="header__link">ログイン</a>
        @endguest

        @auth
            <form method="POST" action="/logout" class="header__logout-form">
                @csrf
                <button type="submit" class="header__link header__logout-button">ログアウト</button>
            </form>
        @endauth

        <a href="{{ route('mypage') }}" class="header__link">マイページ</a>
        <a href="{{ route('items.create') }}" class="header__sell-button">出品</a>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/index.css') }}">
@endsection

@section('content')
    <div class="item-index">
        <div class="item-index__tabs">
            <a
                href="/?keyword={{ request('keyword') }}"
                class="item-index__tab {{ $tab !== 'mylist' ? 'item-index__tab--active' : '' }}"
            >
                おすすめ
            </a>

            <a
                href="/?tab=mylist&keyword={{ request('keyword') }}"
                class="item-index__tab {{ $tab === 'mylist' ? 'item-index__tab--active' : '' }}"
            >
                マイリスト
            </a>
        </div>

        <div class="item-index__list">
            @foreach($items as $item)
                <div class="item-index__card">
                    <a href="{{ route('items.show', ['item_id' => $item->id]) }}" class="item-index__link">
                        <div class="item-index__image-wrap">
                            <img
                                src="{{ asset('storage/' . $item->image_path) }}"
                                alt="{{ $item->name }}"
                                class="item-index__image"
                            >

                            @if ($item->is_sold)
                                <span class="item-index__sold">Sold</span>
                            @endif
                        </div>

                        <p class="item-index__name">{{ $item->name }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection