<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleManagementController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();
        $institutions = Institution::where('is_active', true)->orderBy('name')->get();

        $rolesByInstitution = $this->groupRolesByInstitution($roles, $institutions);

        $userCounts = User::selectRaw('id')
            ->with('roles')
            ->get()
            ->flatMap(fn($u) => $u->roles->pluck('id'))
            ->countBy()
            ->toArray();

        return view('admin.roles.index', compact(
            'roles', 'permissions', 'institutions', 'rolesByInstitution', 'userCounts'
        ));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        $institutions = Institution::where('is_active', true)->orderBy('name')->get();

        return view('admin.roles.create', compact('permissions', 'institutions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'institution_id' => 'nullable|exists:institutions,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $roleName = $validated['name'];
        if ($validated['institution_id']) {
            $institution = Institution::find($validated['institution_id']);
            $prefix = strtolower(str_replace([' ', "'"], ['-', ''], $institution->name));
            if (!str_starts_with($roleName, $prefix)) {
                $roleName = $prefix . '-' . $roleName;
            }
        }

        $roleName = preg_replace('/[^a-z0-9\-]/', '-', strtolower($roleName));
        $roleName = trim(preg_replace('/-+/', '-', $roleName), '-');

        if (Role::where('name', $roleName)->exists()) {
            return back()->withInput()->with('error', "Role '{$roleName}' already exists.");
        }

        $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        auth()->user()->logActivity('role_created', 'success', [
            'role_name' => $roleName,
            'permissions' => $validated['permissions'] ?? [],
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$roleName}' created successfully.");
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $institutions = Institution::where('is_active', true)->orderBy('name')->get();
        $role->load('permissions');

        return view('admin.roles.edit', compact('role', 'permissions', 'institutions'));
    }

    public function update(Request $request, Role $role)
    {
        $protectedRoles = ['super-admin', 'system-administrator', 'administrator'];
        if (in_array($role->name, $protectedRoles)) {
            return back()->with('error', 'Core system roles cannot be modified.');
        }

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        auth()->user()->logActivity('role_updated', 'success', [
            'role_name' => $role->name,
            'permissions' => $validated['permissions'] ?? [],
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' permissions updated.");
    }

    public function destroy(Role $role)
    {
        $protectedRoles = ['super-admin', 'system-administrator', 'administrator', 'source-officer', 'evidence-officer'];
        if (in_array($role->name, $protectedRoles)) {
            return back()->with('error', 'This role is a core system role and cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', "Cannot delete role '{$role->name}' — it is assigned to {$role->users()->count()} user(s). Reassign users first.");
        }

        auth()->user()->logActivity('role_deleted', 'warning', ['role_name' => $role->name]);

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' deleted.");
    }

    private function groupRolesByInstitution($roles, $institutions): array
    {
        $institutionPrefixes = [];
        foreach ($institutions as $inst) {
            $prefix = strtolower(str_replace([' ', "'"], ['-', ''], $inst->name));
            $institutionPrefixes[$prefix] = $inst;
        }

        $grouped = ['System-Wide' => []];
        foreach ($institutions as $inst) {
            $grouped[$inst->name] = [];
        }

        foreach ($roles as $role) {
            $matched = false;
            foreach ($institutionPrefixes as $prefix => $inst) {
                if (str_starts_with($role->name, $prefix)) {
                    $grouped[$inst->name][] = $role;
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                $grouped['System-Wide'][] = $role;
            }
        }

        return array_filter($grouped, fn($r) => count($r) > 0);
    }
}
