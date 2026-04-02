<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.change.submit');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/users', [AdminController::class, 'users'])->name('users.index');
Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
Route::get('/assets', [AdminController::class, 'assets'])->name('assets.index');
Route::get('/assets/create', [AdminController::class, 'createAsset'])->name('assets.create');
Route::post('/assets', [AdminController::class, 'storeAsset'])->name('assets.store');
Route::get('/assets/{asset}/edit', [AdminController::class, 'editAsset'])->name('assets.edit');
Route::put('/assets/{asset}', [AdminController::class, 'updateAsset'])->name('assets.update');
Route::delete('/assets/{asset}', [AdminController::class, 'destroyAsset'])->name('assets.destroy');
Route::get('/borrow-requests', [AdminController::class, 'borrowRequests'])->name('borrow.index');
Route::get('/active-borrows', [AdminController::class, 'activeBorrows'])->name('borrow.active');
Route::post('/borrow-requests/{borrowRequest}/approve', [AdminController::class, 'approveRequest'])->name('borrow.approve');
Route::post('/borrow-requests/{borrowRequest}/reject', [AdminController::class, 'rejectRequest'])->name('borrow.reject');
Route::get('/borrow-requests/{borrowRequest}/handover', [AdminController::class, 'handoverForm'])->name('borrow.handover');
Route::post('/borrow-requests/{borrowRequest}/handover', [AdminController::class, 'handoverStore'])->name('borrow.handover.store');
Route::get('/active-borrows/{borrow}/return', [AdminController::class, 'returnForm'])->name('borrow.return');
Route::post('/active-borrows/{borrow}/return', [AdminController::class, 'returnStore'])->name('borrow.return.store');

Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read');

Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
Route::get('/user/borrowings', [UserController::class, 'myBorrowings'])->name('user.borrowings');
Route::get('/user/borrowings/new', [UserController::class, 'createBorrowRequest'])->name('user.borrowings.new');
Route::post('/user/borrowings', [UserController::class, 'storeBorrowRequest'])->name('user.borrowings.store');
