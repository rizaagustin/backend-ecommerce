<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::prefix('admin')->group(function(){

    //route login
    Route::post('/login',[App\Http\Controllers\Api\Admin\LoginController::class, 'index',['as' => 'admin']]);    

    Route::group(['middleware' => 'auth:api_admin'], function() {

        Route::get('/user',[App\Http\Controllers\Api\Admin\LoginController::class, 'getUser',['as' => 'admin']]);
        //refresh token JWT
        Route::get('/refresh', [App\Http\Controllers\Api\Admin\LoginController::class, 'refreshToken', ['as' => 'admin']]);

        //logout
        Route::post('/logout', [App\Http\Controllers\Api\Admin\LoginController::class, 'logout', ['as' => 'admin']]);

        //dashobard
        Route::get('/dashboard',[App\Http\Controllers\Api\Admin\DashboardController::class, 'index',['as' => 'admin']]);

        Route::apiResource('categories',App\Http\Controllers\Api\Admin\CategoryController::class,['except' => ['create','edit'], 'as' => 'admin']);

        Route::apiResource('products',App\Http\Controllers\Api\Admin\ProductController::class,['except' => ['create','edit'], 'as' => 'admin']);

        Route::apiResource('invoices',App\Http\Controllers\Api\Admin\InvoiceController::class,['except' => ['create','edit'], 'as' => 'admin']);

        //invoices resource
        Route::apiResource('/invoices', App\Http\Controllers\Api\Admin\InvoiceController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy'], 'as' => 'admin']);

        Route::get('/customers', [App\Http\Controllers\Api\Admin\CustomerController::class, 'index', ['as' => 'admin']]);
        //sliders resource
        Route::apiResource('/sliders', App\Http\Controllers\Api\Admin\SliderController::class, ['except' => ['create', 'show', 'edit', 'update'], 'as' => 'admin']);

        //user
        Route::apiResource('/users',App\Http\Controllers\Api\Admin\UserController::class,['except' => ['create','edit'], 'as' => 'admin']);
    });

});

//group route with prefix "customer"
Route::prefix('customer')->group(function () {

    //route register
    Route::post('/register', [App\Http\Controllers\Api\Customer\RegisterController::class, 'store'], ['as' => 'customer']);
    Route::post('/login', [App\Http\Controllers\Api\Customer\LoginController::class, 'index'], ['as' => 'customer']);

    Route::group(['middleware' => 'auth:api_customer'], function () {
        //data user
        Route::get('/user', [App\Http\Controllers\Api\Customer\LoginController::class, 'getUser'], ['as' => 'customer']);
        //refresh token
        Route::get('/refresh', [App\Http\Controllers\Api\Customer\LoginController::class, 'refresh'], ['as' => 'customer']);
        //logout
        Route::post('/logout', [App\Http\Controllers\Api\Customer\LoginController::class, 'logout'], ['as' => 'customer']);
        //dashboard
        Route::get('/dashboard',[App\Http\Controllers\Api\Customer\DashboardController::class, 'index'],['as' => 'customer']);
    
        Route::apiResource('/invoices', App\Http\Controllers\Api\Customer\InvoiceController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy'], 'as' => 'customer']);
        //review
        Route::post('/reviews', [App\Http\Controllers\Api\Customer\ReviewController::class, 'store'], ['as' => 'customer']);

    });
    
});
    