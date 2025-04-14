<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\TenantController;

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
 
Route::group(['middleware' => 'tenant.identify'], function () {
    Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login'])->name('login');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/gettenants', [AdminController::class, 'getTenants'])->name('gettenants');
    // Admin routes
    Route::post('/registertenant', [AdminController::class, 'registerTenant'])->name('registertenant');
    Route::post('/registeruser', [AdminController::class, 'registerUser'])->name('registeruser');
    Route::post('/manageusers', [AdminController::class, 'manageUsers'])->name('manageusers');

    // Tenant Routes
    Route::post('/adduser', [TenantController::class, 'addUser'])->name('adduser');
    Route::get('/getusers', [TenantController::class, 'getUsers'])->name('getusers');
});
    
            
        
        
        
    
    


 
