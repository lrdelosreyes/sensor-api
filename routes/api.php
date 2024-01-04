<?php

use App\Http\Controllers\ReadingController;
use App\Http\Controllers\SensorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([
    'prefix' => 'v1'
], function() {
    // Route::name('user.')
    //     ->group(function() {
    //         Route::get('/users', [UserController::class, 'index'])->name('index');
    //         Route::get('/users/{user}', [UserController::class, 'show'])->name('show');
    //         Route::post('/users', [UserController::class, 'store'])->name('store');
    //         Route::patch('/users/{user}', [UserController::class, 'update'])->name('update');
    //         Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('destroy');
    //     });

    Route::name('sensor.')
        ->group(function() {
            Route::get('/sensors', [SensorController::class, 'index'])->name('index');
            Route::get('/sensors/paginated', [SensorController::class, 'indexPaginated'])->name('index.paginated');
            Route::get('/sensors/{sensor}', [SensorController::class, 'show'])->name('show');
            Route::post('/sensors', [SensorController::class, 'store'])->name('store');
            Route::patch('/sensors/{sensor}', [SensorController::class, 'update'])->name('update');
            Route::delete('/sensors/{sensor}', [SensorController::class, 'destroy'])->name('destroy');
        });

    Route::name('reading.')
        ->group(function() {
            Route::post('/readings', [ReadingController::class, 'store'])->name('store');
        });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


