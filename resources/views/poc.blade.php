<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Offline Work Order Manager PoC</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Yu Gothic UI", "Meiryo", sans-serif; margin: 0; background: #f4f5f7; color: #1f2937; }
        main { max-width: 760px; margin: 48px auto; padding: 32px; background: #fff; border: 1px solid #d1d5db; border-radius: 12px; }
        h1 { margin-top: 0; font-size: 28px; }
        .status { padding: 12px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; }
        dl { display: grid; grid-template-columns: 180px 1fr; gap: 12px; }
        dt { font-weight: 700; }
        dd { margin: 0; overflow-wrap: anywhere; }
        button { padding: 10px 16px; font: inherit; cursor: pointer; }
    </style>
</head>
<body>
<main>
    <h1>Offline Work Order Manager PoC</h1>
    <p>Windowsインストール型・オフライン業務アプリの成立確認画面です。</p>

    @if (session('status'))
        <p class="status">{{ session('status') }}</p>
    @endif

    <dl>
        <dt>SQLite書き込み件数</dt>
        <dd>{{ $count }}</dd>
        <dt>データベース</dt>
        <dd>{{ $databasePath }}</dd>
        <dt>アプリ版</dt>
        <dd>{{ config('nativephp.version') }}</dd>
    </dl>

    <form method="post" action="/write-test">
        @csrf
        <button type="submit">SQLiteへテストデータを書き込む</button>
    </form>
</main>
</body>
</html>
