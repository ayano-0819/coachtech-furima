@extends('layouts.app')

@section('title', '商品購入')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase/create.css') }}">
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
                <button type="submit" class="header__link header__logout-button">
                    ログアウト
                </button>
            </form>
        @endauth

        <a href="{{ route('mypage') }}" class="header__link">マイページ</a>
        <a href="{{ route('items.create') }}" class="header__sell-button">出品</a>
    </div>
@endsection

@section('content')
    <div class="purchase">
        <div class="purchase__inner">

            <div class="purchase__left">

                <div class="purchase__product">
                    <div class="purchase__image">
                        <img
                            src="{{ asset('storage/' . $item->image_path) }}"
                            alt="{{ $item->name }}"
                            class="purchase__image-tag"
                        >
                    </div>

                    <div class="purchase__product-info">
                        <h1 class="purchase__product-name">{{ $item->name }}</h1>
                        <p class="purchase__product-price">
                            ¥ {{ number_format($item->price) }}
                        </p>
                    </div>
                </div>

                <form
                    action="{{ route('purchase.create', ['item_id' => $item->id]) }}"
                    method="GET"
                    class="purchase__payment-form"
                >
                    <div class="purchase__section">
                        <label for="payment_method" class="purchase__label">
                            支払い方法
                        </label>

                        <select
                            id="payment_method"
                            name="payment_method"
                            class="purchase__select"
                            onchange="this.form.submit()"
                        >
                            <option value="">選択してください</option>

                            <option value="convenience" {{ request('payment_method') === 'convenience' ? 'selected' : '' }}>コンビニ支払い</option>
                            <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>カード支払い</option>
                        </select>

                        @error('payment_method')
                            <p class="purchase__error">{{ $message }}</p>
                        @enderror
                    </div>
                </form>

                <div class="purchase__section">
                    <div class="purchase__address-header">
                        <h2 class="purchase__label">配送先</h2>

                        <a
                            href="{{ route('purchase.address.edit', ['item_id' => $item->id, 'payment_method' => request('payment_method')]) }}"
                            class="purchase__address-link"
                        >
                            変更する
                        </a>
                    </div>

                    <div class="purchase__address-body">
                        <p class="purchase__address-postal">
                            〒 {{ session('postal_code', auth()->user()->postal_code) }}
                        </p>

                        <p class="purchase__address-text">
                            {{ session('address', auth()->user()->address) }}
                            {{ session('building', auth()->user()->building) }}
                        </p>

                        @error('shipping_address')
                            <p class="purchase__error purchase__error--address">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            <div class="purchase__right">

                <div class="purchase__summary">
                    <div class="purchase__summary-row">
                        <span class="purchase__summary-label">商品代金</span>
                        <span class="purchase__summary-value">
                            ¥ {{ number_format($item->price) }}
                        </span>
                    </div>

                    <div class="purchase__summary-row">
                        <span class="purchase__summary-label">支払い方法</span>
                        <span class="purchase__summary-value">
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

                <form
                    action="{{ route('purchase.checkout', ['item_id' => $item->id]) }}"
                    method="POST"
                    class="purchase__submit-form"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="payment_method"
                        value="{{ request('payment_method') }}"
                    >

                    <button type="submit" class="purchase__submit-button">
                        購入する
                    </button>
                </form>

            </div>

        </div>
    </div>
@endsection
