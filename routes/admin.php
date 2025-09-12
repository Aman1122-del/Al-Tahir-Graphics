<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\QuoteController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\ChatSettingsController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');

    // Users Management
    Route::resource('users', UserController::class);
    Route::post('users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
    Route::post('stop-impersonating', [UserController::class, 'stopImpersonating'])->name('users.stop-impersonating');

    // Orders Management
    Route::resource('orders', OrderController::class)->except(['create', 'store', 'destroy']);
    Route::post('orders/{order}/assign-designer', [OrderController::class, 'assignDesigner'])->name('orders.assign-designer');
    Route::post('orders/{order}/add-note', [OrderController::class, 'addNote'])->name('orders.add-note');
    Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');

    // Quotes Management
    Route::resource('quotes', QuoteController::class);
    Route::post('quotes/{quote}/assign-designer', [QuoteController::class, 'assignDesigner'])->name('quotes.assign-designer');
    Route::post('quotes/{quote}/approve', [QuoteController::class, 'approve'])->name('quotes.approve');
    Route::post('quotes/{quote}/reject', [QuoteController::class, 'reject'])->name('quotes.reject');

    // Invoices Management
    Route::resource('invoices', InvoiceController::class);

    // Services CRUD
    Route::resource('services', ProductController::class);

    // Services quick actions
    Route::post('services/{service}/toggle', [AdminServiceController::class, 'toggle'])->name('services.toggle');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'generatePdf'])->name('invoices.pdf');
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'downloadPdf'])->name('invoices.download');
    Route::post('invoices/{invoice}/send-email', [InvoiceController::class, 'sendEmail'])->name('invoices.send-email');
    Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/orders', [ReportController::class, 'orders'])->name('orders');
        Route::get('/services', [ReportController::class, 'services'])->name('services');
        Route::get('/designers', [ReportController::class, 'designers'])->name('designers');
        Route::get('/export-sales', [ReportController::class, 'exportSales'])->name('export-sales');
    });

    // Chat Management
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/{userId}', [ChatController::class, 'show'])->name('show');
        Route::get('/statistics', [ChatController::class, 'statistics'])->name('statistics');
        Route::get('/search-users', [ChatController::class, 'searchUsers'])->name('search-users');
        Route::get('/export', [ChatController::class, 'export'])->name('export');
    });

    // Chat Settings
    Route::get('chat-settings', [ChatSettingsController::class, 'edit'])->name('chat.settings');
    Route::post('chat-settings', [ChatSettingsController::class, 'save'])->name('chat.settings.save');
});
