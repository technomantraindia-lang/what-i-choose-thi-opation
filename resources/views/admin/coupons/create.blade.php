@extends('admin.layouts.app')
@section('title', 'Add Coupon')
@section('content')
<h2 class="mb-4">Add Coupon</h2>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.coupons.store') }}" method="POST">@csrf
        @include('admin.coupons._form')
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div></div>
@endsection
