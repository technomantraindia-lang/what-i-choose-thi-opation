@extends('admin.layouts.app')
@section('title', 'Coupons')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Coupons</h2>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Coupon</a>
</div>
<div class="card"><div class="card-body">
    <table class="table table-hover">
        <thead><tr><th>Code</th><th>Type</th><th>Value</th><th>Min Order</th><th>Usage</th><th>Valid Until</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse ($coupons as $coupon)
            <tr>
                <td><strong>{{ $coupon->code }}</strong></td>
                <td>{{ ucfirst($coupon->type) }}</td>
                <td>{{ $coupon->type === 'percentage' ? $coupon->value.'%' : '₹'.$coupon->value }}</td>
                <td>₹{{ number_format($coupon->min_order ?? 0, 0) }}</td>
                <td>{{ $coupon->usages_count }}{{ $coupon->usage_limit ? '/'.$coupon->usage_limit : '' }}</td>
                <td>{{ $coupon->end_date?->format('M d, Y') ?? '-' }}</td>
                <td><span class="badge bg-{{ $coupon->status==='active'?'success':'secondary' }}">{{ ucfirst($coupon->status) }}</span></td>
                <td>
                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button></form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted">No coupons found</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $coupons->links() }}
</div></div>
@endsection
