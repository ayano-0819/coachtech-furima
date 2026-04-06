@extends('layouts.app')

@section('title', '商品出品')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/create.css') }}">
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
<div class="sell">
    <div class="sell__inner">
        <h1 class="sell__title">商品の出品</h1>

        <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="sell__form">
            @csrf

            <div class="sell__section">
                <p class="sell__label">商品画像</p>
                <div class="sell__image-box">
                    <label for="image" class="sell__image-button">画像を選択する</label>
                    <input type="file" name="image" id="image" class="sell__image-input">
                </div>
                @error('image')
                    <p class="sell__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="sell__section">
                <h2 class="sell__heading">商品の詳細</h2>

                <label class="sell__label sell__label--category">カテゴリー</label>
                <div class="sell__category-list">
                    @foreach($categories as $category)
                        <label class="sell__category-item">
                            <input
                                type="checkbox"
                                name="categories[]"
                                value="{{ $category->id }}"
                                class="sell__category-checkbox"
                                {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}
                            >
                            <span class="sell__category-text">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('categories')
                    <p class="sell__error">{{ $message }}</p>
                @enderror

                <label for="condition_id" class="sell__label">商品の状態</label>
                    <select id="condition_id" name="condition_id" class="sell__select">
                        <option value="">選択してください</option>
                        @foreach($conditions as $condition)
                            <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}>
                                {{ $condition->name }}
                            </option>
                        @endforeach
                    </select>
                @error('condition_id')
                    <p class="sell__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="sell__section">
                <h2 class="sell__heading">商品名と説明</h2>

                <label for="name" class="sell__label">商品名</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="sell__input"
                >
                @error('name')
                    <p class="sell__error">{{ $message }}</p>
                @enderror

                <label for="brand_name" class="sell__label">ブランド名</label>
                    <input
                        id="brand_name"
                        type="text"
                        name="brand_name"
                        value="{{ old('brand_name') }}"
                        class="sell__input"
                    >

                <label for="description" class="sell__label">商品の説明</label>
                <textarea
                    id="description"
                    name="description"
                    class="sell__textarea"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="sell__error">{{ $message }}</p>
                @enderror

                <label for="price" class="sell__label">販売価格</label>
                <div class="sell__price-box">
                    <span class="sell__price-mark">¥</span>
                    <input
                        id="price"
                        type="text"
                        name="price"
                        value="{{ old('price') }}"
                        class="sell__input sell__input--price"
                    >
                </div>
                @error('price')
                    <p class="sell__error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="sell__submit">出品する</button>
        </form>
    </div>
</div>
@endsection