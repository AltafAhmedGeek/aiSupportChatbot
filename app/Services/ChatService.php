<?php

namespace App\Services;

use App\Enums\Chat\BasicIntendEnum;
use App\Models\Faq;
use App\Services\Intent\OrderIntentHandler;
use App\Services\Intent\SlotExtractor;
use Log;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\ValueObjects\Messages\SystemMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Throwable;

class ChatService
{
    protected ChatIntendDetectorService $detector;

    protected bool $aiMode;

    public function __construct(
        bool $aiMode = false
    ) {
        $this->aiMode   = $aiMode;
        $this->detector = app(ChatIntendDetectorService::class);
    }

    public function handleMessage($message): string
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
        return $this->detector->detectBasicintend($message, $this->aiMode);
    }

    private function detectAdvancedintend(BasicIntendEnum $basicIntend, $message): ?string
    {
        return $this->detector->detectAdvancedintend($basicIntend, $message, $this->aiMode);
    }

    private function handleOrderQueries($message): string
    {
        $intend        = $this->detectAdvancedintend(BasicIntendEnum::ORDER, $message);
        $slots         = (new SlotExtractor)->extract($message);
        $intendhandler = new OrderIntentHandler;

        if (! $intend) {
            return "I'm not sure about that. Can you ask differently?";
        }

        $result = $intendhandler->handle($intend, slots: $slots);

        return $this->generateResponse($result, $intendhandler, $this->aiMode);
    }

    private function handleFaqQueries($message): string
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
        return match ($isAiMode) {
            false => $result['message'].' : '.PHP_EOL.$intendhandler->dataToString($result['data']),
            true  => $this->generateAiResponse($result, $intendhandler),
        };
    }

    private function generateAiResponse(array $result, OrderIntentHandler $intendhandler): string
    {
        try {

            $response = Prism::text()
                ->using(Provider::Gemini, 'gemini-2.5-flash')
                ->withSystemPrompt(new SystemMessage(config('app.prism.system_prompts.ai_response_generation')))
                ->withMessages([
                    new UserMessage('Generate a customer friendly response based on the following data: '.PHP_EOL.$intendhandler->dataToString($result['data']).PHP_EOL.' and message: '.$result['message']),
                ])
                ->asText();

            $response = trim($response->text);

            if (empty($response)) {
                return $this->generateResponse($result, $intendhandler);
            }

            Log::info('AI-generated response: '.$response);

            return $response;

        } catch (Throwable $th) {

            Log::error('Error generating AI response, responding with generic response. Error : '.$th->getMessage());

            report($th);

            return $this->generateResponse($result, $intendhandler);
        }
    }
}
