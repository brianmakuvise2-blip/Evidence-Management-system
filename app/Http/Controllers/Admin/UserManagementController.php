<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Institution;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /**
     * Display a listing of all users
     */
    public function index(Request $request)
{
    $query = User::with(['institution', 'department', 'roles']);
    
    // Apply filters
    if ($request->has('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('employee_id', 'like', "%{$search}%");
        });
    }
    
    if ($request->has('institution')) {
        $query->where('institution_id', $request->institution);
    }
    
    if ($request->has('status')) {
        $query->where('account_status', $request->status);
    }
    
    if ($request->has('role')) {
        $query->role($request->role);
    }
    
    $users = $query->orderBy('name')->paginate(15);
    
    // Get stats for cards
    $stats = [
        'total' => User::count(),
        'active' => User::where('account_status', 'active')->count(),
        'inactive' => User::where('account_status', 'inactive')->count(),
        'suspended' => User::where('account_status', 'suspended')->count(),
        'archived' => User::where('account_status', 'archived')->count(),
    ];
    
    $institutions = Institution::where('is_active', true)->get();
    $roles = Role::all();
    
    return view('admin.users.index', compact('users', 'stats', 'institutions', 'roles'));
}

    /**
     * Show form for creating new user
     */
    public function create()
    {
        $institutions = Institution::where('is_active', true)->get();
        $roles = Role::all();
        
        return view('admin.users.create', compact('institutions', 'roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        // Custom password validation with complexity rules
        $passwordErrorMessage = 'password requires uppercase, number, special character (@$!%*?&) and minimum 8 characters';
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                function ($attribute, $value, $fail) use ($passwordErrorMessage) {
                    if (!User::validatePasswordComplexity($value)) {
                        $fail($passwordErrorMessage);
                    }
                },
            ],
            'employee_id' => 'nullable|string|unique:users,employee_id',
            'badge_number' => 'nullable|string|unique:users,badge_number',
            'institution_id' => 'required|exists:institutions,id',
            'department_id' => 'required|exists:departments,id',
            'job_title' => 'required|string|max:255',
            'phone_work' => 'nullable|string|max:20',
            'phone_mobile' => 'nullable|string|max:20',
            'data_access_scope' => 'required|in:personal,department,all',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        try {
            $user = DB::transaction(function () use ($request) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'employee_id' => $request->employee_id,
                    'badge_number' => $request->badge_number,
                    'institution_id' => $request->institution_id,
                    'department_id' => $request->department_id,
                    'job_title' => $request->job_title,
                    'phone_work' => $request->phone_work,
                    'phone_mobile' => $request->phone_mobile,
                    'data_access_scope' => $request->data_access_scope,
                    'account_status' => 'active',
                    'password_changed_at' => now(),
                    'last_password_change_at' => now(),
                    'password_expires_at' => now()->addDays(90), // Password expires in 90 days
                ]);

                $user->syncRoles($request->roles);

                return $user;
            });

            // Audit logging should not block the user creation if it fails.
            try {
                $user->logActivity('user_created', 'success', [
                    'created_by' => auth()->id()
                ]);
            } catch (\Throwable $logException) {
                Log::warning('Failed to write user_created activity log', [
                    'user_id' => $user->id,
                    'exception' => $logException->getMessage(),
                ]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully.');
                
        } catch (\Exception $e) {
            Log::error('User creation failed', [
                'exception' => $e->getMessage(),
                'request' => $request->except('password', 'password_confirmation'),
            ]);

            return back()->withInput()
                ->with('error', 'Failed to create user. Please try again.');
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load(['institution', 'department', 'roles', 'activityLogs' => function($query) {
            $query->latest()->limit(50);
        }]);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show form for editing user
     */
    public function edit(User $user)
    {
        $institutions = Institution::where('is_active', true)->get();
        $departments = Department::where('institution_id', $user->institution_id)
            ->where('is_active', true)
            ->get();
        $roles = Role::all();
        
        return view('admin.users.edit', compact('user', 'institutions', 'departments', 'roles'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'employee_id' => 'nullable|string|unique:users,employee_id,' . $user->id,
            'badge_number' => 'nullable|string|unique:users,badge_number,' . $user->id,
            'institution_id' => 'required|exists:institutions,id',
            'department_id' => 'required|exists:departments,id',
            'job_title' => 'required|string|max:255',
            'phone_work' => 'nullable|string|max:20',
            'phone_mobile' => 'nullable|string|max:20',
            'data_access_scope' => 'required|in:personal,department,all',
            'account_status' => 'required|in:active,inactive,suspended',
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,id',
        ]);

        try {
            $user->update($request->except('roles'));

            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            }

            $user->logActivity('user_updated', 'success', [
                'updated_by' => auth()->id()
            ]);

            return redirect()->route('admin.users.show', $user)
                ->with('success', 'User updated successfully.');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update user. Please try again.');
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user->password = Hash::make($request->password);
            $user->password_changed_at = now();
            $user->save();

            $user->logActivity('password_reset', 'success', [
                'reset_by' => auth()->id()
            ]);

            return redirect()->back()
                ->with('success', 'Password reset successfully.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reset password.');
        }
    }

    /**
     * Archive a user
     */
    public function archive(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            $user->account_status = 'archived';
            $user->archived_at = now();
            $user->archived_by = auth()->id();
            $user->suspension_reason = $request->reason;
            $user->save();

            $user->logActivity('user_archived', 'success', [
                'archived_by' => auth()->id(),
                'reason' => $request->reason
            ]);

            return redirect()->back()
                ->with('success', 'User archived successfully.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to archive user.');
        }
    }

    /**
     * Reactivate an archived user
     */
    public function reactivate(User $user)
    {
        try {
            $user->account_status = 'active';
            $user->archived_at = null;
            $user->archived_by = null;
            $user->suspension_reason = null;
            $user->save();

            $user->logActivity('user_reactivated', 'success', [
                'reactivated_by' => auth()->id()
            ]);

            return redirect()->back()
                ->with('success', 'User reactivated successfully.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reactivate user.');
        }
    }

    /**
     * Bulk update user status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'status' => 'required|in:active,inactive,suspended,archived'
        ]);

        try {
            $count = User::whereIn('id', $request->user_ids)
                ->where('id', '!=', auth()->id()) // Don't update yourself
                ->update(['account_status' => $request->status]);

            auth()->user()->logActivity('bulk_status_update', 'success', [
                'user_ids' => $request->user_ids,
                'new_status' => $request->status,
                'count' => $count
            ]);

            return response()->json([
                'success' => true,
                'message' => "{$count} users updated successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update users'
            ], 500);
        }
    }

    /**
     * Get departments for AJAX
     */
    public function getDepartments(Request $request)
    {
        $departments = Department::where('institution_id', $request->institution_id)
            ->where('is_active', true)
            ->get(['id', 'name']);
            
        return response()->json([
            'success' => true,
            'data' => $departments
        ]);
    }
}