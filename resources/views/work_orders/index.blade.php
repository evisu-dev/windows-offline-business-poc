@extends('layouts.app')
@section('title', '受注一覧')
@section('content')
<div class="header-row">
    <h1>受注一覧</h1>
    <a href="{{ route('work_orders.create') }}" class="btn btn--primary">新規登録</a>
</div>

@if($workOrders->isEmpty())
    <p>受注が登録されていません。</p>
@else
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>顧客</th>
            <th>件名</th>
            <th>ステータス</th>
            <th>納期</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($workOrders as $workOrder)
        <tr>
            <td>{{ $workOrder->id }}</td>
            <td>{{ $workOrder->customer->name }}</td>
            <td>{{ $workOrder->title }}</td>
            <td>
                @php
                    $badgeClass = match($workOrder->status) {
                        '進行中' => 'badge--active',
                        '完了' => 'badge--done',
                        default => '',
                    };
                @endphp
                <span class="badge {{ $badgeClass }}">{{ $workOrder->status }}</span>
            </td>
            <td>{{ $workOrder->due_date?->format('Y-m-d') ?? '-' }}</td>
            <td>
                <div class="actions">
                    <a href="{{ route('work_orders.edit', $workOrder) }}" class="btn btn--secondary btn--sm">編集</a>
                    <form method="post" action="{{ route('work_orders.destroy', $workOrder) }}" onsubmit="return confirm('削除しますか？')">
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
