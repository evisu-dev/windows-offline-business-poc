@extends('layouts.app')
@section('title', '顧客CSV取込')
@section('content')
<div class="header-row">
    <h1>顧客CSV取込</h1>
    <a href="{{ route('customers.index') }}" class="btn btn--secondary">戻る</a>
</div>

@if (session('error'))
    <div class="alert alert--error">{{ session('error') }}</div>
@endif

@if (session('import_errors'))
    <div class="alert alert--error">
        <ul style="margin:0; padding-left:20px;">
            @foreach (session('import_errors') as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <form method="post" action="{{ route('customers.import_store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="csv_file">CSVファイル</label>
            <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
            @error('csv_file') <div class="error">{{ $message }}</div> @enderror
        </div>

        <details style="margin-bottom:16px; font-size:13px; color:#6b7280;">
            <summary>対応形式</summary>
            <ul style="margin-top:8px;">
                <li>文字コード: UTF-8 / UTF-8 BOM付き</li>
                <li>必須列: 名前</li>
                <li>列順: 名前, 電話番号, メール, 住所, 備考</li>
                <li>1行目はヘッダ行として扱います</li>
                <li>エラーがある場合は1件も登録されません</li>
            </ul>
        </details>

        <button type="submit" class="btn btn--primary">取込</button>
    </form>
</div>
@endsection
