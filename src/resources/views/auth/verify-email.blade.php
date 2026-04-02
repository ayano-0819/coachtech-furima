@extends('layouts.app')

@section('title', 'メール認証')

@section('content')
<div style="text-align: center; margin-top: 80px;">

    <p>
        登録していただいたメールアドレスに認証メールを送りました。<br>
        メール認証を完了してください。
    </p>

    <!-- 認証はこちらからボタン（※実際はメール確認させる導線） -->
    <div style="margin-top: 30px;">
        <a href="https://mailtrap.io/inboxes" target="_blank" rel="noopener noreferrer" style="text-decoration: none;">
            <button style="padding: 10px 20px; cursor: pointer;">
                認証はこちらから
            </button>
        </a>
    </div>

    <!-- 再送 -->
    <div style="margin-top: 20px;">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" style="color: blue; background: none; border: none; cursor: pointer;">
                認証メールを再送する
            </button>
        </form>
    </div>

    <!-- メッセージ表示（再送後） -->
    @if (session('message'))
        <div style="margin-top: 20px; color: green;">
            {{ session('message') }}
        </div>
    @endif

</div>
@endsection