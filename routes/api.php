<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\ChatController;
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::controller(OrderController::class)->group(function()
{
    Route::post('/orders', 'store')->name('orders.store');
    Route::get('/orders','index')->name('orders.index');
    Route::get('/orders/{id}', 'show')->name('orders.show');
    Route::delete('/orders/{id}',  'destroy')->name('orders.destroy');

    Route::post('/orders/deleted',  'deletedOrders')->name('orders.deleted');

Route::get('/dashboard','dashboard')->name('orders.dashboard');
Route::get('/orders/stream', 'streamOrders');
Route::get('/products/latest',  'getLatestProducts');
Route::get('/products/suggestions',  'getProductSuggestions');

Route::get('/orders/{id}/edit',  'edit')->name('orders.edit');
Route::put('/orders/{id}',  'update')->name('orders.update');

    Route::get('/analytics',  'analytics')->name('orders.analytics');
    Route::post('/orders/{id}/restore', 'restore')->name('orders.restore');
Route::delete('/orders/{id}/force-delete', 'forceDelete')->name('orders.forceDelete');


}
);


    // Route::get('/recommendations',  'getRecommendations')->name('recommend.get');
Route::get('/weather',  [RecommendationController::class,'getWeather'])->name('recommend.weather');
Route::get('/home', [RecommendationController::class,'home'])->name('home');
Route::post('/chat', ChatController::class)->name('chat');

