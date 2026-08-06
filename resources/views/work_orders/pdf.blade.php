<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>作業指示書 #{{ $workOrder->id }}</title>
    <style>
        body { font-family: "IPAexGothic", "Hiragino Kaku Gothic ProN", "Yu Gothic", "Meiryo", sans-serif; font-size: 12px; color: #1f2937; margin: 0; padding: 24px; }
        h1 { font-size: 20px; text-align: center; margin-bottom: 24px; border-bottom: 2px solid #1f2937; padding-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #6b7280; padding: 8px 10px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; width: 120px; font-weight: 600; }
        .footer { margin-top: 32px; font-size: 10px; color: #6b7280; text-align: right; }
        .description { white-space: pre-wrap; }
    </style>
</head>
<body>
<h1>作業指示書</h1>

<table>
    <tr>
        <th>受注番号</th>
        <td>#{{ $workOrder->id }}</td>
    </tr>
    <tr>
        <th>顧客名</th>
        <td>{{ $workOrder->customer->name }}</td>
    </tr>
    <tr>
        <th>件名</th>
        <td>{{ $workOrder->title }}</td>
    </tr>
    <tr>
        <th>ステータス</th>
        <td>{{ $workOrder->status }}</td>
    </tr>
    <tr>
        <th>納期</th>
        <td>{{ $workOrder->due_date?->format('Y年m月d日') ?? '未定' }}</td>
    </tr>
    <tr>
        <th>登録日</th>
        <td>{{ $workOrder->created_at->format('Y年m月d日') }}</td>
    </tr>
    <tr>
        <th>詳細</th>
        <td class="description">{{ $workOrder->description ?? 'なし' }}</td>
    </tr>
</table>

@if($workOrder->customer->phone || $workOrder->customer->address)
<table>
    <tr>
        <th>顧客電話</th>
        <td>{{ $workOrder->customer->phone ?? '-' }}</td>
    </tr>
    <tr>
        <th>顧客住所</th>
        <td>{{ $workOrder->customer->address ?? '-' }}</td>
    </tr>
</table>
@endif

<div class="footer">
    出力日時: {{ now()->format('Y-m-d H:i') }} / Offline Work Order Manager
</div>
</body>
</html>
