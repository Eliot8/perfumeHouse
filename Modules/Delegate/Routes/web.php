<?php

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

use App\Models\Cart;
use App\Models\Product;
use Modules\Delegate\Entities\Stock;

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
    Route::resource('delegates', 'DelegatesController')->except(['destroy']);
    Route::resource('provinces', 'ProvinceController')->except(['destroy']);
    Route::resource('zones', 'ZonesController')->except(['destroy']);
    Route::resource('stock', 'StockController')->except(['destroy']);
    
    Route::get('/delegate/{id}/delete', 'DelegatesController@destroy')->name('delegates.destroy');
    Route::get('/province/{id}/delete', 'ProvinceController@destroy')->name('provinces.destroy');
    Route::get('/zones/{id}/delete', 'ZonesController@destroy')->name('zones.destroy');
    Route::get('/stock/{id}/delete', 'StockController@destroy')->name('stock.destroy');

    Route::get('/delegate-stock/{delegate_id}/manage', 'StockController@manage')->name('stock.manage');

    // NIEGHBORHOOD
    Route::post('/neighborhood/create', 'ZonesController@storeNeighborhood')->name('neighborhood.store');
    Route::get('/neighborhood/{id}/delete', 'ZonesController@destroyNeighborhood')->name('neighborhood.destroy');



    // AJAX
    Route::post('/delegate/deleteModal', 'DelegatesController@getModalDeleteByAjax');
    Route::get('/product/{id}/colors', 'StockController@getColors')->name('product.colors');
    Route::get('/product/{id}/attributes', 'StockController@getAttributes')->name('product.attributes');


    # payment reuqest
    Route::get('/week-payment-request/{delegate_id}/{week_end}', 'DelegatesController@paymentRequest')->name('week.payment.request');
    Route::get('/payment-request-invoice/{ids}/{name}', 'DelegatesController@paymentRequestInvoice')->name('payment_request.invoice');

    Route::get('/delegate/{id}/payment_request_view', 'DelegatesController@paymentRequestView')->name('delegates.payment_request_view');

});
Route::post('/delegate/update_payments_info', 'DelegatesController@updatePaymentInfo')->name('deleagte.update.payment_info');

// AJAX
Route::get('/province/{id}/zone', 'DelegatesController@getZone');

Route::get('/update/allStock', function() {
    $products = Product::get();
    foreach($products as $product) {
        $stock = Stock::where('product_id', $product->id)->get();
        foreach($stock as $item) {
            updateOfficialProductStock($product->id, $item->variation);
        }
    }
});






