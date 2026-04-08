<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'coachtech-furima')</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>
<body>

    <header class="header">
        <div class="header__inner">
            <div class="header__left">
                <a href="{{ route('items.index') }}" class="header__logo">
                    <img
                        src="{{ asset('images/header-logo.png') }}"
                        alt="COACHTECHのロゴ"
                    >
                </a>
            </div>

            @yield('header-nav')
        </div>
    </header>

    <main class="main">
        @yield('content')
    </main>

</body>
</html>
