<?php

use App\Http\Controllers\Admin\ClassController as AdminClassController;
use App\Http\Controllers\Admin\SubjectController as AdminSubjectController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/health-check', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
})->name('health-check');

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Attendance management
    Route::resource('attendance', AttendanceController::class)->except(['edit', 'update']);
    
    // Schedule management
    Route::resource('schedules', ScheduleController::class);
    
    // Leave requests
    Route::resource('leave-requests', LeaveRequestController::class);
    
    // Admin routes
    Route::middleware([App\Http\Middleware\EnsureAdminAccess::class])->prefix('admin')->name('admin.')->group(function () {
        // User management
        Route::resource('users', AdminUserController::class);
        
        // Class management
        Route::resource('classes', AdminClassController::class);
        
        // Subject management  
        Route::resource('subjects', AdminSubjectController::class);
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';