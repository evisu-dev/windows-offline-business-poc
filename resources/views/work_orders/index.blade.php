@extends('layouts.app')
@section('title', '受注一覧')
@section('content')
<div class="header-row">
    <h1>受注一覧</h1>
    <div class="actions">
        <a href="{{ route('work_orders.export_csv') }}" class="btn btn--secondary">CSV出力</a>
        <a href="{{ route('work_orders.create') }}" class="btn btn--primary">新規登録</a>
    </div>
</div>

<form method="get" action="{{ route('work_orders.index') }}" class="card" style="margin-bottom:16px;">
    <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:flex-end;">
        <div class="form-group" style="margin-bottom:0; flex:1; min-width:150px;">
            <label for="q">件名で検索</label>
            <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="件名を入力">
        </div>
        <div class="form-group" style="margin-bottom:0; min-width:120px;">
            <label for="status">ステータス</label>
            <select id="status" name="status">
                <option value="">すべて</option>
                @foreach(\App\Models\WorkOrder::STATUSES as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin-bottom:0; min-width:150px;">
            <label for="customer_id">顧客</label>
            <select id="customer_id" name="customer_id">
                <option value="">すべて</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn--primary">検索</button>
        @if(request('q') || request('status') || request('customer_id'))
            <a href="{{ route('work_orders.index') }}" class="btn btn--secondary">クリア</a>
        @endif
    </div>
</form>

@if($workOrders->isEmpty())
    @if(request('q') || request('status') || request('customer_id'))
        <p>検索条件に一致する受注がありません。</p>
    @else
        <p>受注が登録されていません。</p>
    @endif
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
                    <a href="{{ route('work_orders.export_pdf', $workOrder) }}" class="btn btn--secondary btn--sm">PDF</a>
                    <a href="{{ route('work_orders.edit', $workOrder) }}" class="btn btn--secondary btn--sm">編集</a>
                    <form method="post" action="{{ route('work_orders.destroy', $workOrder) }}" onsubmit="return confirm('削除しますか？')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn--danger btn--sm" aria-label="{{ $workOrder->title }}を削除">削除</button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endsection
