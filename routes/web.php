<?php
use \App\Http\Controllers\ProductController;
use \App\Http\Controllers\ProductSaleController;
/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

Route::group([

    'prefix' => 'warehouse/api'

], function ($router) {
    Route::post('products/store', 'ProductController@store');
    Route::get('products/get', 'ProductController@getAll');
    Route::get('products/get/{barcode}', 'ProductController@getByBarcode');

    Route::post('sales/store', 'ProductSaleController@store');
    Route::get('sales/get', 'ProductSaleController@getAll');
    Route::get('sales/get/{no}', 'ProductSaleController@getByNo');
});
