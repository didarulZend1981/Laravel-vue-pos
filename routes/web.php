<?php

use App\Http\Controllers\AuthController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SaleInvoiceController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Middleware\tokenVerificationMiddleware;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StockController;

//home page
Route::get('/', function () {
    // return Inertia::render('HomePage');
});

//======================Registration Management=====================//
Route::get('/sign-up', [AuthController::class, 'showSignUp'])->name('signup.page');
Route::post('/registration', [AuthController::class, 'signUp'])->name('signup');

//======================Login Management=====================//
Route::get('/', [AuthController::class, 'showSignIn'])->name('signIn.page');
Route::post('/login', [AuthController::class, 'signIn'])->name('signIn');

//======================OTP management =====================//
Route::get('/otp', [AuthController::class, 'showOTPPage'])->name('OTP.page');
Route::post('/send-otp', [AuthController::class, 'sendOTP'])->name('send.OTP');
Route::get('/verify-otp', [AuthController::class, 'showVerifyOTPPage'])->name('verify.OTP.page');
Route::post('/otp-verification', [AuthController::class, 'verifyOTP'])->name('verify.OTP');

//======================New password management =====================//
Route::group(['prefix' => '/new-password', 'middleware' => tokenVerificationMiddleware::class], function () {
    Route::get('/', [AuthController::class, 'showNewPasswordPage'])->name('new.password.page');
    Route::post('/set', [AuthController::class, 'newPassword'])->name('new.password');
});

//==========================logout management ==========================//
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

//=======================update profile==========================//
Route::group(['prefix' => '/profile', 'middleware' => tokenVerificationMiddleware::class], function () {
    Route::get('/get', [ProfileController::class, 'getProfile'])->name('get.profile');
    Route::post('/update', [ProfileController::class, 'updateProfile'])->name('update.profile');
});

//======================Category Management=====================//
Route::group(['prefix' => 'category', 'middleware' => tokenVerificationMiddleware::class], function () {
    Route::get('/', [CategoryController::class, 'showCategory'])->name('category.page');
    Route::get('/all', [CategoryController::class, 'getAllCategories'])->name('all.categories');
    Route::get('/by/{id}', [CategoryController::class, 'categoryById'])->name('category.by.id');
    Route::get('/save', [CategoryController::class, 'showSaveCategory'])->name('show.save.category'); //working
    Route::post('/store', [CategoryController::class, 'storeCategory'])->name('store.category'); //working
    Route::post('/update/{id}', [CategoryController::class, 'updateCategory'])->name('update.category'); //working
    Route::delete('/delete/{id}', [CategoryController::class, 'deleteCategory'])->name('delete.category'); //working
});

//======================Brand Management=====================//
Route::group(['prefix' => 'brand', 'middleware' => tokenVerificationMiddleware::class], function () {
    Route::get('/', [BrandController::class, 'showBrand'])->name('brand.page');
    Route::get('/save', [BrandController::class, 'showSaveBrand'])->name('show.save.brand');
    Route::post('/store', [BrandController::class, 'storeBrand'])->name('store.brand');
    Route::post('/update/{id}', [BrandController::class, 'updateBrand'])->name('update.brand');
    Route::delete('/delete/{id}', [BrandController::class, 'deleteBrand'])->name('delete.brand');
});

//========================Product Management=====================//
Route::group(['prefix' => 'product', 'middleware' => tokenVerificationMiddleware::class], function () {
    Route::get('/', [ProductController::class, 'showProduct'])->name('product.page');
    Route::get('/all', [ProductController::class, 'getAllProducts'])->name('all.products');
    Route::get('/save', [ProductController::class, 'showSaveProduct'])->name('show.save.product');
    Route::post('/store', [ProductController::class, 'storeProduct'])->name('store.product');
    Route::post('/update/{id}', [ProductController::class, 'updateProduct'])->name('update.product');
    Route::delete('/delete/{id}', [ProductController::class, 'deleteProduct'])->name('delete.product');
    Route::get('/low-stock', [ProductController::class, 'getLowStockProduct'])->name('low.stock.products');
});

//======================Customer Management=====================//
Route::group(['prefix' => 'customer', 'middleware' => tokenVerificationMiddleware::class], function () {
    Route::get('/', [CustomerController::class, 'showCustomer'])->name('customer.page');
    Route::get('/save', [CustomerController::class, 'showSaveCustomer'])->name('show.save.customer');
    Route::post('/store', [CustomerController::class, 'storeCustomer'])->name('store.customer');
    Route::post('/update/{id}', [CustomerController::class, 'updateCustomer'])->name('update.customer');
    Route::delete('/delete/{id}', [CustomerController::class, 'deleteCustomer'])->name('delete.customer');
    Route::get('/details/{id}', [CustomerController::class, 'customerDetails'])->name('customer.details');
});

