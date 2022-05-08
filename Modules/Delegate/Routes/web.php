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

use Illuminate\Support\Facades\Hash;
use Modules\Delegate\Entities\Delegate;

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
    // Route::get('/province/{id}/zone', 'DelegatesController@getZone');
    Route::post('/delegate/deleteModal', 'DelegatesController@getModalDeleteByAjax');
      
});

Route::get('/hash/password', function() {
    $passowrd = Hash::make('*jrJarj@^p5jMh5q*jrJarj@^p5jMh5q');
    dd($passowrd);
});
Route::get('/province/{id}/zone', 'DelegatesController@getZone');

Route::get('/pass/check', function () {
    $delegate = Delegate::find(15);
    $pass = Hash::check('123456', $delegate->password);
    dd($pass);
});




