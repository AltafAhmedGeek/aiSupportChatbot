<?php

use App\Http\Controllers\Api\Chat\ChatBotController;
use Illuminate\Support\Facades\Route;

Route::post('/chat-bot', ChatBotController::class);
