<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiLoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => $this->message,
            'token'   => $this->when($this->token ?? null, fn () => $this->token),
            'user'    => $this->when($this->user ?? null, fn () => $this->user),
        ];
    }
}
