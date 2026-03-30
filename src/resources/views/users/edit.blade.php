@extends('layouts.app')

@section('title', 'プロフィール設定')

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
<div class="profile-setting">
    <div class="profile-setting__inner">
        <h1 class="profile-setting__title">プロフィール設定</h1>

        <form action="{{ route('mypage.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="profile-setting__image-area">
                <div class="profile-setting__image-preview">
                    @if($user->profile_image_path)
                        <img src="{{ asset('storage/' . $user->profile_image_path) }}" alt="プロフィール画像">
                    @endif
                </div>

                <label for="image" class="profile-setting__image-button">
                    画像を選択する
                </label>
                <input type="file" name="profile_image" id="image" class="profile-setting__file">

                @error('profile_image')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div class="profile-setting__group">
                <label for="name" class="profile-setting__label">ユーザー名</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    class="profile-setting__input"
                    value="{{ old('name', $user->name ?? '') }}"
                >
                @error('name')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div class="profile-setting__group">
                <label for="postal_code" class="profile-setting__label">郵便番号</label>
                <input
                    type="text"
                    name="postal_code"
                    id="postal_code"
                    class="profile-setting__input"
                    value="{{ old('postal_code', $user->postal_code ?? '') }}"
                >
                @error('postal_code')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div class="profile-setting__group">
                <label for="address" class="profile-setting__label">住所</label>
                <input
                    type="text"
                    name="address"
                    id="address"
                    class="profile-setting__input"
                    value="{{ old('address', $user->address ?? '') }}"
                >
                @error('address')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div class="profile-setting__group">
                <label for="building" class="profile-setting__label">建物名</label>
                <input
                    type="text"
                    name="building"
                    id="building"
                    class="profile-setting__input"
                    value="{{ old('building', $user->building ?? '') }}"
                >
            </div>

            <button type="submit" class="profile-setting__submit">
                更新する
            </button>
        </form>
    </div>
</div>
@endsection