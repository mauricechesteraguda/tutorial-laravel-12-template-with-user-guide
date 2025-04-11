<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();



// Group routes with auth middleware
Route::middleware(['auth'])->group(function () {
	Route::get('/profile', [UserController::class, 'show'])->name('profile.show');
	Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
	Route::delete('/profile', [UserController::class, 'destroy'])->name('profile.destroy');
});

	Route::middleware(['auth'])->group(function () {
	Route::resource('roles', RoleController::class);
	
	});

	Route::middleware(['auth'])->group(function () {
		
		Route::get('/home', function () {
			return view('home');
		})->name('home');
		});


	Route::middleware(['auth'])->group(function () {
		
		Route::resource('users', UserController::class);
		});
		