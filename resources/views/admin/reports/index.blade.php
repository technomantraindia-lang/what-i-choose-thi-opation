@extends('admin.layouts.app')

@section('title', 'Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Sales Reports & Analytics</h2>
        <p class="text-muted mb-0">Complete overview of your MadhavFood store performance</p>
    </div>
    <span class="badge bg-primary fs-6">{{ now()->format('F d, Y') }}</span>
</div>

{{-- Revenue Stats --}}
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body">
                <h6 class="opacity-75 mb-2"><i class="fas fa-rupee-sign"></i> Total Revenue</h6>
                <h2 class="mb-0">₹{{ number_format($stats['total_revenue'], 0) }}</h2>
                <small class="opacity-75">All time paid orders</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-0" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
            <div class="card-body">
                <h6 class="opacity-75 mb-2"><i class="fas fa-calendar-alt"></i> This Month</h6>
                <h2 class="mb-0">₹{{ number_format($stats['month_revenue'], 0) }}</h2>
                <small class="opacity-75">{{ $stats['month_orders'] }} orders this month</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-0" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <div class="card-body">
                <h6 class="opacity-75 mb-2"><i class="fas fa-sun"></i> Today</h6>
                <h2 class="mb-0">₹{{ number_format($stats['today_revenue'], 0) }}</h2>
                <small class="opacity-75">{{ $stats['today_orders'] }} orders today</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-0" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <div class="card-body">
                <h6 class="opacity-75 mb-2"><i class="fas fa-shopping-bag"></i> Avg Order Value</h6>
                <h2 class="mb-0">₹{{ number_format($stats['avg_order_value'], 0) }}</h2>
                <small class="opacity-75">{{ $stats['total_orders'] }} total orders</small>
            </div>
        </div>
    </div>
</div>

{{-- Quick Stats Row --}}
<div class="row mb-4">
    <div class="col-md-2 mb-3"><div class="card text-center"><div class="card-body py-3"><h4 class="text-primary mb-0">{{ $stats['total_customers'] }}</h4><small class="text-muted">Customers</small></div></div></div>
    <div class="col-md-2 mb-3"><div class="card text-center"><div class="card-body py-3"><h4 class="text-success mb-0">{{ $stats['total_products'] }}</h4><small class="text-muted">Products</small></div></div></div>
    <div class="col-md-2 mb-3"><div class="card text-center"><div class="card-body py-3"><h4 class="text-warning mb-0">{{ $stats['pending_orders'] }}</h4><small class="text-muted">Pending</small></div></div></div>
    <div class="col-md-2 mb-3"><div class="card text-center"><div class="card-body py-3"><h4 class="text-info mb-0">{{ $stats['delivered_orders'] }}</h4><small class="text-muted">Delivered</small></div></div></div>
    <div class="col-md-2 mb-3"><div class="card text-center"><div class="card-body py-3"><h4 class="text-danger mb-0">{{ $stats['low_stock_products'] }}</h4><small class="text-muted">Low Stock</small></div></div></div>
    <div class="col-md-2 mb-3"><div class="card text-center"><div class="card-body py-3"><h4 class="text-secondary mb-0">{{ $stats['pending_inquiries'] }}</h4><small class="text-muted">Inquiries</small></div></div></div>
</div>

{{-- Charts Row --}}
<div class="row mb-4">
    <div class="col-md-8 mb-3">
        <div class="card h-100">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="fas fa-chart-line text-primary"></i> Monthly Revenue (Last 6 Months)</h5></div>
            <div class="card-body">
                <canvas id="monthlyChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="fas fa-chart-pie text-success"></i> Orders by Status</h5></div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="fas fa-chart-bar text-info"></i> Daily Sales (Last 7 Days)</h5></div>
            <div class="card-body">
                <canvas id="dailyChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="fas fa-credit-card text-warning"></i> Payments by Method</h5></div>
            <div class="card-body">
                @forelse($paymentsByMethod as $payment)
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded" style="background: #f8f9fa;">
                    <div>
                        <strong>{{ strtoupper(str_replace('_', ' ', $payment->method)) }}</strong>
                        <br><small class="text-muted">{{ $payment->count }} transactions</small>
                    </div>
                    <span class="badge bg-primary fs-6">₹{{ number_format($payment->total, 0) }}</span>
                </div>
                @empty
                <p class="text-muted text-center">No payment data yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Top Products & Category Sales --}}
<div class="row mb-4">
    <div class="col-md-7 mb-3">
        <div class="card">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="fas fa-trophy text-warning"></i> Top Selling Products</h5></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Product</th><th>Category</th><th>Qty Sold</th><th>Revenue</th></tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $i => $item)
                        <tr>
                            <td><span class="badge bg-{{ $i < 3 ? 'warning' : 'secondary' }}">{{ $i + 1 }}</span></td>
                            <td><strong>{{ $item->product?->name ?? 'Unknown' }}</strong></td>
                            <td>{{ $item->product?->category?->name ?? '-' }}</td>
                            <td>{{ $item->total_qty }}</td>
                            <td><strong>₹{{ number_format($item->revenue, 2) }}</strong></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No sales data yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-5 mb-3">
        <div class="card">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="fas fa-sitemap text-primary"></i> Sales by Category</h5></div>
            <div class="card-body">
                @forelse($categorySales as $cat)
                @php $maxRev = $categorySales->max('revenue') ?: 1; $pct = ($cat->revenue / $maxRev) * 100; @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ $cat->name }}</span>
                        <strong>₹{{ number_format($cat->revenue, 0) }}</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar" style="width: {{ $pct }}%; background: linear-gradient(90deg, #667eea, #764ba2);"></div>
                    </div>
                    <small class="text-muted">{{ $cat->total_qty }} items sold</small>
                </div>
                @empty
                <p class="text-muted text-center">No category sales data</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Recent Orders --}}
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list text-secondary"></i> Recent Orders</h5>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">View All Orders</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th>Payment</th><th>Date</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                <tr>
                    <td><strong>{{ $order->order_num }}</strong></td>
                    <td>{{ $order->user?->name ?? 'N/A' }}</td>
                    <td>₹{{ number_format($order->total, 2) }}</td>
                    <td><span class="badge bg-info">{{ ucfirst($order->status) }}</span></td>
                    <td><span class="badge bg-{{ $order->pay_status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($order->pay_status) }}</span></td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No orders yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const monthlyData = @json($monthlySales);
const statusData = @json($ordersByStatus);
const dailyData = @json($dailySales);

new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: monthlyData.map(d => d.label),
        datasets: [{
            label: 'Revenue (₹)',
            data: monthlyData.map(d => d.total),
            backgroundColor: 'rgba(102, 126, 234, 0.7)',
            borderColor: 'rgba(102, 126, 234, 1)',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

const statusColors = ['#ffc107','#17a2b8','#28a745','#007bff','#6f42c1','#dc3545','#fd7e14','#20c997'];
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1)),
        datasets: [{ data: Object.values(statusData), backgroundColor: statusColors }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('dailyChart'), {
    type: 'line',
    data: {
        labels: dailyData.map(d => d.label),
        datasets: [{
            label: 'Revenue (₹)',
            data: dailyData.map(d => d.total),
            borderColor: 'rgba(17, 153, 142, 1)',
            backgroundColor: 'rgba(17, 153, 142, 0.1)',
            fill: true,
            tension: 0.4,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
@endsection
