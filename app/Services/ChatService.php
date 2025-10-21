<?php

namespace App\Services;

use App\Enums\Chat\BasicIntendEnum;
use App\Models\Faq;

class ChatService
{
    public function __construct(
        private ChatIntendDetectorService $detector
    ) {}

    public function handleMessage($message, $user)
    {
        $intend = $this->detectBasicintend($message);

        return match ($intend) {

            BasicIntendEnum::FAQ->value => $this->handleFaqQueries($message),

            BasicIntendEnum::ORDER->value => $this->handleOrderQueries($message),

            default => "I'm not sure, Could you please rephrase?"
        };
    }

    private function detectBasicintend($message): ?string
    {
        return $this->detector->detectBasicintend($message);

    }

    private function detectAdvancedintend(BasicIntendEnum $basicIntend, $message): ?string
    {
        return $this->detector->detectAdvancedintend($basicIntend, $message);
    }

    private function handleOrderQueries($message): string
    {
        return 'Please provide your order number to check the status.';
    }

    private function handleFaqQueries($message)
    {
        $intend = $this->detectAdvancedintend(BasicIntendEnum::FAQ, $message);

        if (! $intend) {
            return "I'm not sure about that. Can you ask differently?";
        }

        if (cache()->has('faq_response_'.sha1($intend))) {
            return cache()->get('faq_response_'.sha1($intend));
        }

        $faqs = Faq::whereJsonContains('meta->tags', $intend)->get();

        $answer = null;

        $faqs->each(function ($faq) use (&$answer) {
            $answer .= $faq->answer;
        });

        if (! $answer) {
            return "I'm not sure about that. Can you ask differently?";
        }

        cache()->put('faq_response_'.sha1($message), $answer, now()->addHours(24));

        return $answer;
    }
}
