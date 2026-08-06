@extends('layouts.app')
@section('title', '顧客編集')
@section('content')
<div class="header-row">
    <h1>顧客編集</h1>
    <a href="{{ route('customers.index') }}" class="btn btn--secondary">戻る</a>
</div>

<div class="card">
    <form method="post" action="{{ route('customers.update', $customer) }}">
        @csrf
        @method('PUT')
        @include('customers._form')
        <button type="submit" class="btn btn--primary">更新</button>
    </form>
</div>
@endsection
