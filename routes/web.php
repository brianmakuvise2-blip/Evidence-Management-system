<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\TransferRequestController;
use App\Http\Controllers\CourtBundleController;
use App\Http\Controllers\ReportsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public route
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // Password reset routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});
    
// Protected routes (require login)
Route::middleware(['auth'])->group(function () {
    // Dashboard - Accessible to all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // =========== PROFILE ROUTES ===========
    // Accessible to all authenticated users
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::get('/change-password', [ProfileController::class, 'editPassword'])->name('edit-password');
        Route::put('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');
    });
    
    // =========== EVIDENCE COLLECTION ROUTES ===========
    // Index - accessible to all authenticated users
    Route::get('/evidence', [EvidenceController::class, 'index'])->name('evidence.index');
    
    // Create and Store - restricted to source officers and administrators
    Route::middleware(['role:source-officer,administrator,system-administrator'])->group(function () {
        Route::get('/evidence/create', [EvidenceController::class, 'create'])->name('evidence.create');
        Route::post('/evidence', [EvidenceController::class, 'store'])->name('evidence.store');
    });
    
    // Show and Download - accessible to all authenticated users
    Route::get('/evidence/{evidence}', [EvidenceController::class, 'show'])->name('evidence.show');
    Route::get('/evidence/{evidence}/view', [EvidenceController::class, 'view'])->name('evidence.view');
    Route::get('/evidence/{evidence}/file', [EvidenceController::class, 'previewFile'])->name('evidence.file');
    Route::get('/evidence/{evidence}/download', [EvidenceController::class, 'download'])->name('evidence.download');
    
    // Edit and Update - restricted to evidence officers and administrators
    Route::middleware(['role:evidence-officer,administrator,system-administrator'])->group(function () {
        Route::get('/evidence/{evidence}/edit', [EvidenceController::class, 'edit'])->name('evidence.edit');
        Route::put('/evidence/{evidence}', [EvidenceController::class, 'update'])->name('evidence.update');
    });
    
    // Verify and Archive - restricted to administrators and system administrators
    Route::middleware(['role:administrator,system-administrator'])->group(function () {
        Route::post('/evidence/{evidence}/verify', [EvidenceController::class, 'verify'])->name('evidence.verify');
        Route::patch('/evidence/{evidence}/archive', [EvidenceController::class, 'archive'])->name('evidence.archive');
    });
    
    // Delete - restricted to system administrators only
    Route::middleware(['role:system-administrator'])->group(function () {
        Route::delete('/evidence/{evidence}', [EvidenceController::class, 'destroy'])->name('evidence.destroy');
    });
    
    // =========== TRANSFER REQUEST ROUTES ===========
    // Request transfer - requires 'request-transfer' permission
    Route::middleware(['permission:request-transfer'])->group(function () {
        Route::get('/transfers/create', [TransferRequestController::class, 'create'])->name('transfers.create');
        Route::post('/transfers', [TransferRequestController::class, 'store'])->name('transfers.store');
    });
    
    // View transfer requests - accessible to authorized users
    Route::get('/transfers', [TransferRequestController::class, 'index'])->name('transfers.index');
    Route::get('/transfers/{transfer}', [TransferRequestController::class, 'show'])->name('transfers.show');
    
    // Approve/Reject transfers - requires 'approve-transfer' permission
    Route::middleware(['permission:approve-transfer'])->group(function () {
        Route::get('/transfers/{transfer}/approve', [TransferRequestController::class, 'approvalForm'])->name('transfers.approval-form');
        Route::post('/transfers/{transfer}/approve', [TransferRequestController::class, 'approve'])->name('transfers.approve');
        Route::get('/transfers/{transfer}/reject', [TransferRequestController::class, 'rejectionForm'])->name('transfers.rejection-form');
        Route::post('/transfers/{transfer}/reject', [TransferRequestController::class, 'reject'])->name('transfers.reject');
    });
    
    // Acknowledge receipt - requires 'acknowledge-receipt' permission
    Route::middleware(['permission:acknowledge-receipt'])->group(function () {
        Route::get('/transfers/{transfer}/acknowledge', [TransferRequestController::class, 'acknowledgmentForm'])->name('transfers.acknowledgment-form');
        Route::post('/transfers/{transfer}/acknowledge', [TransferRequestController::class, 'acknowledgeReceipt'])->name('transfers.acknowledge');
    });
    
    // View custody history - accessible to all authenticated users
    Route::get('/evidence/{evidence}/custody-history', [TransferRequestController::class, 'custodyHistory'])->name('custody-history');
    
    // Export custody history - requires 'disclose-evidence' permission
    Route::middleware(['permission:disclose-evidence'])->get('/evidence/{evidence}/custody-history/export', [TransferRequestController::class, 'exportCustodyHistory'])->name('custody-history-export');

    // =========== COURT BUNDLE ROUTES ==========
    Route::middleware(['permission:view-bundle'])->group(function () {
        Route::get('/bundles', [CourtBundleController::class, 'index'])->name('bundles.index');
        Route::get('/bundles/{bundle}', [CourtBundleController::class, 'show'])->name('bundles.show');
        Route::get('/bundles/{bundle}/export', [CourtBundleController::class, 'export'])->name('bundles.export');
    });

    Route::middleware(['permission:prepare-bundle'])->group(function () {
        Route::get('/bundles/lp/create', [CourtBundleController::class, 'create'])->name('bundles.create');
        Route::post('/bundles', [CourtBundleController::class, 'store'])->name('bundles.store');
        Route::post('/bundles/{bundle}/approve', [CourtBundleController::class, 'approve'])->name('bundles.approve');
    });

    // =========== REPORTS & ANALYTICS ROUTES ==========
    Route::middleware(['permission:view-reports'])->group(function () {
        Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportsController::class, 'export'])->name('reports.export');
    });
    
    // =========== NOTIFICATIONS ROUTES ===========
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{notification}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::delete('/notifications/{notification}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    
    // =========== USER MANAGEMENT ROUTES ===========
    // Only accessible to administrators and system-administrators
    Route::middleware(['role:administrator,system-administrator'])->prefix('admin')->name('admin.')->group(function () {
        
        // List all users (GET /admin/users)
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        
        // Show create form (GET /admin/users/create)
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        
        // Store new user (POST /admin/users)
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        
        // Show single user (GET /admin/users/{user})
        Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
        
        // Show edit form (GET /admin/users/{user}/edit)
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        
        // Update user (PUT /admin/users/{user})
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        
        // Delete user (DELETE /admin/users/{user})
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        
        // =========== ADDITIONAL USER ACTIONS ===========
        
        // Reset password (POST /admin/users/{user}/reset-password)
        Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
        
        // Archive user (POST /admin/users/{user}/archive)
        Route::post('/users/{user}/archive', [UserManagementController::class, 'archive'])->name('users.archive');
        
        // Reactivate user (POST /admin/users/{user}/reactivate)
        Route::post('/users/{user}/reactivate', [UserManagementController::class, 'reactivate'])->name('users.reactivate');
        
        // Get user activity logs (GET /admin/users/{user}/activity-logs)
        Route::get('/users/{user}/activity-logs', [UserManagementController::class, 'activityLogs'])->name('users.activity-logs');
        
        // =========== AJAX ROUTES ===========
        
        // Get departments by institution (GET /admin/get-departments?institution_id=1)
        Route::get('/get-departments', [UserManagementController::class, 'getDepartments'])->name('get-departments');
        
        // Bulk update users (POST /admin/users/bulk-update-status)
        Route::post('/users/bulk-update-status', [UserManagementController::class, 'bulkUpdateStatus'])->name('users.bulk-update-status');
    });
});

// =========== API ROUTES ===========
Route::prefix('api')->group(function () {
    // Get departments by institution
    Route::get('/institutions/{institution}/departments', function (\App\Models\Institution $institution) {
        return $institution->departments()->select('id', 'name', 'code')->get();
    });

    // Get officers (users with acknowledge-receipt permission) by institution
    Route::get('/institutions/{institution}/officers', function (\App\Models\Institution $institution) {
        return $institution->users()
            ->select('users.id', 'users.name', 'departments.name as department')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->get();
    });
});