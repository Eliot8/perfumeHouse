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

use Modules\Delegate\Entities\Province;
use Modules\Delegate\Entities\Zone;

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
    Route::resource('delegates', 'DelegatesController')->except(['destroy']);
    Route::resource('provinces', 'ProvinceController')->except(['destroy']);
    Route::resource('zones', 'ZonesController')->except(['destroy']);
    
    Route::get('/delegate/{id}/delete', 'DelegatesController@destroy')->name('delegates.destroy');
    Route::get('/province/{id}/delete', 'ProvinceController@destroy')->name('provinces.destroy');
    Route::get('/zones/{id}/delete', 'ZonesController@destroy')->name('zones.destroy');



    // AJAX
    Route::get('/province/{id}/zone', 'DelegatesController@getZone');
    Route::post('/delegate/deleteModal', 'DelegatesController@getModalDeleteByAjax');



    Route::get('/test', function() {
        
    });

});


Route::get('/route/list', function () {
    $routes = [];
    foreach (Route::getRoutes()->getIterator() as $route) {
        if (strpos($route->uri, 'api') === false) {
            $routes[] = $route->uri;
        }
    }

    dd($routes);
});



