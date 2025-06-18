<?php

use App\Http\Controllers\TransactionController;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::redirect('/', 'app/login');

Route::get('/app/accounts/{account}/transactions', TransactionController::class)->middleware(Authenticate::class);
