<?php

use Illuminate\Support\Facades\Route;
use Wergh\RemoteApiLogin\Controllers\ApiGetTokenController;
use Wergh\RemoteApiLogin\Controllers\ApiRequestLoginController;

Route::get('/login-request', ApiRequestLoginController::class);
Route::post('/get-token', ApiGetTokenController::class);
