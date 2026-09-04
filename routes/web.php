<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ProductController; 
use App\Http\Controllers\Admin\SalesAreaController;
use App\Http\Controllers\Salesman\VisitController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\ReportController; 
use App\Http\Controllers\Admin\TargetController; 
use App\Http\Controllers\ScoreboardController; 
use App\Http\Controllers\Admin\VisitPlanController;

// Public Landing Page
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('public.landing');
});

// Papan Skor Publik (Internal) - Membutuhkan autentikasi
Route::get('/scoreboard', [ScoreboardController::class, 'index'])->name('scoreboard')->middleware('auth');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard khusus Admin/Supervisor/Management
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // 1. Custom Routes (WAJIB diletakkan di ATAS Route::resource)
        
        // --- ROUTE BARU: Stok & Diskon Mitra (Harus di atas resource customers) ---
        Route::get('/customers/{customer}/stocks', [CustomerController::class, 'stockIndex'])->name('customers.stocks');
        Route::post('/customers/{customer}/stocks', [CustomerController::class, 'stockStore'])->name('customers.stocks.store');
        Route::get('/customers/{customer}/discounts', [CustomerController::class, 'discountIndex'])->name('customers.discounts');
        Route::post('/customers/{customer}/discounts', [CustomerController::class, 'discountStore'])->name('customers.discounts.store');
        
        // --- ROUTE BARU: Detail & Manajemen Diskon Mitra ---
        Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        Route::post('/customers/{customer}/discount', [CustomerController::class, 'storeDiscount'])->name('customers.discount.store');
        Route::delete('/customers/{customer}/discount/{discount}', [CustomerController::class, 'destroyDiscount'])->name('customers.discount.destroy');
        Route::post('/customers/{customer}/discount/{discount}/approve', [CustomerController::class, 'approveDiscount'])->name('customers.discount.approve');
        Route::post('/customers/{customer}/discount/{discount}/reject', [CustomerController::class, 'rejectDiscount'])->name('customers.discount.reject');
        // --------------------------------------------------------------------------------

        Route::get('/areas/template', [SalesAreaController::class, 'template'])->name('areas.template');
        Route::post('/areas/import', [SalesAreaController::class, 'import'])->name('areas.import');

        Route::get('/products/template', [ProductController::class, 'template'])->name('products.template');
        Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
        
        // --- ROUTE BARU: Bulk Delete Products ---
        Route::delete('/products/bulk-delete', [ProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');
        // ----------------------------------------

        Route::get('/customers/template', [CustomerController::class, 'template'])->name('customers.template');
        Route::post('/customers/import', [CustomerController::class, 'import'])->name('customers.import');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

        Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
        Route::post('/collections/{collection}/verify', [CollectionController::class, 'verify'])->name('collections.verify');
        
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

        // --- ROUTE BARU: Approval Jadwal & Setting ---
        Route::get('/schedule-approvals', [VisitPlanController::class, 'approvalIndex'])->name('schedule-approvals.index');
        Route::post('/schedule-approvals/{request}/approve', [VisitPlanController::class, 'approveSchedule'])->name('schedule-approvals.approve');
        Route::post('/schedule-approvals/{request}/reject', [VisitPlanController::class, 'rejectSchedule'])->name('schedule-approvals.reject');
        // ---------------------------------------------

        // Tambahkan route resource untuk VisitPlan
        Route::resource('visit-plans', VisitPlanController::class); 

        // 2. Resource Routes (Diletakkan di BAWAH custom routes)
        Route::resource('areas', SalesAreaController::class);
        Route::resource('products', ProductController::class);
        Route::resource('customers', CustomerController::class)->except(['show']); // Kecualikan show karena sudah didefinisikan di atas
        Route::resource('employees', EmployeeController::class);
        Route::resource('tasks', TaskController::class);
        Route::resource('targets', TargetController::class);
    });

    // Salesman Routes
    Route::prefix('salesman')->name('salesman.')->group(function () {
        // Saat salesman login, arahkan ke sini
        Route::get('/home', [VisitController::class, 'index'])->name('home');
        Route::get('/visits', [VisitController::class, 'index'])->name('visits.index');
        Route::get('/visits/{visit}', [VisitController::class, 'show'])->name('visits.show');
        Route::post('/visits/{visit}/product-check', [VisitController::class, 'storeProductCheck'])->name('visits.product_check');
        Route::post('/visits/{visit}/order', [VisitController::class, 'storeOrder'])->name('visits.order');
        Route::post('/visits/{visit}/collection', [VisitController::class, 'storeCollection'])->name('visits.collection');
                
        Route::middleware(['role:salesman'])->group(function () {
            Route::post('/visits/{plan}/checkin', [VisitController::class, 'checkIn'])->name('visits.checkin');
            Route::post('/visits/{visit}/checkout', [VisitController::class, 'checkOut'])->name('visits.checkout');
        });
        
        // Tambahkan route detail task ini
        Route::get('/tasks/{task}', [VisitController::class, 'showTask'])->name('tasks.show');

        Route::post('/visits/{visit}/propose-discount', [VisitController::class, 'proposeDiscount'])->name('visits.propose_discount');

        // --- ROUTE BARU: Usulkan Jadwal & Lihat Stok Toko ---
        Route::get('/schedule/create', [VisitController::class, 'createSchedule'])->name('schedule.create');
        Route::post('/schedule', [VisitController::class, 'storeSchedule'])->name('schedule.store');
        Route::get('/customers/{customer}/stocks', [VisitController::class, 'customerStocks'])->name('customers.stocks');
        // ----------------------------------------------------
    });
});

require __DIR__.'/auth.php';