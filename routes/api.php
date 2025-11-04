<?php

use App\Http\Controllers\Api\Auth\ApiLoginController;
use App\Http\Controllers\Api\Chat\ChatBotController;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', ApiLoginController::class);

Route::group([
    'middleware' => [
        'auth:sanctum',
    ],
], function () {
    Route::post('/chat-bot', ChatBotController::class);
});
