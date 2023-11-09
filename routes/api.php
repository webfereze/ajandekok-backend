<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CanvasController;
use App\Http\Controllers\OrderApiController;
use App\Models\Order;

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

// Route::get('/products', function(){
//     return 'products';
// });

//Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/orders', [OrderApiController::class, 'store']);
Route::get('canvas', [CanvasController::class, 'getData']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('orders', [OrderApiController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('canvas', [CanvasController::class, 'create'])->middleware('admin');
    Route::put('canvas/{id}', [CanvasController::class, 'edit'])->middleware('admin');
    Route::delete('canvas/{id}', [CanvasController::class, 'delete'])->middleware('admin');
    Route::put('/orders/{order_id}', [OrderApiController::class, 'updateStatus']);
    Route::delete('/orders/{order_id}', [OrderApiController::class, 'deleteOrder']);
    Route::put('/canvas/{id}/status', [CanvasController::class, 'updateStatus']);
});
