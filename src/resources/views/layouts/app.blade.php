<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
</head>
<body>

<header class="header">
    <div class="header__inner">
        <a href="/" class="header__logo">COACHTECH</a>

        @yield('header-nav')

    </div>
</header>

<main class="main">
    @yield('content')
</main>

</body>
</html>