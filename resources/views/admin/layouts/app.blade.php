@php
    $lowStockCount = \App\Models\Product::whereColumn('stock_qty', '<=', 'low_stock_qty')->count();
    $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count();
    $pendingInquiriesCount = \App\Models\Inquiry::where('status', 'pending')->count();
    $totalNotifications = $lowStockCount + $pendingOrdersCount + $pendingInquiriesCount;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - MadhavFood</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --theme-btn-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        body { background: #f8f9fa; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
        .sidebar { background: var(--sidebar-bg); min-height: 100vh; padding: 20px; transition: background 0.3s ease; box-shadow: 2px 0 10px rgba(0,0,0,0.05); }
        .sidebar h5 { color: white; margin-bottom: 25px; font-weight: 700; letter-spacing: 0.5px; }
        .sidebar a { color: rgba(255,255,255,0.8); text-decoration: none; display: block; padding: 10px 15px; border-radius: 6px; transition: all 0.2s ease; margin-bottom: 5px; }
        .sidebar a:hover { background: rgba(255,255,255,0.15); color: white; transform: translateX(3px); }
        .sidebar a.active { background: rgba(255,255,255,0.25); color: white; font-weight: 600; }
        .navbar { border-bottom: 1px solid rgba(0,0,0,0.05); }
        .text-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .card { border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.02), 0 1px 3px rgba(0,0,0,0.05); border-radius: 10px; transition: transform 0.2s ease; }
        .card-header { background: #f8f9fa; border-bottom: 1px solid rgba(0,0,0,0.05); border-top-left-radius: 10px !important; border-top-right-radius: 10px !important; }
        .table { background: white; border-radius: 8px; overflow: hidden; }
        .badge { padding: 6px 12px; font-weight: 600; border-radius: 6px; }
        .main-content { padding: 25px; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <h5><i class="fas fa-store"></i> MadhavFood</h5>
                <a href="{{ route('admin.dashboard') }}" class="@if(request()->routeIs('admin.dashboard')) active @endif">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="{{ route('admin.products.index') }}" class="@if(request()->routeIs('admin.products.*')) active @endif"><i class="fas fa-boxes"></i> Products</a>
                <a href="{{ route('admin.categories.index') }}" class="@if(request()->routeIs('admin.categories.*')) active @endif"><i class="fas fa-sitemap"></i> Categories</a>
                <a href="{{ route('admin.brands.index') }}" class="@if(request()->routeIs('admin.brands.*')) active @endif"><i class="fas fa-copyright"></i> Brands</a>
                <a href="{{ route('admin.attributes.index') }}" class="@if(request()->routeIs('admin.attributes.*')) active @endif"><i class="fas fa-layer-group"></i> Attributes</a>
                <a href="{{ route('admin.inventory.index') }}" class="@if(request()->routeIs('admin.inventory.*')) active @endif"><i class="fas fa-cube"></i> Inventory</a>
                <a href="{{ route('admin.orders.index') }}" class="@if(request()->routeIs('admin.orders.*')) active @endif"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="{{ route('admin.returns.index') }}" class="@if(request()->routeIs('admin.returns.*')) active @endif"><i class="fas fa-undo"></i> Returns</a>
                <a href="{{ route('admin.rto.index') }}" class="@if(request()->routeIs('admin.rto.*')) active @endif"><i class="fas fa-shipping-fast"></i> RTO</a>
                <a href="{{ route('admin.customers.index') }}" class="@if(request()->routeIs('admin.customers.*')) active @endif"><i class="fas fa-users"></i> Customers</a>
                <a href="{{ route('admin.coupons.index') }}" class="@if(request()->routeIs('admin.coupons.*')) active @endif"><i class="fas fa-ticket-alt"></i> Coupons</a>
                <a href="{{ route('admin.taxes.index') }}" class="@if(request()->routeIs('admin.taxes.*')) active @endif"><i class="fas fa-percentage"></i> GST/Tax</a>
                <a href="{{ route('admin.shipping.index') }}" class="@if(request()->routeIs('admin.shipping.*')) active @endif"><i class="fas fa-truck"></i> Shipping</a>
                <a href="{{ route('admin.payments.index') }}" class="@if(request()->routeIs('admin.payments.*')) active @endif"><i class="fas fa-credit-card"></i> Payments</a>
                <a href="{{ route('admin.invoices.index') }}" class="@if(request()->routeIs('admin.invoices.*')) active @endif"><i class="fas fa-file-invoice"></i> Invoices</a>
                <a href="{{ route('admin.inquiries.index') }}" class="@if(request()->routeIs('admin.inquiries.*')) active @endif"><i class="fas fa-comments"></i> Inquiries</a>
                <a href="{{ route('admin.reports.index') }}" class="@if(request()->routeIs('admin.reports.*')) active @endif"><i class="fas fa-chart-bar"></i> Reports</a>
                <a href="{{ route('admin.banners.index') }}" class="@if(request()->routeIs('admin.banners.*')) active @endif"><i class="fas fa-image"></i> Banners</a>
                <a href="{{ route('admin.pages.index') }}" class="@if(request()->routeIs('admin.pages.*')) active @endif"><i class="fas fa-file-alt"></i> Pages</a>
                <a href="{{ route('admin.users.index') }}" class="@if(request()->routeIs('admin.users.*')) active @endif"><i class="fas fa-user-shield"></i> Admin Users</a>
                <a href="{{ route('admin.activity-logs.index') }}" class="@if(request()->routeIs('admin.activity-logs.*')) active @endif"><i class="fas fa-history"></i> Activity Logs</a>
                <a href="{{ route('admin.woocommerce.index') }}" class="@if(request()->routeIs('admin.woocommerce.index')) active @endif"><i class="fab fa-wordpress"></i> WooCommerce</a>
                <a href="{{ route('admin.woocommerce.products.index') }}" class="@if(request()->routeIs('admin.woocommerce.products.*')) active @endif"><i class="fas fa-sync"></i> WC Product Sync</a>
                <a href="{{ route('admin.woocommerce.sync-logs.index') }}" class="@if(request()->routeIs('admin.woocommerce.sync-logs.*')) active @endif"><i class="fas fa-list-alt"></i> WC Sync Logs</a>
                <a href="{{ route('admin.woocommerce.conflicts.index') }}" class="@if(request()->routeIs('admin.woocommerce.conflicts.*')) active @endif"><i class="fas fa-exclamation-triangle"></i> WC Conflicts</a>
                <a href="{{ route('admin.settings.index') }}" class="@if(request()->routeIs('admin.settings.*')) active @endif"><i class="fas fa-cog"></i> Settings</a>
                <hr style="border-color: rgba(255,255,255,0.3); margin: 20px 0;">
                <a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>

            <div class="col-md-10">
                <nav class="navbar navbar-expand-lg navbar-light mb-4 px-3 bg-white rounded shadow-sm">
                    <div class="container-fluid">
                        <span class="navbar-brand fw-bold text-gradient"><i class="fas fa-lock"></i> Admin Panel</span>
                        
                        <div class="d-flex align-items-center ms-auto gap-3">
                            <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-outline-primary d-none d-sm-inline-flex align-items-center gap-1">
                                <i class="fas fa-external-link-alt"></i> View Store
                            </a>

                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="quickAddDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-plus"></i> Quick Add
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="quickAddDropdown">
                                    <li><a class="dropdown-item" href="{{ route('admin.products.create') }}"><i class="fas fa-box me-2 text-primary"></i> Add Product</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.categories.index') }}"><i class="fas fa-sitemap me-2 text-success"></i> Manage Categories</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.coupons.create') }}"><i class="fas fa-ticket-alt me-2 text-warning"></i> Add Coupon</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class="fas fa-cog me-2 text-secondary"></i> System Settings</a></li>
                                </ul>
                            </div>

                            <div class="dropdown">
                                <button class="btn btn-link text-dark position-relative p-1" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration: none;">
                                    <i class="far fa-bell fa-lg"></i>
                                    @if($totalNotifications > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 4px 6px;">
                                            {{ $totalNotifications }}
                                        </span>
                                    @endif
                                </button>
                                <div class="dropdown-menu dropdown-menu-end shadow-lg p-0" aria-labelledby="notificationDropdown" style="width: 320px; border-radius: 8px; border: none;">
                                    <div class="p-3 border-bottom bg-light rounded-top d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-bold">Store Notifications</h6>
                                        <span class="badge bg-primary rounded-pill">{{ $totalNotifications }} Alert(s)</span>
                                    </div>
                                    <div class="list-group list-group-flush" style="max-height: 280px; overflow-y: auto;">
                                        @if($pendingOrdersCount > 0)
                                            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3">
                                                <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                                    <i class="fas fa-shopping-basket"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="small fw-semibold text-dark">Pending Orders</div>
                                                    <div class="text-muted small">You have {{ $pendingOrdersCount }} pending order(s)</div>
                                                </div>
                                            </a>
                                        @endif
                                        @if($lowStockCount > 0)
                                            <a href="{{ route('admin.inventory.index') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3">
                                                <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="small fw-semibold text-dark">Low Stock Alert</div>
                                                    <div class="text-muted small">{{ $lowStockCount }} product(s) are low in stock!</div>
                                                </div>
                                            </a>
                                        @endif
                                        @if($pendingInquiriesCount > 0)
                                            <a href="{{ route('admin.inquiries.index') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3">
                                                <div class="bg-info bg-opacity-10 text-info rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                                    <i class="fas fa-envelope"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="small fw-semibold text-dark">New Inquiries</div>
                                                    <div class="text-muted small">You have {{ $pendingInquiriesCount }} unread inquiry/message(s)</div>
                                                </div>
                                            </a>
                                        @endif
                                        @if($totalNotifications == 0)
                                            <div class="text-center py-4 text-muted">
                                                <i class="far fa-check-circle fa-2x text-success mb-2"></i>
                                                <p class="mb-0 small">All caught up! No notifications</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-2 border-top bg-light text-center rounded-bottom">
                                        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none small fw-semibold text-primary">View Dashboard Overview</a>
                                    </div>
                                </div>
                            </div>

                            <span class="text-muted d-none d-md-inline-block">|</span>

                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 32px; height: 32px; background: linear-gradient(135deg, #667eea, #764ba2) !important;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="text-dark fw-semibold small d-none d-md-inline">{{ auth()->user()->name }}</span>
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="main-content">
                    <!-- Dynamic Breadcrumbs -->
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb bg-white px-3 py-2 rounded shadow-sm d-inline-flex mb-0 align-items-center">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted"><i class="fas fa-home"></i> Dashboard</a></li>
                            @php
                                $routeName = request()->route() ? request()->route()->getName() : '';
                                $segments = explode('.', str_replace('admin.', '', $routeName));
                            @endphp
                            @foreach($segments as $index => $segment)
                                @if($segment != 'dashboard' && $segment != '')
                                    <li class="breadcrumb-item text-capitalize {{ $index == count($segments) - 1 ? 'active text-primary fw-semibold' : 'text-muted' }}">
                                        @if($index < count($segments) - 1 && Route::has('admin.' . $segment . '.index'))
                                            <a href="{{ route('admin.' . $segment . '.index') }}" class="text-decoration-none text-muted">{{ $segment }}</a>
                                        @else
                                            {{ str_replace('-', ' ', $segment) }}
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                    </nav>

                    @include('admin.partials.alert')
                    @yield('content')

                    <!-- System Info Footer -->
                    <footer class="mt-5 pt-3 border-top text-muted d-flex flex-wrap justify-content-between align-items-center" style="font-size: 0.85rem;">
                        <div>
                            &copy; {{ date('Y') }} <strong>MadhavFood Admin</strong>. All rights reserved.
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 d-flex align-items-center gap-1">
                                <span class="rounded-circle d-inline-block bg-success" style="width:6px;height:6px;"></span> Server Online
                            </span>
                            <span class="text-muted d-flex align-items-center gap-1">
                                <i class="far fa-clock"></i> <span id="serverClock">{{ date('h:i:s A | d-M-Y') }}</span>
                            </span>
                            <span class="badge bg-light text-dark">v1.2.0-Woo</span>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </div>

    <!-- Theme Customizer Trigger Button -->
    <button class="btn btn-primary rounded-circle shadow-lg position-fixed d-flex align-items-center justify-content-center" id="themeCustomizerBtn" style="bottom: 20px; right: 20px; width: 50px; height: 50px; z-index: 1050; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
        <i class="fas fa-cog fa-spin fa-lg text-white"></i>
    </button>

    <!-- Theme Customizer Panel -->
    <div class="card shadow-lg position-fixed border-0 d-none" id="themeCustomizerPanel" style="bottom: 80px; right: 20px; width: 260px; z-index: 1050; border-radius: 12px;">
        <div class="card-header text-white d-flex justify-content-between align-items-center py-2" style="background: var(--sidebar-bg) !important; border-top-left-radius: 12px; border-top-right-radius: 12px;">
            <h6 class="mb-0 fw-bold"><i class="fas fa-palette me-2"></i>Sidebar Theme</h6>
            <button type="button" class="btn-close btn-close-white btn-sm" id="closeCustomizer"></button>
        </div>
        <div class="card-body p-3">
            <div class="d-grid gap-2">
                <button class="btn btn-outline-primary text-start d-flex align-items-center gap-2 py-2" onclick="setSidebarTheme('indigo')">
                    <span class="rounded-circle d-inline-block" style="width: 18px; height: 18px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></span>
                    Royal Indigo
                </button>
                <button class="btn btn-outline-secondary text-start d-flex align-items-center gap-2 py-2" onclick="setSidebarTheme('slate')">
                    <span class="rounded-circle d-inline-block" style="width: 18px; height: 18px; background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);"></span>
                    Midnight Slate
                </button>
                <button class="btn btn-outline-success text-start d-flex align-items-center gap-2 py-2" onclick="setSidebarTheme('emerald')">
                    <span class="rounded-circle d-inline-block" style="width: 18px; height: 18px; background: linear-gradient(135deg, #059669 0%, #064e3b 100%);"></span>
                    Forest Emerald
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Theme Customizer Logic
        const themeBtn = document.getElementById('themeCustomizerBtn');
        const themePanel = document.getElementById('themeCustomizerPanel');
        const closeBtn = document.getElementById('closeCustomizer');

        if(themeBtn && themePanel) {
            themeBtn.addEventListener('click', () => {
                themePanel.classList.toggle('d-none');
            });
            closeBtn.addEventListener('click', () => {
                themePanel.classList.add('d-none');
            });
        }

        function setSidebarTheme(themeName) {
            const root = document.documentElement;
            let bgValue = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            let primaryBtnBg = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            
            if (themeName === 'slate') {
                bgValue = 'linear-gradient(135deg, #2d3748 0%, #1a202c 100%)';
                primaryBtnBg = 'linear-gradient(135deg, #2d3748 0%, #1a202c 100%)';
            } else if (themeName === 'emerald') {
                bgValue = 'linear-gradient(135deg, #059669 0%, #064e3b 100%)';
                primaryBtnBg = 'linear-gradient(135deg, #059669 0%, #064e3b 100%)';
            }
            
            root.style.setProperty('--sidebar-bg', bgValue);
            root.style.setProperty('--theme-btn-bg', primaryBtnBg);
            localStorage.setItem('admin-sidebar-theme', themeName);
        }

        // Apply saved theme and clock on load
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('admin-sidebar-theme') || 'indigo';
            setSidebarTheme(savedTheme);
            
            // Live Server Clock
            const clockEl = document.getElementById('serverClock');
            if (clockEl) {
                setInterval(() => {
                    const now = new Date();
                    clockEl.innerText = now.toLocaleTimeString() + ' | ' + now.toLocaleDateString();
                }, 1000);
            }
        });
    </script>
</body>
</html>
