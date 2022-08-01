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
    Route::get('/week-payment-request/{delegate_id}/{week_end}', function($delegate_id, $week_end){
        $week_orders = \Modules\Delegate\Entities\WeekOrders::where('delivery_man_id', $delegate_id)
            ->where('week_end', $week_end)
            ->first();
        $week_orders->personal_earnings = 0;
        $week_orders->system_earnings = 0;
        $week_orders->save();

        flash(Lang::get('delegate::delivery.payment_request_success'), 'success');
        return back();
    })->name('week.payment.request');
});

// AJAX
Route::get('/province/{id}/zone', 'DelegatesController@getZone');






