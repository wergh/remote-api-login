<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Wergh\RemoteApiLogin\Controllers\ApiGetTokenController;
use Wergh\RemoteApiLogin\Controllers\ApiRequestLoginController;

Route::get(Config::get('remote-api-login.request_url'), ApiRequestLoginController::class);
Route::post(Config::get('remote-api-login.token_url'), ApiGetTokenController::class);
