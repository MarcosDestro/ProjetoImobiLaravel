<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['as', 'api.'], function(){

    Route::post('/auth/login', [AuthController::class, 'login'])->name('login');

    /** Rotas que devem estar com o usuário autenticado */
    Route::group(['middleware' => ['apiJwt']], function(){

        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/me', [AuthController::class, 'me'])->name('me');
        Route::apiResource('/company', CompanyController::class);

    });
});
