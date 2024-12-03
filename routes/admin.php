<?php

use App\Models\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\SalesSegmentController;
use App\Http\Controllers\Dashboard\CityController;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\SellController;
use App\Http\Controllers\Dashboard\UnitController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\BrandController;
use App\Http\Controllers\Dashboard\BranchController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\ContactController;
use App\Http\Controllers\Dashboard\ExpenseController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Dashboard\AccountCountroller;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\PurchaseController;
use App\Http\Controllers\PaymentTransactionController;
use App\Http\Controllers\Dashboard\SellReturnController;
use App\Http\Controllers\Dashboard\SiteSliderController;
use App\Http\Controllers\Dashboard\ActivityLogController;
use App\Http\Controllers\Dashboard\GovernorateController;
use App\Http\Controllers\Dashboard\SiteSettingController;
use App\Http\Controllers\Dashboard\ActivityTypeController;
use App\Http\Controllers\Dashboard\SpoiledStockController;
use App\Http\Controllers\Dashboard\StockTransferController;
use App\Http\Controllers\Dashboard\AuthenticationController;
use App\Http\Controllers\Dashboard\PurchaseReturnController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Dashboard\ExpenseCategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// date_default_timezone_set(Setting::first()->time_zone);

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {

        Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
            Route::get('/login', [AuthenticationController::class, 'loginView'])->name('adminlogin')->middleware('guest:user');
            Route::post('/login', [AuthenticationController::class, 'login'])->middleware('guest:user');

            Route::post('/summernote_upload_image', [Controller::class, 'summernote_upload_image'])->name('summernote_upload_image');

            Route::group(['middleware' => 'auth:user'], function () {
                Route::get('/', [HomeController::class, 'index'])->name('home');
                Route::get('/logout', [AuthenticationController::class, 'logout'])->name('logout');

                Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
                    Route::get('/', [UserController::class, 'index'])->name('index');
                    Route::get('/create', [UserController::class, 'create'])->name('create');
                    Route::post('/create', [UserController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [UserController::class, 'edit'])->name('edit');
                    Route::post('{id}/edit', [UserController::class, 'update'])->name('update');
                    Route::get('{id}/destroy', [UserController::class, 'destroy'])->name('destroy');
                    Route::get('{id}/activity-logs', [UserController::class, 'activity_logs'])->name('activityLogs');
                });

                Route::group(['prefix' => 'activity-log', 'as' => 'activity-log.'], function () {
                    Route::get('/', [ActivityLogController::class, 'index'])->name('index');
                    Route::get('{id}', [ActivityLogController::class, 'show'])->name('show');
                    Route::get('{id}/activity-logs', [ActivityLogController::class, 'activity_logs'])->name('activityLogs');
                });

                Route::group(['prefix' => 'roles', 'as' => 'roles.'], function () {
                    Route::get('/', [RoleController::class, 'index'])->name('index');
                    Route::get('/create', [RoleController::class, 'create'])->name('create');
                    Route::post('/create', [RoleController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [RoleController::class, 'edit'])->name('edit');
                    Route::post('{id}/edit', [RoleController::class, 'update'])->name('update');
                    Route::get('{id}/destroy', [RoleController::class, 'destroy'])->name('destroy');
                });

                Route::group(['prefix' => 'contacts', 'as' => 'contacts.'], function () {
                    Route::get('/', [ContactController::class, 'index'])->name('index');
                    Route::get('/create', [ContactController::class, 'create'])->name('create');
                    Route::get('/show-contact/{id}', [ContactController::class, 'showContact'])->name('showContact');
                    Route::post('/create', [ContactController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [ContactController::class, 'edit'])->name('edit');
                    Route::post('{id}/edit', [ContactController::class, 'update'])->name('update');
                    Route::get('{id}/destroy', [ContactController::class, 'destroy'])->name('destroy');
                    Route::get('{id}/pay', [PaymentTransactionController::class, 'pay'])->name('pay');
                    Route::post('{id}/pay', [PaymentTransactionController::class, 'payPost'])->name('dashboard.contacts.pay');
                    Route::get('{id}/pay-popup', [PaymentTransactionController::class, 'payPopup'])->name('pay-popup');
                    Route::post('{id}/pay-popup-post', [PaymentTransactionController::class, 'payPopupPost'])->name('pay-popup-post');
                    Route::get('{id}/payment-history/{type}', [PaymentTransactionController::class, 'paymentHistory'])->name('payment-history');
                    Route::get('{id}/payment-history-statistics', [PaymentTransactionController::class, 'getStatisticsByDateRange'])->name('payment-history-statistics');
                    Route::get('ContctCreditLimit', [ContactController::class, 'ContctCreditLimit'])->name('ContctCreditLimit');
                    Route::get('payment-history-details/{id}', [PaymentTransactionController::class, 'paymentHistoryDetails'])->name('payment-history-details');
                    Route::post('import-contacts', [ContactController::class, 'importContacts'])->name('import.contacts');
                    Route::get('import-contacts-view', [ContactController::class, 'importContactsView'])->name('importContactsView');
                });

                Route::group(['prefix' => 'accounts', 'as' => 'accounts.'], function () {
                    Route::get('/', [AccountCountroller::class, 'index'])->name('index');
                    Route::get('/create', [AccountCountroller::class, 'create'])->name('create');
                    Route::post('/create', [AccountCountroller::class, 'store'])->name('store');
                    Route::get('/change-status/{id}', [AccountCountroller::class, 'changeStatus'])->name('change-status');
                    Route::post('/change-status/{id}', [AccountCountroller::class, 'changeStatusPost'])->name('changeStatusPost');
                    Route::get('{id}/edit', [AccountCountroller::class, 'edit'])->name('edit');
                    Route::post('{id}/edit', [AccountCountroller::class, 'update'])->name('update');
                    Route::get('{id}/destroy', [AccountCountroller::class, 'destroy'])->name('destroy');
                    Route::get('{id}/transaction-history', [AccountCountroller::class, 'transactionHistory'])->name('transaction-history');
                    Route::get('{id}/transaction-history', [AccountCountroller::class, 'transactionHistory'])->name('transaction-history');
                    Route::get('{id}/add-deposit', [AccountCountroller::class, 'addDeposit'])->name('add-deposit');
                    Route::post('{id}/add-deposit', [AccountCountroller::class, 'addDepositPost'])->name('add-deposit-post');
                    Route::get('{id}/transfer', [AccountCountroller::class, 'transferForm'])->name('transfer');
                    Route::post('/transfer', [AccountCountroller::class, 'transferMoney'])->name('transfer.post');
                    Route::get('/AccountByBranch', [AccountCountroller::class, 'AccountByBranch'])->name('AccountByBranch');
                });

                Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
                    Route::get('/', [CategoryController::class, 'index'])->name('index');
                    Route::get('/create', [CategoryController::class, 'create'])->name('create');
                    Route::post('/create', [CategoryController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [CategoryController::class, 'edit'])->name('edit');
                    Route::post('{id}/edit', [CategoryController::class, 'update'])->name('update');
                    Route::get('{id}/destroy', [CategoryController::class, 'destroy'])->name('destroy');
                    Route::get('/sub-categories', [CategoryController::class, 'subCategories'])->name('subCategoriesAjax');
                });

                Route::group(['prefix' => 'brands', 'as' => 'brands.'], function () {
                    Route::get('/', [BrandController::class, 'index'])->name('index');
                    Route::get('/create', [BrandController::class, 'create'])->name('create');
                    Route::post('/create', [BrandController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [BrandController::class, 'edit'])->name('edit');
                    Route::post('{id}/edit', [BrandController::class, 'update'])->name('update');
                    Route::get('{id}/destroy', [BrandController::class, 'destroy'])->name('destroy');
                });

                Route::group(['prefix' => 'branchs', 'as' => 'branchs.'], function () {
                    Route::get('/', [BranchController::class, 'index'])->name('index');
                    Route::get('/create', [BranchController::class, 'create'])->name('create');
                    Route::post('/create', [BranchController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [BranchController::class, 'edit'])->name('edit');
                    Route::post('{id}/edit', [BranchController::class, 'update'])->name('update');
                    Route::get('{id}/destroy', [BranchController::class, 'destroy'])->name('destroy');
                    Route::get('/getCitiesByGovernorate', [BranchController::class, 'getCitiesByGovernorate'])->name('getCitiesByGovernorate');
                });
                Route::group(['prefix' => 'cities', 'as' => 'cities.'], function () {
                    Route::get('/', [CityController::class, 'index'])->name('index');
                    Route::get('/create', [CityController::class, 'create'])->name('create');
                    Route::post('/create', [CityController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [CityController::class, 'edit'])->name('edit');
                    Route::post('{id}/edit', [CityController::class, 'update'])->name('update');
                    Route::get('{id}/destroy', [CityController::class, 'destroy'])->name('destroy');
                });
                Route::group(['prefix' => 'governorates', 'as' => 'governorates.'], function () {
                    Route::get('/', [GovernorateController::class, 'index'])->name('index');
                    Route::get('/create', [GovernorateController::class, 'create'])->name('create');
                    Route::post('/create', [GovernorateController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [GovernorateController::class, 'edit'])->name('edit');
                    Route::post('{id}/edit', [GovernorateController::class, 'update'])->name('update');
                    Route::get('{id}/destroy', [GovernorateController::class, 'destroy'])->name('destroy');
                });
                Route::group(['prefix' => 'activityTypes', 'as' => 'activityTypes.'], function () {
                    Route::get('/', [ActivityTypeController::class, 'index'])->name('index');
                    Route::get('/create', [ActivityTypeController::class, 'create'])->name('create');
                    Route::post('/create', [ActivityTypeController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [ActivityTypeController::class, 'edit'])->name('edit');
                    Route::post('{id}/edit', [ActivityTypeController::class, 'update'])->name('update');
                    Route::get('{id}/destroy', [ActivityTypeController::class, 'destroy'])->name('destroy');
                });

                Route::group(['prefix' => 'units', 'as' => 'units.'], function () {
                    Route::get('/', [UnitController::class, 'index'])->name('index');
                    Route::get('/create', [UnitController::class, 'create'])->name('create');
                    Route::post('/create', [UnitController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [UnitController::class, 'edit'])->name('edit');
                    Route::post('{id}/edit', [UnitController::class, 'update'])->name('update');
                    Route::get('{id}/destroy', [UnitController::class, 'destroy'])->name('destroy');
                    Route::get('/sub-units', [UnitController::class, 'subUnitsAjax'])->name('subUnitsAjax');
                    Route::get('/get-sub-units', [UnitController::class, 'getSubUnits'])->name('subUnits');
                    Route::post('/updateUnit', [UnitController::class, 'updateUnit'])->name('product.update');
                    Route::post('/updateUnitStock', [UnitController::class, 'updateUnitStock'])->name('stock.product.update');
                });

                Route::group(['prefix' => 'sells', 'as' => 'sells.'], function () {
                    Route::get('/', [SellController::class, 'index'])->name('index');
                    Route::get('/drafts', [SellController::class, 'allDraft'])->name('drafts.index');
                    Route::get('/create', [SellController::class, 'create'])->name('create');
                    Route::post('/product-row-add', [SellController::class, 'ProductRowAdd'])->name('products.row.add');
                    Route::post('/stock-product-row-add', [SellController::class, 'ProductRowAddStock'])->name('stock.products.row.add');
                    Route::get('/loadSellProducts', [SellController::class, 'loadSellProducts'])->name('load.products');
                    Route::get('/getProductsByBrand', [SellController::class, 'getProductsByBrand'])->name('products.getByBrand');
                    Route::post('/addProductsToTable', [SellController::class, 'addProductsToTable'])->name('products.addToTable');
                    Route::get('/products/search/', [SellController::class, 'searchProducts'])->name('products.search');
                    Route::post('/create', [SellController::class, 'store'])->name('store');
                    Route::get('show/{id}', [SellController::class, 'show'])->name('show');
                    Route::get('{id}/delete', [SellController::class, 'delete'])->name('delete');
                    Route::get('{id}/edit', [SellController::class, 'edit'])->name('edit');
                    Route::get('{id}/edit/draft', [SellController::class, 'draftEdit'])->name('draft.edit');
                    Route::post('{id}/edit', [SellController::class, 'update'])->name('update');
                    Route::post('getLastProductPriceByUnit', [SellController::class, 'getLastProductPriceByUnit'])->name('getLastProductPriceByUnit');
                    Route::post('{id}/finish/draft', [SellController::class, 'finishSell'])->name('draft.finish');
                    Route::get('/multiPay', [SellController::class, 'multiPay'])->name('multiPay');
                    Route::get('{transaction_id}/pay', [PaymentTransactionController::class, 'payTransactionView'])->name('payTransactionView');
                    Route::post('{transaction_id}/pay', [PaymentTransactionController::class, 'payTransaction'])->name('payTransaction');
                    Route::get('{id}/printInvoice', [SellController::class, 'printInvoicePage'])->name('printInvoicePage');
                    Route::get('{id}/printThermalInvoice', [SellController::class, 'printThermalInvoice'])->name('printThermalInvoice');
                    Route::get('{id}/change-delivery-status', [SellController::class, 'changeDeliveryStatus'])->name('change-delivery-status');
                    Route::post('{id}/change-delivery-status', [SellController::class, 'changeDeliveryStatusPost'])->name('change-delivery-status-post');
                    Route::get('/get-segment-prices', [SellController::class, 'getSegmentPrices'])->name('getSegmentPrices');

                    Route::group(['prefix' => 'sell-return', 'as' => 'sell-return.'], function () {
                        Route::get('/', [SellReturnController::class, 'index'])->name('index');
                        Route::get('{sell_id}/create', [SellReturnController::class, 'create'])->name('create');
                        Route::post('{sell_id}/create', [SellReturnController::class, 'store'])->name('store');
                        Route::get('{id}/show', [SellReturnController::class, 'show'])->name('show');
                        Route::get('delete/{id}', [SellReturnController::class, 'delete'])->name('delete');
                    });

                    Route::get('product-row-add', [SellController::class, 'ProductRowAdd'])->name('ProductRowAdd');
                    Route::get('add-bulck-products-PopUp', [SellController::class, 'AddBulckProductsPopUp'])->name('AddBulckProductsPopUp');
                    Route::get('add-bulck-products_ajax', [SellController::class, 'AddBulckProductsAjax'])->name('AddBulckProductsAjax');

                    Route::get('add-bulck-products_insert_ajax', [SellController::class, 'AddBulckProductsInsertAjax'])->name('AddBulckProductsInsertAjax');
                    Route::get('add-bulck-products_insert_ajax', [SellController::class, 'AddBulckProductsInsertAjax'])->name('AddBulckProductsInsertAjax');
                });

                Route::group(['prefix' => 'products', 'as' => 'products.'], function () {
                    Route::get('/', [ProductController::class, 'index'])->name('index');
                    Route::get('/create', [ProductController::class, 'create'])->name('create');
                    Route::post('/create', [ProductController::class, 'store'])->name('store');
                    Route::get('show/{id}', [ProductController::class, 'show'])->name('show');
                    Route::get('{id}/edit', [ProductController::class, 'edit'])->name('edit');
                    Route::post('{id}/edit', [ProductController::class, 'update'])->name('update');
                    Route::get('{id}/destroy', [ProductController::class, 'destroy'])->name('destroy');
                    Route::get('/bulk-edit', [ProductController::class, 'bulkEdit'])->name('bulkEdit');
                    Route::post('/bulk-update', [ProductController::class, 'bulkUpdate'])->name('bulkUpdate');
                    Route::get('{id}/history', [ProductController::class, 'history'])->name('history');
                    Route::get('{product}/statistics/{branch?}', [ProductController::class, 'getStatisticsByBranch'])->name('statistics.branch');
                    Route::get('/proudcts-by-branch-ajax', [ProductController::class, 'ProudctsByBranch'])->name('ProudctsByBranch');
                    Route::get('/proudcts-by-branch-ajax/for-transfer', [ProductController::class, 'ProudctsByBranchForTransfer'])->name('ProudctsByBranchForTransfer');
                    Route::get('{id}/open_stock', [ProductController::class, 'openStockView'])->name('openStockView');
                    Route::post('{id}/open_stock', [ProductController::class, 'openStock'])->name('openStock');
                    Route::get('import', [ProductController::class, 'importView'])->name('importView');
                    Route::post('import', [ProductController::class, 'import'])->name('import');
                    Route::post('downloadTemplateExcel', [ProductController::class, 'downloadTemplateExcel'])->name('downloadTemplateExcel');
                    Route::get('/search', [ProductController::class, 'search'])->name('search');
                    Route::get('import-products', [ProductController::class, 'importView'])->name('importProductsView');
                    Route::get('import-open-stock', [ProductController::class, 'importOpenStockView'])->name('importOpenStockView');
                    Route::post('import-open-stock', [ProductController::class, 'importOpenStock'])->name('importOpenStock');
                });

                Route::group(['prefix' => 'purchases', 'as' => 'purchases.'], function () {
                    Route::get('/', [PurchaseController::class, 'index'])->name('index');
                    Route::get('/create', [PurchaseController::class, 'create'])->name('create');
                    Route::post('/create', [PurchaseController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [PurchaseController::class, 'edit'])->name('edit');
                    Route::post('{id}/edit', [PurchaseController::class, 'update'])->name('update');
                    Route::get('{id}/destroy', [PurchaseController::class, 'destroy'])->name('destroy');
                    Route::get('{id}/show', [PurchaseController::class, 'show'])->name('show');
                    Route::get('/multiPay', [PurchaseController::class, 'multiPay'])->name('multiPay');
                    Route::get('{transaction_id}/payTransactionPurchasView', [PaymentTransactionController::class, 'payTransactionPurchasView'])->name('payTransactionPurchasView');
                    Route::post('{transaction_id}/payTransactionPurchas', [PaymentTransactionController::class, 'payTransactionPurchas'])->name('payTransactionPurchas');
                    Route::get('{id}/printInvoice', [PurchaseController::class, 'printInvoicePage'])->name('printInvoicePage');
                    Route::get('product-row-add', [PurchaseController::class, 'ProductRowAdd'])->name('ProductRowAdd');
                    Route::get('edit-product-row-add', [PurchaseController::class, 'EditProductRowAdd'])->name('EditProductRowAdd');
                    Route::get('{id}/change-delivery-status', [PurchaseController::class, 'changeDeliveryStatus'])->name('change-delivery-status');
                    Route::post('{id}/change-delivery-status', [PurchaseController::class, 'changeDeliveryStatusPost'])->name('change-delivery-status-post');

                    Route::group(['prefix' => 'purchase-return', 'as' => 'purchase-return.'], function () {
                        Route::get('/', [PurchaseReturnController::class, 'index'])->name('index');
                        Route::get('{sell_id}/create', [PurchaseReturnController::class, 'create'])->name('create');
                        Route::post('{sell_id}/create', [PurchaseReturnController::class, 'store'])->name('store');
                        Route::get('show/{id}', [PurchaseReturnController::class, 'show'])->name('show');
                        Route::get('delete/{id}', [PurchaseReturnController::class, 'delete'])->name('delete');
                        Route::get('{id}/printInvoice', [PurchaseReturnController::class, 'printInvoicePage'])->name('printInvoicePage');
                    });
                });

                Route::group(['prefix' => 'expense-categories', 'as' => 'expense-categories.'], function () {
                    Route::get('/', [ExpenseCategoryController::class, 'index'])->name('index');
                    Route::get('/create', [ExpenseCategoryController::class, 'create'])->name('create');
                    Route::post('/create', [ExpenseCategoryController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [ExpenseCategoryController::class, 'edit'])->name('edit');
                    Route::post('{id}/edit', [ExpenseCategoryController::class, 'update'])->name('update');
                    Route::get('{id}/destroy', [ExpenseCategoryController::class, 'destroy'])->name('destroy');
                });
                Route::group(['prefix' => 'expenses', 'as' => 'expenses.'], function () {
                    Route::get('/', [ExpenseController::class, 'index'])->name('index');
                    Route::get('/create', [ExpenseController::class, 'create'])->name('create');
                    Route::post('/create', [ExpenseController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [ExpenseController::class, 'edit'])->name('edit');
                    Route::post('{id}/edit', [ExpenseController::class, 'update'])->name('update');
                    Route::get('{id}/destroy', [ExpenseController::class, 'destroy'])->name('destroy');
                    Route::get('{id}/show', [ExpenseController::class, 'show'])->name('show');
                });
                Route::group(['prefix' => 'stock-transfers', 'as' => 'stock-transfers.'], function () {
                    Route::get('/', [StockTransferController::class, 'index'])->name('index');
                    Route::get('/create', [StockTransferController::class, 'create'])->name('create');
                    Route::get('/loadTransferProducts', [StockTransferController::class, 'loadTransferProducts'])->name('loadTransferProducts');
                    Route::get('{id}/show', [StockTransferController::class, 'show'])->name('show');
                    Route::post('/create', [StockTransferController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [StockTransferController::class, 'edit'])->name('edit');
                    Route::post('{id}/update', [StockTransferController::class, 'update'])->name('update');
                    Route::get('{id}/destroy', [StockTransferController::class, 'destroy'])->name('destroy');
                    Route::get('{id}/change-status', [StockTransferController::class, 'changeStatusView'])->name('changeStatusView');
                    Route::post('{id}/change-status', [StockTransferController::class, 'changeStatus'])->name('changeStatus');
                    Route::get('product-row-add', [StockTransferController::class, 'ProductRowAdd'])->name('ProductRowAdd');
                });
                Route::group(['prefix' => 'spoiled-stock', 'as' => 'spoiled-stock.'], function () {
                    Route::get('/', [SpoiledStockController::class, 'index'])->name('index');
                    Route::get('/create', [SpoiledStockController::class, 'create'])->name('create');
                    Route::get('{id}/show', [SpoiledStockController::class, 'show'])->name('show');
                    Route::post('/create', [SpoiledStockController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [SpoiledStockController::class, 'edit'])->name('edit');
                    Route::post('{id}/update', [SpoiledStockController::class, 'update'])->name('update');
                    Route::get('{id}/change-status', [SpoiledStockController::class, 'changeStatusView'])->name('changeStatusView');
                    Route::post('{id}/change-status', [SpoiledStockController::class, 'changeStatus'])->name('changeStatus');
                    Route::get('{id}/destroy', [SpoiledStockController::class, 'destroy'])->name('destroy');
                    Route::get('product-row-add', [SpoiledStockController::class, 'ProductRowAdd'])->name('ProductRowAdd');
                });

                Route::group(['prefix' => 'sales-segments', 'as' => 'sales-segments.'], function () {
                    Route::get('/', [SalesSegmentController::class, 'index'])->name('index');
                    Route::get('/create', [SalesSegmentController::class, 'create'])->name('create');
                    Route::post('/store', [SalesSegmentController::class, 'store'])->name('store');
                    Route::get('{id}/edit', [SalesSegmentController::class, 'edit'])->name('edit');
                    Route::post('{id}/update', [SalesSegmentController::class, 'update'])->name('update');
                    Route::get('{id}/destroy', [SalesSegmentController::class, 'destroy'])->name('destroy');
                });
                Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
                    Route::get('/edit', [SettingController::class, 'edit'])->name('edit');
                    Route::post('/edit', [SettingController::class, 'update'])->name('update');
                    Route::get('/site', [SettingController::class, 'site'])->name('site');
                    Route::post('/site', [SettingController::class, 'updateSite'])->name('updateSite');
                    Route::get('/sales', [SettingController::class, 'sales'])->name('sales');
                    Route::post('/sales', [SettingController::class, 'updateSales'])->name('updateSales');
                    Route::get('/invoice', [SettingController::class, 'invoice'])->name('invoice');
                    Route::post('/invoice', [SettingController::class, 'updateInvoice'])->name('updateInvoice');
                    Route::get('/products', [SettingController::class, 'products'])->name('products');
                    Route::post('/products', [SettingController::class, 'updateProducts'])->name('updateProducts');
                    Route::get('/contacts', [SettingController::class, 'contacts'])->name('contacts');
                    Route::post('/contacts', [SettingController::class, 'updateContacts'])->name('updateContacts');
                });
                Route::group(['prefix' => 'site-setting', 'as' => 'site-setting.'], function () {
                    Route::get('/edit', [SiteSettingController::class, 'showSettings'])->name('edit');
                    Route::post('/edit', [SiteSettingController::class, 'updateSettings'])->name('update');
                    Route::get('/sliders', [SiteSliderController::class, 'index'])->name('sliders');
                    Route::get('/sliders/create', [SiteSliderController::class, 'createForm'])->name('sliders.create');
                    Route::post('/sliders/create', [SiteSliderController::class, 'createSlider'])->name('sliders.store');
                    Route::get('/sliders/{id}/edit', [SiteSliderController::class, 'showSlider'])->name('sliders.edit');
                    Route::post('/sliders/edit', [SiteSliderController::class, 'updateSlider'])->name('sliders.update');
                    Route::get('/sliders/{id}/destroy', [SiteSliderController::class, 'destroySlider'])->name('sliders.delete');
                 
                });

                Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
                    Route::get('/', [ProfileController::class, 'edit'])->name('edit');
                    Route::post('/', [ProfileController::class, 'update'])->name('update');
                });

                Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
                    Route::get('/stock', [ReportController::class, 'stockReport'])->name('stock');
                    Route::get('/transaction-sell', [ReportController::class, 'transactionSellReport'])->name('transaction.sell');
                    Route::get('/expenses', [ReportController::class, 'expensesReport'])->name('expenses');
                    Route::get('/popular-products', [ReportController::class, 'popularProductsReport'])->name('popular.products');
                    Route::get('/spoiled-products', [ReportController::class, 'spoiledProductsReport'])->name('spoiled.products');
                    Route::get('/dept-report', [ReportController::class, 'deptReport'])->name('dept.report');
                    Route::get('/change-in-price-report', [ReportController::class, 'changeInPriceReport'])->name('change.in.price.report');
                    Route::get('/profit-and-loss-report', [ReportController::class, 'profitAndLossReport'])->name('profit.and.loss.report');
                    Route::get('/profit-and-loss-report/filter', [ReportController::class, 'profitAndLossReportFilter'])->name('profit.and.loss.report.filter');
                });

                Route::get('/products/branches/{product_id}', [ProductController::class, 'branches'])->name('products.branches');

                Route::post('/update_image', [ProfileController::class, 'update_image'])->name('upload.image');

                Route::get('test', function () {

                  
                   dd(auth('user')->user()->has_permission('read-activity-log'));
                    return auth()->user()->unreadNotifications()->orderBy('created_at', 'desc')->get();
                    $transaction = App\Models\Transaction::find(112);
                    return $totalAmount = $transaction->load('PaymentsTransaction');
                    $payments = App\Models\Payment::with('PaymentTransaction')
                        ->withSum('PaymentTransaction', 'amount')
                        ->where('contact_id', 16)
                        ->get();

                    return $payments;
                });
            });
        });

        Route::get('/test-pop-up', [HomeController::class, 'testPopUp'])->name('testPopUp');

        Route::get('/delete-popup', [HomeController::class, 'DeletePopup'])->name('DeletePopup');
        Route::get('/markAllAsRead', [HomeController::class, 'markAllAsRead'])->name('markAllAsRead')->middleware('auth:user');
        Route::post('/markAsRead', [HomeController::class, 'markAsRead'])->name('markAsRead')->middleware('auth:user');
        Route::get('/getUnreadCount', [HomeController::class, 'getUnreadCount'])->name('getUnreadCount')->middleware('auth:user');

        Route::get('/test', function () {
            return App\Models\Contact::with('city','governorate')->get();
        });

        Route::get('/php-version', function () {
            return phpversion();
        });
    }
);
