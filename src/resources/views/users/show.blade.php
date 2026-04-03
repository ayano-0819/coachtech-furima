@extends('layouts.app')

@section('title', 'マイページ')

@section('css')
<link rel="stylesheet" href="{{ asset('css/users/show.css') }}">
@endsection

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

@section('content')
<div class="mypage">

    <!-- プロフィール -->
    <div class="mypage__profile">
        <div class="mypage__profile-left">
            <div class="mypage__icon">
                @if($user->profile_image_path)
                    <img src="{{ asset('storage/' . $user->profile_image_path) }}" alt="{{ $user->name }}">
                @else
                    <div class="mypage__icon-placeholder"></div>
                @endif
            </div>

            <h2 class="mypage__name">
                {{ $user->name ?? 'ユーザー名' }}
            </h2>
        </div>

        <div class="mypage__profile-right">
            <a href="{{ route('mypage.profile.edit') }}" class="mypage__edit-btn">
                プロフィールを編集
            </a>
        </div>
    </div>

    <!-- タブ -->
    <div class="mypage__tabs">
        <a
            href="{{ route('mypage') }}?page=sell"
            class="mypage__tab {{ request('page', 'sell') === 'sell' ? 'mypage__tab--active' : '' }}"
        >
            出品した商品
        </a>

        <a
            href="{{ route('mypage') }}?page=buy"
            class="mypage__tab {{ request('page') === 'buy' ? 'mypage__tab--active' : '' }}"
        >
            購入した商品
        </a>
    </div>

    <!-- 商品一覧 -->
    <div class="mypage__items">
        @if(request('page', 'sell') === 'sell')
            <div class="mypage__item-list">
                @forelse($sellItems as $item)
                    <a href="{{ route('items.show', ['item_id' => $item->id]) }}" class="mypage__item">
                        <div class="mypage__item-image">
                            @if($item->image_path)
                                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                            @endif
                        </div>
                        <p class="mypage__item-name">{{ $item->name }}</p>
                    </a>
                @empty
                    <p class="mypage__empty-message">出品した商品はありません</p>
                @endforelse
            </div>
        @else
            <div class="mypage__item-list">
                @forelse($buyItems as $order)
                    <a href="{{ route('items.show', ['item_id' => $order->item->id]) }}" class="mypage__item">
                        <div class="mypage__item-image">
                            @if($order->item->image_path)
                                <img src="{{ asset('storage/' . $order->item->image_path) }}" alt="{{ $order->item->name }}">
                            @endif
                        </div>
                        <p class="mypage__item-name">{{ $order->item->name ?? '商品名なし' }}</p>
                    </a>
                @empty
                    <p class="mypage__empty-message">購入した商品はありません</p>
                @endforelse
            </div>
        @endif
    </div>

</div>
@endsection