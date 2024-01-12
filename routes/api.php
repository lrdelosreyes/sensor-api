<?php

use App\Http\Controllers\AuthController;
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
    'prefix' => 'v1',
    'middleware' => ['api']
], function() {
    Route::resource('sensors', SensorController::class)->only(['index']);
    Route::resource('readings', ReadingController::class)->only(['store']);

    Route::name('user.')
        ->group(function() {
            Route::post('/register', [AuthController::class, 'register'])->name('register');
            Route::post('/login', [AuthController::class, 'login'])->name('login');
            Route::get('/unauthenticated', [AuthController::class, 'unauthenticated'])->name('unauthenticated');
        });

    Route::middleware('auth:sanctum')->group(function() {
        Route::get('/me', function (Request $request) {
            return response()->json([
                'data' => [
                    'id' => $request->user()->id,
                    'first_name' => $request->user()->first_name,
                    'middle_name' => $request->user()->middle_name,
                    'last_name' => $request->user()->last_name,
                    'is_admin' => $request->user()->isAdmin(),
                    'username' => $request->user()->username,
                    'email' => $request->user()->email,
                    'email_verified_at' => $request->user()->email_verified_at
                ]
            ]);
        });

        Route::get('/sensors/paginated', [SensorController::class, 'indexPaginated'])
            ->name('sensor.index.paginated');
        Route::resource('sensors', SensorController::class)->except(['index']);
    });
});
