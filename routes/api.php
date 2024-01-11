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
    Route::get('/sensors', [SensorController::class, 'index'])->name('sensor.index');

    Route::name('user.')
        ->group(function() {
            Route::post('/register', [AuthController::class, 'register'])->name('register');
            Route::post('/login', [AuthController::class, 'login'])->name('login');
            Route::get('/unauthenticated', [AuthController::class, 'unauthenticated'])->name('unauthenticated');
        });

    Route::name('reading.')
        ->group(function() {
            Route::post('/readings', [ReadingController::class, 'store'])->name('store');
        });

    Route::middleware('auth:sanctum')->group(function() {
        Route::get('/me', function (Request $request) {
            return response()->json([
                'data' => [
                    'id' => $request->user()->id,
                    'first_name' => $request->user()->first_name,
                    'middle_name' => $request->user()->first_name,
                    'last_name' => $request->user()->first_name,
                    'is_admin' => $request->user()->isAdmin(),
                    'username' => $request->user()->username,
                    'email' => $request->user()->email,
                    'email_verified_at' => $request->user()->email_verified_at
                ]
            ]);
        });
        Route::name('sensor.')
            ->group(function() {
                Route::get('/sensors/paginated', [SensorController::class, 'indexPaginated'])->name('index.paginated');
                Route::get('/sensors/{sensor}', [SensorController::class, 'show'])->name('show');
                Route::post('/sensors', [SensorController::class, 'store'])->name('store');
                Route::patch('/sensors/{sensor}', [SensorController::class, 'update'])->name('update');
                Route::delete('/sensors/{sensor}', [SensorController::class, 'destroy'])->name('destroy');
            });
    });
});
