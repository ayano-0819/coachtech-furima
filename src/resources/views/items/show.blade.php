@extends('layouts.app')

@section('title', '商品詳細')

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

    <a href="{{ route('mypage') }}">マイページ</a>
    <a href="{{ route('items.create') }}">出品</a>
@endsection

@section('content')
<div class="item-detail">
    <div class="item-detail__image">
        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
    </div>

    <div class="item-detail__content">
        <h1>{{ $item->name }}</h1>

        <p>{{ $item->brand_name }}</p>

        <p>¥{{ number_format($item->price) }} (税込)</p>

        <div class="item-detail__counts">
            {{-- いいね --}}
            @auth
                @if(auth()->id() !== $item->user_id)
                    @if($isLiked)
                        <form action="{{ route('likes.destroy', ['item_id' => $item->id]) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none;border:none;">
                                <img src="{{ asset('images/heart-liked.png') }}" width="20">
                            </button>
                        </form>
                    @else
                        <form action="{{ route('likes.store', ['item_id' => $item->id]) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" style="background:none;border:none;">
                                <img src="{{ asset('images/heart-default.png') }}" width="20">
                            </button>
                        </form>
                    @endif
                @else
                    <img src="{{ asset('images/heart-default.png') }}" width="20">
                @endif
            @endauth

            @guest
                <img src="{{ asset('images/heart-default.png') }}" width="20">
            @endguest

            <span>{{ $likeCount }}</span>

            {{-- コメント --}}
            <span style="margin-left:10px;">
                <img src="{{ asset('images/comment.png') }}" width="20">
            </span>
            <span>{{ $commentCount }}</span>
        </div>

        @auth
            @if(auth()->id() !== $item->user_id)
                <a href="{{ route('purchase.create', ['item_id' => $item->id]) }}" class="purchase-btn">
                    購入手続きへ
                </a>
            @else
                <button class="purchase-btn disabled" disabled>
                    購入手続きへ
                </button>
            @endif
        @else
            <a href="{{ route('login') }}" class="purchase-btn">
                購入手続きへ
            </a>
        @endauth

        <h2>商品説明</h2>
        <p>{{ $item->description }}</p>

        <h2>商品の情報</h2>

        <div>
            <strong>カテゴリー</strong>
            @foreach($item->categories as $category)
                <span>{{ $category->name }}</span>
            @endforeach
        </div>

        <div>
            <strong>商品の状態</strong>
            <span>{{ $item->condition->name }}</span>
        </div>

        <h2>コメント({{ $commentCount }})</h2>

        @foreach($item->comments as $comment)
            <div class="item-detail__comment">
                <div class="item-detail__comment-user">
                    <div class="item-detail__comment-icon">
                        @if($comment->user->profile_image_path)
                            <img src="{{ asset('storage/' . $comment->user->profile_image_path) }}" alt="{{ $comment->user->name }}">
                        @endif
                    </div>
                    <p class="item-detail__comment-user-name">{{ $comment->user->name }}</p>
                </div>

                <p class="item-detail__comment-text">{{ $comment->content }}</p>
            </div>
        @endforeach

        <h2>商品へのコメント</h2>

        @auth
            <form action="{{ route('comments.store', ['item_id' => $item->id]) }}" method="POST">
                @csrf
                <textarea name="content">{{ old('content') }}</textarea>
                @error('content')
                    <p>{{ $message }}</p>
                @enderror
                <button type="submit">コメントを送信する</button>
            </form>
        @else
            <form action="{{ route('login') }}" method="GET">
                <textarea name="content"></textarea>
                <button type="submit">コメントを送信する</button>
            </form>
        @endauth
    </div>
</div>
@endsection