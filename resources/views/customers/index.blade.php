@extends('layouts.app')
@section('title', '顧客一覧')
@section('content')
<div class="header-row">
    <h1>顧客一覧</h1>
    <a href="{{ route('customers.create') }}" class="btn btn--primary">新規登録</a>
</div>

@if($customers->isEmpty())
    <p>顧客が登録されていません。</p>
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
                        <button type="submit" class="btn btn--danger btn--sm">削除</button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endsection
