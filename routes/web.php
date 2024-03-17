<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;


Route::get('/login',[AdminController::class,'login_form'])->name('login.form');
Route::post('/login-validate',[AdminController::class,'login_validate'])->name('login.validate');

Route::get('/register',[AdminController::class,'register_form'])->name('register.form');
Route::post('/register-store',[AdminController::class,'register'])->name('register.store');

Route::group(['middleware'=>'admin'],function(){
    Route::get('/logout',[AdminController::class,'logout'])->name('log-out');
    Route::post('/logout',[AdminController::class,'logout'])->name('log-out');
    
    Route::get('/',[AdminController::class,'dashboard'])->name('dashboard');
    Route::get('/admin/dashboard',[AdminController::class,'dashboard'])->name('dashboard');
});

//Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


/* ================== Clear Cache Routes ================== */
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});

//Reoptimized class loader:
Route::get('/optimize', function() {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

//Route cache:
Route::get('/route-cache', function() {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

//Clear Route cache:
Route::get('/route-clear', function() {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});

//Clear View cache:
Route::get('/view-clear', function() {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});

//Clear All 
Route::get('/clear', function() {
   Artisan::call('cache:clear');
   Artisan::call('config:clear');
   Artisan::call('config:cache');
   Artisan::call('view:clear');
   Artisan::call('route:clear');
   return "All Cleared!";
});

Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    Artisan::call('view:clear');
    return "Cache is cleared";
});
