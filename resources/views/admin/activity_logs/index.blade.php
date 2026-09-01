@extends('admin.layouts.app')

@section('title', 'Activity Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold m-0"><i class="fas fa-history me-2 text-primary"></i>Activity / Audit Logs</h3>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Description, IP..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">User</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Module</label>
                <select name="module" class="form-select form-select-sm">
                    <option value="">All Modules</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" @selected(request('module') === $mod)>{{ ucfirst($mod) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-1 d-flex align-items-end gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-filter"></i></button>
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-undo"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-sm">
                <thead class="table-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="text-nowrap" style="font-size: 0.85rem;">{{ $log->created_at?->format('M d, Y H:i:s') }}</td>
                            <td>
                                @if($log->user)
                                    <span class="fw-semibold">{{ $log->user->name }}</span>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $log->user->email }}</div>
                                @else
                                    <span class="text-muted">System / Guest</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ strtoupper($log->action) }}</span>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ ucfirst($log->module) }}</span></td>
                            <td style="max-width: 300px;" class="text-truncate" title="{{ $log->description }}">
                                {{ $log->description ?? 'N/A' }}
                            </td>
                            <td><code>{{ $log->ip_address ?? '127.0.0.1' }}</code></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No activity logs recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
