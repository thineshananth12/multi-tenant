<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TenantController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

 

Route::redirect('/', 'login');

Route::get('login', function(){
    return view('login');
});

Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

Route::get('/{tenant}', [TenantController::class, 'index'])->name('tenant.dashboard');




 


 
