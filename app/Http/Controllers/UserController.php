<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'branch', 'department'])->latest()->paginate(15);
        $roles = Role::all();
        $branches = Branch::all();
        $departments = Department::all();

        return view('users.index', compact('users', 'roles', 'branches', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:100',
            'employee_code' => 'nullable|string|max:50',
            'branch_id' => 'nullable|exists:branches,id',
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'required|exists:roles,name',
        ]);

        $companyId = session('current_company_id') ?? auth()->user()->company_id;

        $user = User::create([
            'company_id' => $companyId,
            'branch_id' => $validated['branch_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'designation' => $validated['designation'] ?? null,
            'employee_code' => $validated['employee_code'] ?? null,
            'status' => 'active',
        ]);

        $user->assignRole($validated['role']);

        if (!empty($validated['branch_id'])) {
            $user->branches()->sync([$validated['branch_id'] => ['is_default' => true]]);
        }

        ActivityLog::log('create', 'users', "Created new user: {$user->name} ({$user->email})", $user);

        return back()->with('success', "User {$user->name} created successfully.");
    }

    public function toggleStatus(User $user)
    {
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        ActivityLog::log('update', 'users', "Changed status of user {$user->name} to {$newStatus}", $user);

        return back()->with('success', "User status updated to {$newStatus}.");
    }
}