//======================Supplier Management=====================//
Route::group(['prefix' => 'supplier', 'middleware' => tokenVerificationMiddleware::class], function () {
    Route::get('/', [SupplierController::class, 'showSupplier'])->name('supplier.page');
    Route::get('/save', [SupplierController::class, 'showSaveSupplier'])->name('show.save.supplier');
    Route::post('/store', [SupplierController::class, 'storeSupplier'])->name('store.supplier');
    Route::post('/update/{id}', [SupplierController::class, 'updateSupplier'])->name('update.supplier');
    Route::delete('/delete/{id}', [SupplierController::class, 'deleteSupplier'])->name('delete.supplier');
    Route::get('details/{id}', [SupplierController::class, 'supplierDetails'])->name('supplier.details');
});


//========================Sale Invoice Management=====================//
Route::group(['prefix' => '/sale-invoice', 'middleware' => tokenVerificationMiddleware::class], function () {
    Route::get('/list', [SaleInvoiceController::class, 'showInvoice'])->name('show.invoice.page');
    Route::get('/', [SaleInvoiceController::class, 'showCreateSale'])->name('show.sale');
    Route::post('/create', [SaleInvoiceController::class, 'createSaleInvoice'])->name('create.sale.invoice');
    Route::get('/custom', [SaleInvoiceController::class, 'showCustomeSaleCreate'])->name('show.custom.sale');
    Route::post('/custom-create', [SaleInvoiceController::class, 'customeSaleCreate'])->name('custom.sale.create');
    Route::post('/details/{id}', [SaleInvoiceController::class, 'invoiceDetails'])->name('sale.invoice.details');
    Route::delete('/delete/{id}', [SaleInvoiceController::class, 'invoiceDelete'])->name('sale.invoice.delete');
});

//========================Invoice Management=====================//
Route::group(['prefix' => '/purchase-invoice', 'middleware' => tokenVerificationMiddleware::class], function () {
    Route::get('/list', [PurchaseInvoiceController::class, 'showPurchaseInvoiceList'])->name('show.purchase.invoice.page');
    Route::get('/', [PurchaseInvoiceController::class, 'showPurchaseInvoice'])->name('show.purchase.invoice');
    Route::post('/create', [PurchaseInvoiceController::class, 'createPurchaseInvoice'])->name('create.purchase.invoice');
    Route::get('/custom', [PurchaseInvoiceController::class, 'showCustomPurchaseInvoice'])->name('show.custom.purchase.create.page');
    Route::post('/custom-create', [PurchaseInvoiceController::class, 'createCustomPurchaseInvoice'])->name('create.custom.purchase');
    Route::post('/details/{id}', [PurchaseInvoiceController::class, 'purchaseInvoiceDetails'])->name('purchase.invoice.details');
    Route::delete('/delete/{id}', [PurchaseInvoiceController::class, 'purchaseInvoiceDelete'])->name('purchase.invoice.delete');
});

//========================Report Management=====================//
Route::group(['prefix' => '/report', 'middleware' => tokenVerificationMiddleware::class], function () {
    Route::get('/', [ReportController::class, 'reportPage'])->name('report.page');
    Route::get('/sales/{FormDate}/{ToDate}', [ReportController::class, 'salesReport'])->name('sales.report');
    Route::get('/purchase/{FormDate}/{ToDate}', [ReportController::class, 'purchaseReport'])->name('purchase.report');
});

//======================Dashboard Management=====================//
Route::group(['prefix' => 'dashboard', 'middleware' => tokenVerificationMiddleware::class], function () {
    Route::get('/', [DashboardController::class, 'showDashboard'])->name('show.dashboard');
});


// //======================Dashboard Management=====================//
// Route::group(['prefix' => 'stock-report', 'middleware' => tokenVerificationMiddleware::class], function () {
//     // Route::get('/', [DashboardController::class, 'showDashboard'])->name('show.stock-report');
//     // Route::inertia('/stock-report', 'StockReport/Index');
//     Route::inertia('/', '/Dashboard/StockReport/Index')->name('stock.report');
//     // return Inertia::render
// });





//========================Report Management=====================//
Route::group(['prefix' => '/stock', 'middleware' => tokenVerificationMiddleware::class], function () {
    Route::get('/', [StockController::class, 'StockPage'])->name('stock.page');
    Route::post('/stocks', [StockController::class, 'stocksReport'])->name('stocks.report');
    // Route::get('/purchase/{FormDate}/{ToDate}', [ReportController::class, 'purchaseReport'])->name('purchase.report');
});


//======================User Management=====================//
Route::group(['prefix' => 'user', 'middleware' => tokenVerificationMiddleware::class], function () {

        Route::get('/', [AuthController::class, 'showUser'])->name('user.page');
        Route::get('/save', [AuthController::class, 'showSaveUser'])->name('show.save.user');
        Route::post('/store', [AuthController::class, 'signUp'])->name('store.user');
        Route::post('/update', [ProfileController::class, 'updateProfile'])->name('update.user');
        Route::put('/user/{id}/update-role', [AuthController::class, 'updateRole'])->name('update.role');
        Route::delete('/delete/{id}', [AuthController::class, 'deleteUser'])->name('delete.user');


});

//========================404 Page Management=====================//
Route::fallback(function () {
    return Inertia::render('404/404');
});

//====================cometing soon page=======================//
Route::get('/comeing-soon', function () {
    return Inertia::render('ComeingSoon');
})->name('comeing.soon');

