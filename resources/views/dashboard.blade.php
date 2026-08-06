@extends('layouts.app')
@section('title', 'ダッシュボード')
@section('content')
<h1>ダッシュボード</h1>
<p>Windowsインストール型・オフライン業務アプリの管理画面です。</p>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:16px; margin:24px 0;">
    <div class="card" style="text-align:center;">
        <div style="font-size:32px; font-weight:700; color:#2563eb;">{{ $customerCount }}</div>
        <div style="font-size:14px; color:#6b7280; margin-top:4px;">顧客数</div>
    </div>
    <div class="card" style="text-align:center;">
        <div style="font-size:32px; font-weight:700; color:#2563eb;">{{ $workOrderCount }}</div>
        <div style="font-size:14px; color:#6b7280; margin-top:4px;">受注数</div>
    </div>
    <div class="card" style="text-align:center;">
        <div style="font-size:32px; font-weight:700; color:#dc2626;">{{ $pendingCount }}</div>
        <div style="font-size:14px; color:#6b7280; margin-top:4px;">未着手</div>
    </div>
    <div class="card" style="text-align:center;">
        <div style="font-size:32px; font-weight:700; color:#d97706;">{{ $inProgressCount }}</div>
        <div style="font-size:14px; color:#6b7280; margin-top:4px;">進行中</div>
    </div>
</div>

<div class="card">
    <h2 style="font-size:18px; margin-top:0;">クイック操作</h2>
    <div style="display:flex; flex-wrap:wrap; gap:12px;">
        <a href="{{ route('customers.create') }}" class="btn btn--primary">顧客を登録</a>
        <a href="{{ route('work_orders.create') }}" class="btn btn--primary">受注を登録</a>
        <a href="{{ route('work_orders.export_csv') }}" class="btn btn--secondary">受注CSV出力</a>
        <a href="{{ route('backup.download') }}" class="btn btn--secondary">バックアップ</a>
    </div>
</div>

<div class="card">
    <h2 style="font-size:18px; margin-top:0;">アプリ情報</h2>
    <dl>
        <dt>バージョン</dt>
        <dd>{{ config('nativephp.version', '不明') }}</dd>
        <dt>環境</dt>
        <dd>{{ config('app.env') }}</dd>
    </dl>
</div>
@endsection
