@extends('admin.layouts.app')

@section('title', 'Admin Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold m-0"><i class="fas fa-user-shield me-2 text-primary"></i>Admin Users</h3>
    @if(auth()->user()->hasPermission('users.manage'))
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Admin User
        </a>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary flex-grow-1"><i class="fas fa-search me-1"></i> Filter</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="fas fa-undo me-1"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>#{{ $user->id }}</td>
                            <td class="fw-semibold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $user->isSuperAdmin() ? 'bg-danger' : 'bg-primary' }}">
                                    {{ $user->role?->name ?? 'No Role' }}
                                </span>
                            </td>
                            <td>
                                @if($user->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($user->status === 'suspended')
                                    <span class="badge bg-danger">Suspended</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at?->format('M d, Y') }}</td>
                            <td class="text-end">
                                @if(auth()->user()->hasPermission('users.manage'))
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.users.toggleStatus', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $user->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                            <i class="fas {{ $user->status === 'active' ? 'fa-ban' : 'fa-check' }}"></i>
                                            {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No admin users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
