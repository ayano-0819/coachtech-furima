@extends('layouts.app')

@section('title', '商品出品')

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

    <a href="/mypage">マイページ</a>
    <a href="/sell">出品</a>
@endsection

@section('content')
<div class="sell">
    <div class="sell__inner">
        <h1 class="sell__title">商品の出品</h1>

        <form action="/sell" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="sell__section">
                <label class="sell__label">商品画像</label>
                <div class="sell__image-box">
                    <label for="image" class="sell__image-button">画像を選択する</label>
                    <input type="file" name="image" id="image">
                </div>
            </div>

            <div class="sell__section">
                <h2 class="sell__heading">商品の詳細</h2>

                <label class="sell__label">カテゴリー</label>
                <div class="sell__category-list">
                    @foreach($categories as $category)
                        <label>
                            <input
                                type="checkbox"
                                name="categories[]"
                                value="{{ $category->id }}"
                            >
                            {{ $category->name }}
                        </label>
                    @endforeach
                    </div>

                <label class="sell__label">商品の状態</label>
                <select name="condition_id">
                    <option value="">選択してください</option>
                        @foreach($conditions as $condition)
                            <option value="{{ $condition->id }}">
                                {{ $condition->name }}
                            </option>
                        @endforeach

</select>
            </div>

            <div class="sell__section">
                <h2 class="sell__heading">商品名と説明</h2>

                <label class="sell__label">商品名</label>
                <input type="text" name="name">

                <label class="sell__label">ブランド名</label>
                <input type="text" name="brand_name">

                <label class="sell__label">商品の説明</label>
                <textarea name="description"></textarea>

                <label class="sell__label">販売価格</label>
                <input type="text" name="price">
            </div>

            <button type="submit">出品する</button>
        </form>
    </div>
</div>
@endsection