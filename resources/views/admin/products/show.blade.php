@extends('admin.layouts.app')
@section('title', $product->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>{{ $product->name }}</h2>
    <div class="d-flex gap-2">
        <form action="{{ route('cart.add', $product) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-primary">Add to Cart</button>
        </form>
        <form action="{{ route('cart.buyNow', $product) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary">Buy Now</button>
        </form>
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">Edit</a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        @if($product->image)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="img-fluid rounded mb-3">
        @endif
        @if($product->images->count())
        <div class="d-flex flex-wrap gap-2">
            @foreach($product->images as $img)
            <img src="{{ $img->image_url }}" alt="{{ $product->name }} gallery image" class="rounded" style="width:70px;height:70px;object-fit:cover;">
            @endforeach
        </div>
        @endif
    </div>
    <div class="col-md-8">
        <div class="card mb-3"><div class="card-body">
            <p><strong>SKU:</strong> {{ $product->sku }} | <strong>Slug:</strong> {{ $product->slug }}</p>
            <p><strong>Category:</strong> {{ $product->category?->name }} @if($product->subCategory) -> {{ $product->subCategory->name }}@endif @if($product->subSubCategory) -> {{ $product->subSubCategory->name }}@endif</p>
            <p><strong>Brand:</strong> {{ $product->brand?->name ?? 'N/A' }}</p>
            <p><strong>Price:</strong> ₹{{ number_format($product->price,2) }} @if($product->sale_price) | <strong>Sale:</strong> ₹{{ number_format($product->sale_price,2) }}@endif</p>
            <p><strong>Stock:</strong> {{ $product->stock_qty }} {{ $product->unit }} | <strong>Min Order:</strong> {{ $product->min_order_qty }}</p>
            <p><strong>Status:</strong> <span class="badge bg-{{ $product->status==='active'?'success':'secondary' }}">{{ ucfirst($product->status) }}</span>
            @if($product->featured)<span class="badge bg-warning text-dark">Featured</span>@endif</p>
            <p>{{ $product->short_desc }}</p>
            <div>{!! nl2br(e($product->description)) !!}</div>
        </div></div>
        @if($product->inventoryLogs->count())
        <div class="card"><div class="card-header"><h5>Inventory History</h5></div><div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead><tr><th>Date</th><th>Type</th><th>Old</th><th>Change</th><th>New</th><th>Note</th></tr></thead>
                <tbody>
                    @foreach($product->inventoryLogs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('M d, Y') }}</td>
                        <td>{{ ucfirst($log->type) }}</td>
                        <td>{{ $log->old_qty }}</td>
                        <td>{{ $log->change_qty > 0 ? '+' : '' }}{{ $log->change_qty }}</td>
                        <td>{{ $log->new_qty }}</td>
                        <td>{{ $log->note ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div></div>
        @endif
    </div>
</div>
@endsection
