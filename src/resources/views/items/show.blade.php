@extends('layouts.app')

@section('title', '商品詳細')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
@endsection

@section('header-nav')
    <div class="header__center">
        <form action="{{ route('items.index') }}" method="GET" class="header__search-form">
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
            <a href="{{ route('login') }}" class="header__link">ログイン</a>
        @endguest

        @auth
            <form method="POST" action="{{ route('logout') }}" class="header__logout-form">
                @csrf
                <button type="submit" class="header__link header__logout-button">ログアウト</button>
            </form>
        @endauth

        <a href="{{ route('mypage') }}" class="header__link">マイページ</a>
        <a href="{{ route('items.create') }}" class="header__sell-button">出品</a>
    </div>
@endsection

@section('content')
<div class="item-detail">
    <div class="item-detail__inner">
        <div class="item-detail__image-area">
            <div class="item-detail__image-box">
                <img
                    src="{{ asset('storage/' . $item->image_path) }}"
                    alt="{{ $item->name }}"
                    class="item-detail__image"
                >

                @if($item->is_sold)
                    <span class="item-detail__sold">Sold</span>
                @endif
            </div>
        </div>

        <div class="item-detail__content">
            <h1 class="item-detail__name">{{ $item->name }}</h1>

            @if($item->brand_name)
                <p class="item-detail__brand">{{ $item->brand_name }}</p>
            @endif

            <p class="item-detail__price">¥{{ number_format($item->price) }} <span class="item-detail__price-tax">（税込）</span></p>

            <div class="item-detail__counts">
                <div class="item-detail__count-group">
                    @auth
                        @if(auth()->id() !== $item->user_id)
                            @if($isLiked)
                                <form action="{{ route('likes.destroy', ['item_id' => $item->id]) }}" method="POST" class="item-detail__icon-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="item-detail__icon-button">
                                        <img src="{{ asset('images/heart-liked.png') }}" alt="いいね解除" class="item-detail__icon-image">
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('likes.store', ['item_id' => $item->id]) }}" method="POST" class="item-detail__icon-form">
                                    @csrf
                                    <button type="submit" class="item-detail__icon-button">
                                        <img src="{{ asset('images/heart-default.png') }}" alt="いいね" class="item-detail__icon-image">
                                    </button>
                                </form>
                            @endif
                        @else
                            <img src="{{ asset('images/heart-default.png') }}" alt="いいね" class="item-detail__icon-image">
                        @endif
                    @endauth

                    @guest
                        <img src="{{ asset('images/heart-default.png') }}" alt="いいね" class="item-detail__icon-image">
                    @endguest

                    <span class="item-detail__count-number">{{ $likeCount }}</span>
                </div>

                <div class="item-detail__count-group">
                    <img src="{{ asset('images/comment.png') }}" alt="コメント" class="item-detail__icon-image">
                    <span class="item-detail__count-number">{{ $commentCount }}</span>
                </div>
            </div>

            @auth
                @if(auth()->id() !== $item->user_id && !$item->is_sold)
                    <a href="{{ route('purchase.create', ['item_id' => $item->id]) }}" class="item-detail__purchase-btn">
                        購入手続きへ
                    </a>
                @else
                    <button class="item-detail__purchase-btn item-detail__purchase-btn--disabled" disabled>
                        購入手続きへ
                    </button>
                @endif
            @else
                <a href="{{ route('login') }}" class="item-detail__purchase-btn">
                    購入手続きへ
                </a>
            @endauth

            <section class="item-detail__section">
                <h2 class="item-detail__heading">商品説明</h2>
                <p class="item-detail__description">{{ $item->description }}</p>
            </section>

            <section class="item-detail__section">
                <h2 class="item-detail__heading">商品の情報</h2>

                <div class="item-detail__meta-row">
                    <span class="item-detail__meta-label">カテゴリー</span>
                    <div class="item-detail__category-list">
                        @foreach($item->categories as $category)
                            <span class="item-detail__category-tag">{{ $category->name }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="item-detail__meta-row">
                    <span class="item-detail__meta-label">商品の状態</span>
                    <span class="item-detail__meta-value">{{ $item->condition->name }}</span>
                </div>
            </section>

            <section class="item-detail__section">
                <h2 class="item-detail__heading item-detail__heading--comment">コメント({{ $commentCount }})</h2>

                @foreach($item->comments as $comment)
                    <div class="item-detail__comment">
                        <div class="item-detail__comment-user">
                            <div class="item-detail__comment-icon">
                                @if($comment->user->profile_image_path)
                                    <img
                                        src="{{ asset('storage/' . $comment->user->profile_image_path) }}"
                                        alt="{{ $comment->user->name }}"
                                        class="item-detail__comment-avatar"
                                    >
                                @else
                                    <div class="item-detail__comment-avatar-placeholder"></div>
                                @endif
                            </div>

                            <p class="item-detail__comment-user-name">{{ $comment->user->name }}</p>
                        </div>

                        <p class="item-detail__comment-text">{{ $comment->content }}</p>
                    </div>
                @endforeach
            </section>

            <section class="item-detail__section">
                <h2 class="item-detail__heading">商品へのコメント</h2>

                @auth
                    <form action="{{ route('comments.store', ['item_id' => $item->id]) }}" method="POST" class="item-detail__comment-form">
                        @csrf

                        <textarea name="content" class="item-detail__textarea">{{ old('content') }}</textarea>

                        @error('content')
                            <p class="item-detail__error">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="item-detail__submit-btn">コメントを送信する</button>
                    </form>
                @else
                    <form action="{{ route('login') }}" method="GET" class="item-detail__comment-form">
                        <textarea name="content" class="item-detail__textarea"></textarea>
                        <button type="submit" class="item-detail__submit-btn">コメントを送信する</button>
                    </form>
                @endauth
            </section>
        </div>
    </div>
</div>
@endsection