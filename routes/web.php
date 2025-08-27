<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('dashboard', [DashboardController::class, 'index']);