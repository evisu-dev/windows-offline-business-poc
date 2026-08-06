@extends('layouts.app')
@section('title', 'システム情報')
@section('content')
<h1>システム情報</h1>

<div class="card">
    <h2 style="font-size:18px; margin-top:0;">アプリケーション</h2>
    <dl>
        <dt>アプリ名</dt>
        <dd>{{ config('app.name') }}</dd>
        <dt>バージョン</dt>
        <dd>{{ $appVersion }}</dd>
        <dt>App ID</dt>
        <dd>{{ $appId }}</dd>
        <dt>Laravel</dt>
        <dd>{{ $laravelVersion }}</dd>
    </dl>
</div>

<div class="card">
    <h2 style="font-size:18px; margin-top:0;">実行環境</h2>
    <dl>
        <dt>PHP</dt>
        <dd>{{ $phpVersion }}</dd>
        <dt>OS</dt>
        <dd>{{ $os }}</dd>
        <dt>データベース</dt>
        <dd>{{ $dbDriver }}</dd>
        <dt>DBファイル</dt>
        <dd style="word-break:break-all;">{{ $dbPath }}</dd>
        <dt>DBサイズ</dt>
        <dd>{{ $dbSize }}</dd>
    </dl>
</div>

<div class="card">
    <h2 style="font-size:18px; margin-top:0;">自動更新</h2>
    <dl>
        <dt>更新機能</dt>
        <dd>{{ $updaterEnabled }}</dd>
        <dt>更新プロバイダー</dt>
        <dd>{{ $updaterProvider }}</dd>
    </dl>
    @if(!config('nativephp.updater.enabled'))
        <p style="color:#6b7280; font-size:13px;">自動更新は現在無効です。本番ビルド時に有効化されます。</p>
    @endif
</div>
@endsection
