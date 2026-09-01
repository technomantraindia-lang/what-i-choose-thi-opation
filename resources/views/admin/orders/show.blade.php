@extends('admin.layouts.app')

@section('title', 'Order ' . $order->order_num)

@push('styles')
<style>
    .od-page { max-width: 1280px; }
    .od-topbar {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: 12px; margin-bottom: 20px;
    }
    .od-topbar h2 { font-size: 1.5rem; margin: 0; color: #1a1a2e; }
    .od-topbar .sub { color: #888; font-size: 13px; }
    .od-actions .btn { font-size: 13px; }

    /* Horizontal status pipeline */
    .od-pipeline {
        display: flex; align-items: center; gap: 0;
        background: #fff; border-radius: 12px; padding: 16px 20px;
        box-shadow: 0 1px 6px rgba(0,0,0,.06); margin-bottom: 20px;
        overflow-x: auto;
    }
    .od-pipeline .pipe-step {
        display: flex; align-items: center; gap: 8px; white-space: nowrap;
        padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600;
        color: #aaa; background: #f4f4f8;
    }
    .od-pipeline .pipe-step.done { background: #e8eafd; color: #667eea; }
    .od-pipeline .pipe-step.active { background: linear-gradient(135deg,#667eea,#764ba2); color: #fff; }
    .od-pipeline .pipe-arrow { color: #ccc; margin: 0 4px; font-size: 10px; }

    /* Info tiles row */
    .od-tiles { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 20px; }
    @media(max-width:992px){ .od-tiles{ grid-template-columns: repeat(2,1fr); } }
    @media(max-width:576px){ .od-tiles{ grid-template-columns: 1fr; } }
    .od-tile {
        background: #fff; border-radius: 12px; padding: 16px 18px;
        box-shadow: 0 1px 6px rgba(0,0,0,.06);
        border-left: 4px solid #667eea;
    }
    .od-tile.tile-green { border-left-color: #10b981; }
    .od-tile.tile-blue { border-left-color: #3b82f6; }
    .od-tile.tile-orange { border-left-color: #f59e0b; }
    .od-tile .tile-label { font-size: 11px; text-transform: uppercase; letter-spacing: .5px; color: #999; margin-bottom: 4px; }
    .od-tile .tile-value { font-size: 15px; font-weight: 700; color: #1a1a2e; }
    .od-tile .tile-sub { font-size: 12px; color: #888; margin-top: 2px; }

    /* Main grid */
    .od-grid { display: grid; grid-template-columns: 1fr 380px; gap: 20px; }
    @media(max-width:992px){ .od-grid{ grid-template-columns: 1fr; } }

    .od-panel {
        background: #fff; border-radius: 12px;
        box-shadow: 0 1px 6px rgba(0,0,0,.06); overflow: hidden;
    }
    .od-panel-head {
        padding: 14px 20px; border-bottom: 1px solid #f0f0f0;
        font-weight: 700; font-size: 14px; color: #333;
        display: flex; justify-content: space-between; align-items: center;
    }

    /* Product rows (not table) */
    .od-product-row {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 20px; border-bottom: 1px solid #f5f5f5;
    }
    .od-product-row:last-child { border-bottom: none; }
    .od-product-row img, .od-product-row .no-img {
        width: 56px; height: 56px; border-radius: 10px; object-fit: cover; flex-shrink: 0;
    }
    .od-product-row .no-img {
        background: #f0f0f5; display: flex; align-items: center; justify-content: center; color: #bbb;
    }
    .od-product-row .p-info { flex: 1; }
    .od-product-row .p-name { font-weight: 600; font-size: 14px; color: #222; }
    .od-product-row .p-meta { font-size: 12px; color: #999; }
    .od-product-row .p-qty { font-size: 13px; color: #666; min-width: 50px; text-align: center; }
    .od-product-row .p-price { font-weight: 700; font-size: 14px; color: #667eea; min-width: 80px; text-align: right; }

    /* Summary box */
    .od-summary { padding: 16px 20px; background: #fafafe; }
    .od-summary .sum-row {
        display: flex; justify-content: space-between; padding: 5px 0; font-size: 13px; color: #666;
    }
    .od-summary .sum-row.total {
        border-top: 2px solid #667eea; margin-top: 8px; padding-top: 10px;
        font-size: 16px; font-weight: 800; color: #1a1a2e;
    }
    .od-summary .sum-row.total span:last-child { color: #667eea; }

    /* Right column panels */
    .od-side-panel { margin-bottom: 16px; }
    .od-side-panel:last-child { margin-bottom: 0; }
    .od-side-body { padding: 16px 20px; }

    .od-customer-avatar {
        width: 44px; height: 44px; border-radius: 50%;
        background: linear-gradient(135deg,#667eea,#764ba2);
        color: #fff; display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 16px; flex-shrink: 0;
    }
    .od-info-line { font-size: 13px; color: #555; margin-bottom: 6px; }
    .od-info-line i { width: 20px; color: #667eea; }

    /* Activity log */
    .od-log-item {
        display: flex; gap: 10px; padding: 10px 0;
        border-bottom: 1px solid #f5f5f5; font-size: 12px;
    }
    .od-log-item:last-child { border-bottom: none; }
    .od-log-dot {
        width: 10px; height: 10px; border-radius: 50%;
        background: #667eea; margin-top: 4px; flex-shrink: 0;
    }

    /* Update form */
    .od-update-form .form-label { font-size: 12px; font-weight: 600; color: #555; margin-bottom: 4px; }
    .od-update-form .form-control, .od-update-form .form-select { font-size: 13px; border-radius: 8px; }
    .od-update-form .btn-save {
        background: linear-gradient(135deg,#667eea,#764ba2);
        border: none; color: #fff; font-weight: 600; border-radius: 8px; padding: 10px;
    }

    @media print {
        .sidebar, .navbar, .no-print { display: none !important; }
        .col-md-10 { width: 100% !important; max-width: 100% !important; }
        .od-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@php
    $isCancelled = in_array($order->status, ['cancelled', 'failed', 'refunded']);
    $stepIndex = $order->statusStepIndex();
    $steps = \App\Models\Order::statusSteps();
@endphp

<div class="od-page">

    {{-- Top Bar --}}
    <div class="od-topbar no-print">
        <div>
            <a href="{{ route('admin.orders.index') }}" class="text-decoration-none small text-muted"><i class="fas fa-arrow-left"></i> All Orders</a>
            <h2 class="mt-1">Order #{{ $order->order_num }}</h2>
            <div class="sub">Placed on {{ $order->created_at->format('l, F d, Y \a\t h:i A') }}</div>
        </div>
        <div class="od-actions d-flex gap-2">
            @if($order->invoice)
            <a href="{{ route('admin.invoices.show', $order->invoice) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-file-invoice"></i> Invoice</a>
            @endif
            @if($order->payment)
            <a href="{{ route('admin.payments.show', $order->payment) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-credit-card"></i> Payment</a>
            @endif
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="fas fa-print"></i> Print</button>
        </div>
    </div>

    {{-- Status Pipeline --}}
    @if(!$isCancelled)
    <div class="od-pipeline no-print">
        @foreach($steps as $i => $step)
            @if($i > 0)<span class="pipe-arrow"><i class="fas fa-chevron-right"></i></span>@endif
            <div class="pipe-step {{ $i < $stepIndex ? 'done' : ($i === $stepIndex ? 'active' : '') }}">
                @if($i < $stepIndex)<i class="fas fa-check-circle"></i>
                @elseif($step === 'pending')<i class="fas fa-clock"></i>
                @elseif($step === 'processing')<i class="fas fa-cog"></i>
                @elseif($step === 'packed')<i class="fas fa-box"></i>
                @elseif($step === 'shipped')<i class="fas fa-truck"></i>
                @else<i class="fas fa-check"></i>
                @endif
                {{ ucfirst($step) }}
            </div>
        @endforeach
    </div>
    @else
    <div class="alert alert-danger mb-3 no-print"><i class="fas fa-ban"></i> Order is <strong>{{ ucfirst($order->status) }}</strong></div>
    @endif

    {{-- Info Tiles --}}
    <div class="od-tiles">
        <div class="od-tile">
            <div class="tile-label">Customer</div>
            <div class="tile-value">{{ $order->user?->name ?? 'Guest' }}</div>
            <div class="tile-sub">{{ $order->user?->email ?? '' }}</div>
        </div>
        <div class="od-tile tile-green">
            <div class="tile-label">Payment</div>
            <div class="tile-value">{{ ucfirst($order->pay_status) }}</div>
            <div class="tile-sub">{{ $order->payment ? strtoupper(str_replace('_',' ',$order->payment->method)) : 'N/A' }} @if($order->payment?->txn_id) · {{ $order->payment->txn_id }}@endif</div>
        </div>
        <div class="od-tile tile-blue">
            <div class="tile-label">Shipping</div>
            <div class="tile-value">{{ $order->shippingMethod?->name ?? ($order->courier ?? 'Standard') }}</div>
            <div class="tile-sub">₹{{ number_format($order->ship_charge, 2) }} @if($order->tracking_num)· Track: {{ $order->tracking_num }}@endif</div>
        </div>
        <div class="od-tile tile-orange">
            <div class="tile-label">Order Total</div>
            <div class="tile-value">₹{{ number_format($order->total, 2) }}</div>
            <div class="tile-sub">{{ $order->items->sum('qty') }} item(s) · <span class="badge bg-{{ $order->statusBadgeClass() }}">{{ ucfirst($order->status) }}</span></div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="od-grid">

        {{-- LEFT: Products + Summary --}}
        <div>
            <div class="od-panel mb-3">
                <div class="od-panel-head">
                    <span><i class="fas fa-box-open text-primary"></i> Products Ordered</span>
                    <span class="badge bg-light text-dark">{{ $order->items->count() }} items</span>
                </div>
                @foreach($order->items as $item)
                <div class="od-product-row">
                    @if($item->product?->image)
                    <img src="{{ asset('storage/'.$item->product->image) }}" alt="">
                    @else
                    <div class="no-img"><i class="fas fa-image"></i></div>
                    @endif
                    <div class="p-info">
                        <div class="p-name">{{ $item->product?->name ?? 'Product Removed' }}</div>
                        <div class="p-meta">SKU: {{ $item->product?->sku ?? '-' }} @if($item->gst_pct > 0)· GST {{ $item->gst_pct }}%@endif</div>
                    </div>
                    <div class="p-qty">× {{ $item->qty }}</div>
                    <div class="p-price">₹{{ number_format($item->qty * $item->price, 2) }}</div>
                </div>
                @endforeach
                <div class="od-summary">
                    <div class="sum-row"><span>Subtotal</span><span>₹{{ number_format($order->subtotal, 2) }}</span></div>
                    @if($order->discount > 0)
                    <div class="sum-row"><span>Discount @if($order->coupon)({{ $order->coupon->code }})@endif</span><span class="text-success">-₹{{ number_format($order->discount, 2) }}</span></div>
                    @endif
                    <div class="sum-row"><span>GST</span><span>₹{{ number_format($order->gst_amt, 2) }}</span></div>
                    <div class="sum-row"><span>Shipping</span><span>₹{{ number_format($order->ship_charge, 2) }}</span></div>
                    <div class="sum-row total"><span>Grand Total</span><span>₹{{ number_format($order->total, 2) }}</span></div>
                </div>
            </div>

            {{-- Addresses --}}
            @if($order->bill_addr || $order->ship_addr)
            <div class="row g-3 mb-3">
                @if($order->bill_addr)
                <div class="col-md-6">
                    <div class="od-panel"><div class="od-panel-head"><i class="fas fa-file-invoice"></i> Billing</div>
                    <div class="od-side-body small text-muted">{!! nl2br(e($order->bill_addr)) !!}</div></div>
                </div>
                @endif
                @if($order->ship_addr)
                <div class="col-md-6">
                    <div class="od-panel"><div class="od-panel-head"><i class="fas fa-map-marker-alt"></i> Shipping Address</div>
                    <div class="od-side-body small text-muted">{!! nl2br(e($order->ship_addr)) !!}</div></div>
                </div>
                @endif
            </div>
            @endif

            {{-- Activity Log --}}
            @if($order->statusHistory->count())
            <div class="od-panel">
                <div class="od-panel-head"><i class="fas fa-history"></i> Activity Log</div>
                <div class="od-side-body">
                    @foreach($order->statusHistory as $history)
                    <div class="od-log-item">
                        <div class="od-log-dot"></div>
                        <div>
                            <strong>{{ ucfirst($history->status) }}</strong>
                            <span class="text-muted">· {{ $history->created_at->format('M d, Y h:i A') }}</span>
                            @if($history->note)<br><span class="text-muted">{{ $history->note }}</span>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- RIGHT: Customer + Update --}}
        <div>
            {{-- Customer --}}
            <div class="od-panel od-side-panel">
                <div class="od-panel-head"><i class="fas fa-user"></i> Customer Details</div>
                <div class="od-side-body">
                    @if($order->user)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="od-customer-avatar">{{ strtoupper(substr($order->user->name, 0, 1)) }}</div>
                        <div>
                            <strong>{{ $order->user->name }}</strong><br>
                            <small class="text-muted">Member since {{ $order->user->created_at->format('M Y') }}</small>
                        </div>
                    </div>
                    <div class="od-info-line"><i class="fas fa-envelope"></i> {{ $order->user->email }}</div>
                    <div class="od-info-line"><i class="fas fa-phone"></i> {{ $order->user->phone ?? 'N/A' }}</div>
                    <a href="{{ route('admin.customers.show', $order->user) }}" class="btn btn-sm btn-outline-primary w-100 mt-3 no-print">View Profile</a>
                    @else
                    <p class="text-muted mb-0">Guest order</p>
                    @endif
                </div>
            </div>

            {{-- Order Meta --}}
            <div class="od-panel od-side-panel">
                <div class="od-panel-head"><i class="fas fa-info-circle"></i> Order Info</div>
                <div class="od-side-body small">
                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Order ID</span><span>#{{ $order->id }}</span></div>
                    @if($order->coupon)<div class="d-flex justify-content-between mb-2"><span class="text-muted">Coupon</span><span class="badge bg-success">{{ $order->coupon->code }}</span></div>@endif
                    @if($order->invoice)<div class="d-flex justify-content-between mb-2"><span class="text-muted">Invoice</span><span>{{ $order->invoice->inv_num }}</span></div>@endif
                    @if($order->admin_note)<hr class="my-2"><p class="text-muted mb-1">Admin Note:</p><p class="mb-0">{{ $order->admin_note }}</p>@endif
                </div>
            </div>

            {{-- Update Form --}}
            <div class="od-panel od-side-panel no-print">
                <div class="od-panel-head" style="background:linear-gradient(135deg,#667eea10,#764ba210);">
                    <span style="color:#667eea;"><i class="fas fa-edit"></i> Manage Order</span>
                </div>
                <div class="od-side-body od-update-form">
                    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    @foreach(['pending','processing','packed','shipped','delivered','cancelled','failed','refunded'] as $s)
                                    <option value="{{ $s }}" @selected($order->status===$s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Payment</label>
                                <select name="pay_status" class="form-select form-select-sm">
                                    @foreach(['pending','paid','failed','refunded'] as $s)
                                    <option value="{{ $s }}" @selected($order->pay_status===$s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Tracking Number</label>
                            <input type="text" name="tracking_num" class="form-control form-control-sm" value="{{ old('tracking_num', $order->tracking_num) }}" placeholder="TRACK123456">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Courier</label>
                            <input type="text" name="courier" class="form-control form-control-sm" value="{{ old('courier', $order->courier) }}" placeholder="Delhivery, BlueDart...">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Status Note</label>
                            <input type="text" name="status_note" class="form-control form-control-sm" placeholder="Reason for status change">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Admin Note</label>
                            <textarea name="admin_note" class="form-control form-control-sm" rows="2">{{ old('admin_note', $order->admin_note) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-save w-100"><i class="fas fa-save"></i> Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
