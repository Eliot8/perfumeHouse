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
    
    
    
    Route::get('/test', function() {
        dd(app()->getLocale());
    });
    
});
Route::get('/province/{id}/zone', 'DelegatesController@getZone');


Route::get('/route/list', function () {
    $routes = [];
    foreach (Route::getRoutes()->getIterator() as $route) {
        if (strpos($route->uri, 'api') === false) {
            $routes[] = $route->uri;
        }
    }

    dd($routes);
});



