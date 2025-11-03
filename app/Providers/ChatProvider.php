<?php

namespace App\Providers;

use App\Services\ChatService;
use Illuminate\Support\ServiceProvider;

class ChatProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        app()->bind(ChatService::class, function () {
            return new ChatService(request()->boolean('ai_mode', false));
        });
    }
}
