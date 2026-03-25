@extends('layouts.app')

@section('title', 'マイページ')

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
<div class="mypage">

    <!-- プロフィール -->
    <div class="mypage__profile">
        <div class="mypage__profile-left">
            <div class="mypage__icon">
                @if($user->profile_image_path)
                    <img src="{{ asset('storage/' . $user->profile_image_path) }}" alt="{{ $user->name }}">
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
        <a href="{{ route('mypage') }}?page=sell"
            class="mypage__tab {{ request('page', 'sell') === 'sell' ? 'active' : '' }}">
            出品した商品
        </a>

        <a href="{{ route('mypage') }}?page=buy"
            class="mypage__tab {{ request('page') === 'buy' ? 'active' : '' }}">
            購入した商品
        </a>
    </div>

    <!-- 商品一覧 -->
    <div class="mypage__items">

        @if(request('page', 'sell') === 'sell')
            {{-- 出品した商品 --}}
            <div class="mypage__item-list">
                @forelse($sellItems as $item)
                    <a href="{{ route('items.show', ['item_id' => $item->id]) }}" class="mypage__item">
                        <div class="mypage__item-image">
                            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                        </div>
                        <p class="mypage__item-name">{{ $item->name }}</p>
                    </a>
                @empty
                    <p>出品した商品はありません</p>
                @endforelse
            </div>
        @else
            {{-- 購入した商品 --}}
            <div class="mypage__item-list">
                @forelse($buyItems as $order)
                    <div class="mypage__item">
                        <div class="mypage__item-image">
                            <img src="{{ asset('storage/' . $order->item->image_path) }}" alt="{{ $order->item->name }}">
                        </div>
                        <p class="mypage__item-name">{{ $order->item->name ?? '商品名なし' }}</p>
                    </div>
                @empty
                    <p>購入した商品はありません</p>
                @endforelse
            </div>
        @endif

    </div>

</div>
@endsection