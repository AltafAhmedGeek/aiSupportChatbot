<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatBotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_message' => $this->when($this->userMessage, fn () => $this->userMessage),
            'bot_response' => $this->when($this->botResponse, fn () => $this->botResponse),
            'ai_mode'      => $this->when($this->aiMode, fn () => $this->aiMode),
        ];
    }
}
