@extends('layouts.app')
@section('title', '顧客一覧')
@section('content')
<div class="header-row">
    <h1>顧客一覧</h1>
    <div class="actions">
        <a href="{{ route('customers.export_csv') }}" class="btn btn--secondary">CSV出力</a>
        <a href="{{ route('customers.import') }}" class="btn btn--secondary">CSV取込</a>
        <a href="{{ route('customers.create') }}" class="btn btn--primary">新規登録</a>
    </div>
</div>

<form method="get" action="{{ route('customers.index') }}" class="card" style="margin-bottom:16px; display:flex; gap:8px; align-items:flex-end;">
    <div class="form-group" style="margin-bottom:0; flex:1;">
        <label for="q">顧客名で検索</label>
        <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="顧客名を入力">
    </div>
    <button type="submit" class="btn btn--primary">検索</button>
    @if(request('q'))
        <a href="{{ route('customers.index') }}" class="btn btn--secondary">クリア</a>
    @endif
</form>

@if($customers->isEmpty())
    @if(request('q'))
        <p>検索条件に一致する顧客がありません。</p>
    @else
        <p>顧客が登録されていません。</p>
    @endif
@else
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>名前</th>
            <th>電話番号</th>
            <th>メール</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($customers as $customer)
        <tr>
            <td>{{ $customer->id }}</td>
            <td>{{ $customer->name }}</td>
            <td>{{ $customer->phone ?? '-' }}</td>
            <td>{{ $customer->email ?? '-' }}</td>
            <td>
                <div class="actions">
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn--secondary btn--sm">編集</a>
                    <form method="post" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('削除しますか？')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn--danger btn--sm" aria-label="{{ $customer->name }}を削除">削除</button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endsection
