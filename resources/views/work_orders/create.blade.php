@extends('layouts.app')
@section('title', '受注登録')
@section('content')
<div class="header-row">
    <h1>受注登録</h1>
    <a href="{{ route('work_orders.index') }}" class="btn btn--secondary">戻る</a>
</div>

<div class="card">
    <form method="post" action="{{ route('work_orders.store') }}">
        @csrf
        @include('work_orders._form')
        <button type="submit" class="btn btn--primary">登録</button>
    </form>
</div>
@endsection
