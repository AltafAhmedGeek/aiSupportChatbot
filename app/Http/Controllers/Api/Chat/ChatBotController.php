<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChatBotRequest;
use App\Http\Resources\ChatBotResource;
use App\Services\ChatService;
use Illuminate\Support\Facades\Log;
use Throwable;

class ChatBotController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ChatBotRequest $request, ChatService $chatService): ChatBotResource
    {
        try {
            $response = $chatService->handleMessage($request->input('message'));

            return new ChatBotResource((object) [
                'userMessage' => $request->validated('message'),
                'botResponse' => $response,
                'aiMode'      => $request->validated('ai_mode', false),
            ]);

        } catch (Throwable $th) {

            Log::error('Error processing chat bot request: '.$th->getMessage());

            report($th);

            return new ChatBotResource((object) [
                'userMessage' => $request->validated('message'),
                'botResponse' => 'Sorry, something went wrong while processing your request.',
                'aiMode'      => $request->validated('ai_mode', false),
            ]);
        }
    }
}
