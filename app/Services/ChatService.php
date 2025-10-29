<?php

namespace App\Services;

use App\Enums\Chat\BasicIntendEnum;
use App\Models\Faq;
use App\Services\Intent\OrderIntentHandler;
use App\Services\Intent\SlotExtractor;

class ChatService
{
    public function __construct(
        private ChatIntendDetectorService $detector
    ) {}

    public function handleMessage($message)
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
        $intend        = $this->detectAdvancedintend(BasicIntendEnum::ORDER, $message);
        $slots         = (new SlotExtractor)->extract($message);
        $intendhandler = new OrderIntentHandler;
        $result        = $intendhandler->handle($intend, $slots);

        return $this->generateResponse($result, $intendhandler);
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
            $answer .= '\n'.$faq->answer;
        });

        if (! $answer) {
            return "I'm not sure about that. Can you ask differently?";
        }

        cache()->put('faq_response_'.sha1($message), $answer, now()->addHours(24));

        return $answer;
    }

    private function generateResponse(array $result, OrderIntentHandler $intendhandler, bool $isAiMode = false): string
    {
        return $result['message'].' : '.PHP_EOL.$intendhandler->dataToString($result['data']);
    }
}
