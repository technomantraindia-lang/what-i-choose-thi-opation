<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role')
            ->whereHas('role', function ($q) {
                $q->whereIn('name', ['Super Admin', 'Admin']);
            })
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $authUser = Auth::user();
        $rolesQuery = Role::whereIn('name', ['Super Admin', 'Admin']);
        if (! $authUser->isSuperAdmin()) {
            $rolesQuery->where('name', '!=', 'Super Admin');
        }
        $roles = $rolesQuery->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $authUser = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $targetRole = Role::findOrFail($validated['role_id']);

        if ($targetRole->name === 'Super Admin' && ! $authUser->isSuperAdmin()) {
            return back()->withInput()->with('error', 'Only Super Admin can assign the Super Admin role.');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role_id' => $validated['role_id'],
            'status' => $validated['status'],
            'password' => Hash::make($validated['password']),
        ]);

        ActivityLogService::log(
            'create',
            'users',
            "Created admin user {$user->name} ({$user->email})",
            $user,
            null,
            $user->only(['name', 'email', 'phone', 'role_id', 'status'])
        );

        return redirect()->route('admin.users.index')->with('success', 'Admin user created successfully.');
    }

    public function edit(User $user)
    {
        $authUser = Auth::user();

        // Prevent normal admin from editing a super admin
        if ($user->isSuperAdmin() && ! $authUser->isSuperAdmin()) {
            return redirect()->route('admin.users.index')->with('error', 'Only Super Admin can edit Super Admin users.');
        }

        $rolesQuery = Role::whereIn('name', ['Super Admin', 'Admin']);
        if (! $authUser->isSuperAdmin()) {
            $rolesQuery->where('name', '!=', 'Super Admin');
        }
        $roles = $rolesQuery->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $authUser = Auth::user();

        if ($user->isSuperAdmin() && ! $authUser->isSuperAdmin()) {
            return back()->withInput()->with('error', 'Only Super Admin can edit Super Admin users.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $targetRole = Role::findOrFail($validated['role_id']);

        if ($targetRole->name === 'Super Admin' && ! $authUser->isSuperAdmin()) {
            return back()->withInput()->with('error', 'Only Super Admin can assign the Super Admin role.');
        }

        // Prevent regular Admin from deactivating their own account
        if ($authUser->id === $user->id && $validated['status'] !== 'active' && ! $authUser->isSuperAdmin()) {
            return back()->withInput()->with('error', 'You cannot deactivate your own admin account.');
        }

        // Prevent disabling the only active Super Admin account
        if ($user->isSuperAdmin() && ($validated['status'] !== 'active' || $targetRole->name !== 'Super Admin')) {
            $activeSuperAdminsCount = User::whereHas('role', fn ($q) => $q->where('name', 'Super Admin'))
                ->where('status', 'active')
                ->where('id', '!=', $user->id)
                ->count();

            if ($activeSuperAdminsCount === 0) {
                return back()->withInput()->with('error', 'Cannot disable or reassign role for the only active Super Admin.');
            }
        }

        $oldValues = $user->only(['name', 'email', 'phone', 'role_id', 'status']);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->role_id = $validated['role_id'];
        $user->status = $validated['status'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        ActivityLogService::log(
            'update',
            'users',
            "Updated admin user {$user->name} ({$user->email})",
            $user,
            $oldValues,
            $user->only(['name', 'email', 'phone', 'role_id', 'status'])
        );

        return redirect()->route('admin.users.index')->with('success', 'Admin user updated successfully.');
    }

    public function toggleStatus(User $user)
    {
        $authUser = Auth::user();

        if ($authUser->id === $user->id && $user->status === 'active' && ! $authUser->isSuperAdmin()) {
            return back()->with('error', 'You cannot deactivate your own admin account.');
        }

        if ($user->isSuperAdmin() && ! $authUser->isSuperAdmin()) {
            return back()->with('error', 'Only Super Admin can modify Super Admin accounts.');
        }

        if ($user->isSuperAdmin() && $user->status === 'active') {
            $activeSuperAdminsCount = User::whereHas('role', fn ($q) => $q->where('name', 'Super Admin'))
                ->where('status', 'active')
                ->where('id', '!=', $user->id)
                ->count();

            if ($activeSuperAdminsCount === 0) {
                return back()->with('error', 'Cannot deactivate the only active Super Admin.');
            }
        }

        $oldStatus = $user->status;
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        ActivityLogService::log(
            'status_change',
            'users',
            "Toggled status for {$user->email} from {$oldStatus} to {$user->status}",
            $user,
            ['status' => $oldStatus],
            ['status' => $user->status]
        );

        return back()->with('success', 'User status updated successfully.');
    }
}
