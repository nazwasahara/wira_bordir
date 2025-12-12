<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Admin\AdditionItemController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\FontController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\LaceOptionController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\MotifRibbonOptionController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RibbonColorController;
use App\Http\Controllers\Admin\RombeOptionController;
use App\Http\Controllers\Admin\SashTypeController;
use App\Http\Controllers\Admin\SideMotifController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Owner\BestSellerController;
use App\Http\Controllers\Owner\FinancialReportController;
use App\Http\Controllers\Owner\OrderSummaryController;
use App\Http\Controllers\Owner\OwnerDashboardController;
use App\Http\Controllers\Owner\PurchaseInvoiceController;
use App\Http\Controllers\Owner\SalesAnalyticsController;
use App\Http\Controllers\Owner\TransactionHistoryController;
use App\Http\Controllers\Owner\UserManagementController;
use App\Http\Controllers\GalleryPageController;
use App\Http\Controllers\Customer\OrderProcessController;

// Guest Routes (Auth)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');

    Route::resource('products', ProductController::class);
    Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');

    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create'); // NEW
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store'); // NEW
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit'); // NEW
    Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update'); // NEW
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('orders/{order}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('orders.confirm-payment');
    Route::patch('orders/{order}/payment', [OrderController::class, 'updatePayment'])->name('orders.update-payment');
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('orders/{order}/payment-proof', [OrderController::class, 'viewPaymentProof'])->name('orders.payment-proof');
    Route::get('orders/{order}/invoice', [OrderController::class, 'printInvoice'])->name('orders.invoice');
    Route::post('orders/{order}/recalculate', [OrderController::class, 'recalculateTotal'])->name('orders.recalculate');

    Route::resource('galleries', GalleryController::class);
});

// Owner Routes
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    // Dashboard
    Route::get('/', [OwnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [OwnerDashboardController::class, 'index']);

    // Requirement #2: Ringkasan Pesanan
    Route::get('/orders/summary', [OrderSummaryController::class, 'index'])->name('orders.summary');
    Route::get('/orders/{order}', [OrderSummaryController::class, 'show'])->name('orders.show'); // NEW

    // Requirement #5: Grafik Penjualan (Analytics)
    Route::get('/sales/analytics', [SalesAnalyticsController::class, 'index'])->name('sales.analytics');
    Route::get('/sales/chart-data', [SalesAnalyticsController::class, 'getChartData'])->name('sales.chart-data');

    // Requirement #6: Laporan Keuangan
    Route::get('/reports/financial', [FinancialReportController::class, 'index'])->name('reports.financial');
    Route::get('/reports/financial/export', [FinancialReportController::class, 'export'])->name('reports.financial.export');
    Route::get('/reports/financial/print', [FinancialReportController::class, 'print'])->name('reports.financial.print');

    // // Requirement #7: Produk Terlaris
    Route::get('/products/bestseller', [BestSellerController::class, 'index'])->name('products.bestseller');

    // // Requirement #8: Riwayat Transaksi
    Route::get('/transactions/history', [TransactionHistoryController::class, 'index'])->name('transactions.history');
    Route::get('/transactions/{order}', [TransactionHistoryController::class, 'show'])->name('transactions.show');

    // // Requirement #3: Manajemen Pengguna
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    // // Requirement #9: Log Aktivitas Admin
    Route::get('/logs/admin-activity', [ActivityLogController::class, 'index'])->name('logs.admin-activity.index');
    Route::get('/logs/admin-activity/{id}', [ActivityLogController::class, 'show'])->name('logs.admin-activity.show');

    Route::resource('purchase-invoices', PurchaseInvoiceController::class);
    Route::get('/purchase-invoices-items', [PurchaseInvoiceController::class, 'getItemsByType'])->name('purchase-invoices.get-items');
});

Route::middleware(['auth', 'role:admin,owner'])
    ->prefix('services')
    ->name('services.')
    ->group(function () {

        Route::resource('materials', MaterialController::class);
        Route::post('materials/{material}/colors', [MaterialController::class, 'storeColor'])->name('materials.colors.store');
        Route::put('materials/{material}/colors/{color}', [MaterialController::class, 'updateColor'])->name('materials.colors.update');
        Route::delete('materials/{material}/colors/{color}', [MaterialController::class, 'destroyColor'])->name('materials.colors.destroy');

        Route::resource('fonts', FontController::class);
        Route::patch('fonts/{font}/toggle-status', [FontController::class, 'toggleStatus'])->name('fonts.toggle-status');

        Route::resource('ribbon-colors', RibbonColorController::class);
        Route::resource('side-motifs', SideMotifController::class);
        Route::resource('sash-types', SashTypeController::class);
        Route::resource('lace-options', LaceOptionController::class);
        Route::resource('rombe-options', RombeOptionController::class);
        Route::resource('motif-ribbon-options', MotifRibbonOptionController::class);

        Route::resource('additional-items', AdditionItemController::class);
        Route::post('additional-items/{additional_item}/options', [AdditionItemController::class, 'storeOption'])->name('additional-items.options.store');
        Route::put('additional-items/{additional_item}/options/{option}', [AdditionItemController::class, 'updateOption'])->name('additional-items.options.update');
        Route::delete('additional-items/{additional_item}/options/{option}', [AdditionItemController::class, 'destroyOption'])->name('additional-items.options.destroy');
    });


// Customer/Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/gallery', [GalleryPageController::class, 'index'])->name('gallery');

// Customer Order
Route::middleware(['auth', 'role:customer'])->group(function () {

    // Step Order
    Route::get('/order/step1', [OrderProcessController::class, 'step1'])
        ->name('order.step1');

    Route::post('/order/step1', [OrderProcessController::class, 'saveStep1'])
        ->name('order.saveStep1');

    Route::get('/order/{order}/step2', [OrderProcessController::class, 'step2'])
        ->name('order.step2');

    Route::post('/order/{order}/step2/save', [OrderProcessController::class, 'saveItems'])
        ->name('order.saveItems');

    Route::get('/order/{order}/step3', [OrderProcessController::class, 'step3'])
        ->name('order.step3');

    // Riwayat Pesanan
    Route::get('/order/history', [OrderProcessController::class, 'history'])
        ->name('order.history');

    // Batalkan pesanan
    Route::delete('/order/{order}/cancel', [OrderProcessController::class, 'cancel'])
        ->name('order.cancel');

    // Detail Pesanan
    Route::get('/orders/{order}', [OrderProcessController::class, 'detail'])
    ->name('order.detail');

    Route::post('/orders/{order}/upload-payment', [OrderProcessController::class, 'uploadPayment'])
    ->name('order.uploadPayment');
});
