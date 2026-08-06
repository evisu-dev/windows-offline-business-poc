@extends('layouts.app')
@section('title', 'バックアップ・復元')
@section('content')
<h1>バックアップ・復元</h1>

@if (session('error'))
    <div class="alert alert--error">{{ session('error') }}</div>
@endif

<div class="card">
    <h2 style="font-size:18px; margin-top:0;">データベース情報</h2>
    <dl>
        <dt>ファイルパス</dt>
        <dd style="word-break:break-all;">{{ $dbPath }}</dd>
        <dt>ファイルサイズ</dt>
        <dd>{{ $dbSize }}</dd>
    </dl>
</div>

<div class="card">
    <h2 style="font-size:18px; margin-top:0;">バックアップ（ダウンロード）</h2>
    <p>現在のデータベースをファイルとしてダウンロードします。</p>
    <a href="{{ route('backup.download') }}" class="btn btn--primary">データベースをダウンロード</a>
</div>

<div class="card">
    <h2 style="font-size:18px; margin-top:0;">復元（リストア）</h2>
    <p>以前ダウンロードしたバックアップファイルをアップロードして復元します。<br>
    <strong>注意:</strong> 現在のデータはすべて上書きされます。</p>
    <form method="post" action="{{ route('backup.restore') }}" enctype="multipart/form-data" onsubmit="return confirm('現在のデータを上書きして復元します。よろしいですか？')">
        @csrf
        <div class="form-group">
            <label for="backup_file">バックアップファイル（.sqlite）</label>
            <input type="file" id="backup_file" name="backup_file" accept=".sqlite,.db" required>
            @error('backup_file') <div class="error">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn btn--danger">復元する</button>
    </form>
</div>
@endsection
