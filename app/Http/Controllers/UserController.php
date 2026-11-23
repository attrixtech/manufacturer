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
            'is_active' => true,
        ]);

        $user->assignRole($validated['role']);

        ActivityLog::log('create', 'rbac', "Created user: {$user->name} with role: {$validated['role']}");

        return back()->with('success', "Staff member {$user->name} created and role assigned.");
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:50',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        ActivityLog::log('create', 'rbac', "Created security role: {$role->name}");

        return back()->with('success', "Security role {$role->name} created successfully.");
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot deactivate your own account.']);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $action = $user->is_active ? 'activated' : 'deactivated';
        ActivityLog::log('toggle_status', 'rbac', "User {$user->name} has been {$action}.");

        return back()->with('success', "User account {$user->name} has been {$action}.");
    }
}
