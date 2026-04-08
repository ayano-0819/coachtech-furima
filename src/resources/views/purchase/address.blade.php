@extends('layouts.app')

@section('title', '住所の変更')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase/address.css') }}">
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
    <div class="address-edit">
        <div class="address-edit__inner">
            <h1 class="address-edit__title">住所の変更</h1>

            <form
                action="{{ route('purchase.address.update', ['item_id' => $item_id]) }}"
                method="POST"
                class="address-edit__form"
            >
                @csrf

                <input
                    type="hidden"
                    name="payment_method"
                    value="{{ request('payment_method') }}"
                >

                <div class="address-edit__group">
                    <label for="postal_code" class="address-edit__label">郵便番号</label>
                    <input
                        type="text"
                        name="postal_code"
                        id="postal_code"
                        class="address-edit__input"
                        value="{{ old('postal_code', session('postal_code', $user->postal_code)) }}"
                    >
                    @error('postal_code')
                        <p class="address-edit__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="address-edit__group">
                    <label for="address" class="address-edit__label">住所</label>
                    <input
                        type="text"
                        name="address"
                        id="address"
                        class="address-edit__input"
                        value="{{ old('address', session('address', $user->address)) }}"
                    >
                    @error('address')
                        <p class="address-edit__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="address-edit__group">
                    <label for="building" class="address-edit__label">建物名</label>
                    <input
                        type="text"
                        name="building"
                        id="building"
                        class="address-edit__input"
                        value="{{ old('building', session('building', $user->building)) }}"
                    >
                </div>

                <button type="submit" class="address-edit__submit">
                    更新する
                </button>
            </form>
        </div>
    </div>
@endsection
