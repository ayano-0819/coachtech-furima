@extends('layouts.app')

@section('title', '商品購入')

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
<div class="purchase">
    <div class="purchase__inner">
        <div class="purchase__left">

            <div class="purchase__product">
                <div class="purchase__image">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                </div>

                <div class="purchase__product-info">
                    <h1 class="purchase__product-name">{{ $item->name }}</h1>
                    <p class="purchase__product-price">¥ {{ number_format($item->price) }}</p>
                </div>
            </div>

            <form action="{{ route('purchase.create', ['item_id' => $item->id]) }}" method="GET">
                <div class="purchase__section">
                    <h2 class="purchase__label">支払い方法</h2>
                    <select name="payment_method" class="purchase__select" onchange="this.form.submit()">
                        <option value="">選択してください</option>
                        <option value="convenience" {{ request('payment_method') === 'convenience' ? 'selected' : '' }}>
                            コンビニ支払い
                        </option>
                        <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>
                            カード支払い
                        </option>
                    </select>
                </div>
            </form>

            <div class="purchase__section">
                <div class="purchase__address-header">
                    <h2 class="purchase__label">配送先</h2>
                    <a href="{{ route('purchase.address.edit', ['item_id' => $item->id]) }}">変更する</a>
                </div>

                <div class="purchase__address-body">
                    <p class="purchase__address-postal">
                        〒 {{ session('postal_code', auth()->user()->postal_code) }}
                    </p>
                    <p class="purchase__address-text">
                        {{ session('address', auth()->user()->address) }}
                        {{ session('building', auth()->user()->building) }}
                    </p>
                </div>
            </div>

        </div>

        <div class="purchase__right">
            <div class="purchase__summary">
                <div class="purchase__summary-row">
                    <span>商品代金</span>
                    <span>¥ {{ number_format($item->price) }}</span>
                </div>

                <div class="purchase__summary-row">
                    <span>支払い方法</span>
                    <span>
                        @if(request('payment_method') === 'convenience')
                            コンビニ支払い
                        @elseif(request('payment_method') === 'card')
                            カード支払い
                        @else
                            未選択
                        @endif
                    </span>
                </div>
            </div>

            <button type="button" class="purchase__button">
                購入する
            </button>
        </div>
    </div>
</div>
@endsection