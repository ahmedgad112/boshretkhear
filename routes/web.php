<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\PropertyTypeController;
use App\Http\Controllers\Admin\RentalController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/properties', [HomeController::class, 'properties'])->name('properties.index');
Route::get('/properties/sale', fn () => redirect()->route('properties.index', ['purpose' => 'sale']))->name('properties.sale');
Route::get('/properties/rent', fn () => redirect()->route('properties.index', ['purpose' => 'rent']))->name('properties.rent');
Route::get('/properties/{property}', [HomeController::class, 'show'])->name('properties.show');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/inquiries', [HomeController::class, 'storeInquiry'])->name('inquiries.store');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'active'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/search', SearchController::class)->name('search');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAll'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::resource('properties', PropertyController::class);
    Route::post('properties/{property}/status', [PropertyController::class, 'changeStatus'])->name('properties.status');
    Route::delete('properties/{property}/images/{image}', [PropertyController::class, 'destroyImage'])->name('properties.images.destroy');
    Route::post('properties/{property}/images/{image}/primary', [PropertyController::class, 'setPrimaryImage'])->name('properties.images.primary');
    Route::post('properties/{property}/images/reorder', [PropertyController::class, 'reorderImages'])->name('properties.images.reorder');

    Route::resource('property-types', PropertyTypeController::class)->except(['create', 'edit', 'show']);
    Route::resource('customers', CustomerController::class);
    Route::resource('bookings', BookingController::class);
    Route::post('bookings/{booking}/status', [BookingController::class, 'changeStatus'])->name('bookings.status');
    Route::get('bookings/{booking}/contract', [BookingController::class, 'contract'])->name('bookings.contract');
    Route::get('rentals', RentalController::class)->name('rentals.index');
    Route::resource('sales', SaleController::class);
    Route::post('sales/{sale}/complete', [SaleController::class, 'complete'])->name('sales.complete');
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::resource('expenses', ExpenseController::class)->except(['create', 'edit', 'show']);
    Route::get('accounts', AccountController::class)->name('accounts.index');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
    Route::post('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    Route::resource('roles', RoleController::class)->except(['create', 'edit', 'show']);
    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('activity-logs', ActivityLogController::class)->name('activity-logs.index');
});
