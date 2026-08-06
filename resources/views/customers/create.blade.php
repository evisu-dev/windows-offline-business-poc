@extends('layouts.app')
@section('title', '顧客登録')
@section('content')
<div class="header-row">
    <h1>顧客登録</h1>
    <a href="{{ route('customers.index') }}" class="btn btn--secondary">戻る</a>
</div>

<div class="card">
    <form method="post" action="{{ route('customers.store') }}">
        @csrf
        @include('customers._form')
        <button type="submit" class="btn btn--primary">登録</button>
    </form>
</div>
@endsection
