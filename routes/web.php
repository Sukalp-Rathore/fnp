<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BouquetController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\EventController;
// Auth Routes 
Route::middleware(['web'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware(['web' ,'auth'])->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    });
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/mails', [NotificationController::class, 'index'])->name('mails');
    Route::post('/send-mail', [NotificationController::class, 'sendMail'])->name('send.mail');
    Route::post('/send-whatsapp', [NotificationController::class, 'sendWhatsapp'])->name('send.whatsapp');
    Route::post('/send-sms', [NotificationController::class, 'sendSms'])->name('send.sms');
    Route::get('/mail-test',[NotificationController::class, 'testMail'])->name('mail.test');
    Route::get('/customers', [CustomerController::class, 'customers'])->name('customers');
    Route::post('/add-customer', [CustomerController::class, 'createCustomer'])->name('customer.create');
    Route::post('/show-edit-customer', [CustomerController::class, 'showEditCustomer'])->name('customer.show.edit');
    Route::post('/update-customer', [CustomerController::class, 'updateCustomer'])->name('customer.update');
    Route::post('/delete-customer', [CustomerController::class, 'deleteCustomer'])->name('customer.delete');
    Route::get('/vendors', [VendorController::class, 'vendors'])->name('vendors');
    Route::post('/vendor-orders', [VendorController::class, 'getVendorOrders'])->name('vendor.orders');
    Route::post('/add-vendor', [VendorController::class, 'createVendor'])->name('vendor.create');
    Route::post('/show-edit-vendor', [VendorController::class, 'showEditVendor'])->name('vendor.show.edit');
    Route::post('/update-vendor', [VendorController::class, 'updateVendor'])->name('vendor.update');
    Route::post('/delete-vendor', [VendorController::class, 'deleteVendor'])->name('vendor.delete');
    Route::get('/inventory', [InventoryController::class, 'inventory'])->name('inventory');
    Route::post('/add-inventory', [InventoryController::class, 'createInventory'])->name('inventory.create');
    Route::post('/inventory-show-image', [InventoryController::class, 'showImage'])->name('inventory.show.image');
    Route::post('/show-edit-inventory', [InventoryController::class, 'showEditInventory'])->name('inventory.show.edit');
    Route::post('/update-inventory', [InventoryController::class, 'updateInventory'])->name('inventory.update');
    Route::post('/delete-inventory', [InventoryController::class, 'deleteInventory'])->name('inventory.delete');
    Route::post('/fetch-stock', [InventoryController::class, 'fetchStock'])->name('inventory.fetch.stock');
    Route::post('/update-stock', [InventoryController::class, 'updateStock'])->name('inventory.update.stock');
    Route::get('/bouquet', [BouquetController::class, 'bouquet'])->name('bouquet');
    Route::post('/bouquet/fetch-inventory', [BouquetController::class, 'fetchInventory'])->name('bouquet.fetch.inventory');
    Route::post('/bouquet/create', [BouquetController::class, 'createBouquet'])->name('bouquet.create');
    Route::post('/bouquet/details', [BouquetController::class, 'fetchBouquetDetails'])->name('bouquet.details');
    Route::post('/bouquet/edit-modal', [BouquetController::class, 'fetchEditModal'])->name('bouquet.edit.modal');
    Route::post('/bouquet/update', [BouquetController::class, 'updateBouquet'])->name('bouquet.update');
    Route::post('/bouquet-receipt', [BouquetController::class, 'getBouquetReceipt'])->name('bouquet.receipt');
    Route::get('/all-orders', [OrderController::class, 'allOrders'])->name('all-orders');
    Route::post('/orders', [OrderController::class, 'createOrder'])->name('order.create');
    Route::post('/get-vendors-by-city', [OrderController::class, 'getVendorsByCity'])->name('get.vendors.by.city');
    Route::post('/order-mark-delivered', [OrderController::class, 'markDelivered'])->name('order.mark.delivered');
    Route::get('/sales', [SalesController::class, 'sales'])->name('sales');
    Route::post('/sales-enter', [SalesController::class ,'enterSales'])->name('sales.enter');
    Route::get('/purchase', [PurchaseController::class, 'purchase'])->name('purchase');
    Route::post('/purchase-enter', [PurchaseController::class ,'enterPurchase'])->name('purchase.enter');
    Route::get('/purchase-vendors', [PurchaseController::class, 'index'])->name('purchase-vendors.index');
    Route::post('/purchase-vendors/add', [PurchaseController::class, 'store'])->name('purchase-vendors.store');
    Route::post('/purchase-vendors/edit', [PurchaseController::class, 'update'])->name('purchase-vendors.update');
    Route::post('/purchase-vendors/delete', [PurchaseController::class, 'destroy'])->name('purchase-vendors.destroy');
    Route::get('/event', [EventController::class, 'event'])->name('event');
    Route::post('/event', [EventController::class ,'createEvent'])->name('event.create');
    Route::post('/show-edit-event', [EventController::class, 'showEditEvent'])->name('event.show.edit');
    Route::post('/update-event', [EventController::class, 'updateEvent'])->name('event.update');

    Route::get('/test-form',[DataController::class, 'testForm'])->name('test.form');

    // data routes 
    Route::post('add-customer-test',[DataController::class, 'addCustomer'])->name('add.customer');
    Route::post('add-inventoryss',[DataController::class, 'uploadCsv'])->name('add.stock');
    Route::get('customer-change-type',[DataController::class, 'customerType'])->name('customer.change.type');
    Route::get('import-data', [DataController::class, 'importData'])->name('import.data');
    Route::get('find-duplicate-customers', [DataController::class, 'findDuplicateCustomers'])->name('find.duplicate.customers');
    Route::get('delete-duplicate-customers', [DataController::class, 'deleteDuplicateCustomers'])->name('delete.duplicate.customers');
    Route::get('update-dates',[DataController::class, 'updateDates'])->name('update.dates');
});