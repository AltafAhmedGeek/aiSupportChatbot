<?php

namespace App\Services;

use App\Constants\Faq;
use App\Enums\Chat\BasicIntendEnum;
use App\Service\Intent\AdvancedIntentManager;
use App\Services\Intent\RuleBasedIntentClassifier;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\ValueObjects\Messages\SystemMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Throwable;

class ChatIntendDetectorService
{
    public function detectBasicintend(string $message, bool $aiMode = false): ?string
    {
        if ($aiMode) {

            $system = config('app.prism.system_prompts.intend_detection');

            $systemMessage = new SystemMessage($system);

            $response = Prism::text()
                ->using(Provider::Gemini, 'gemini-2.5-flash')
                ->withSystemPrompt($systemMessage)
                ->withMessages([
                    new UserMessage($message),
                ])
                ->asText();

            $intend = trim(strtolower($response->text));

            Log::info('Detecting intent using AI for message: '.$message.'. Detected intent: '.$intend);

            if (in_array($intend, [BasicIntendEnum::ORDER->value, BasicIntendEnum::FAQ->value])) {
                return $intend;
            }
        }

        if ($this->isOrderRelated($message)) {
            return BasicIntendEnum::ORDER->value;
        }

        if ($this->isFaqRelated($message)) {
            return BasicIntendEnum::FAQ->value;
        }

        return null;
    }

    public function detectAdvancedintend(BasicIntendEnum $intend, string $message, bool $aiMode): ?string
    {
        $detector = app(AdvancedIntentManager::class)->getIntentDetector($aiMode);

        return $detector->detectAdvancedintend($intend, $message);
    }

    private function isFaqRelated($message): bool
    {
        $keywords = Faq::TAGS;

        $message = strtolower($message);

        foreach ($keywords as $key => $keyword) {
            if (str_contains($message, strtolower($key))) {
                return true;
            }
        }

        return false;
    }

    private function isOrderRelated(string $message): bool
    {
        try {
            $message = strtolower($message);

            $classifier = new RuleBasedIntentClassifier;

            $result = $classifier->classify($message);

            return str_starts_with($result['intent'], BasicIntendEnum::ORDER->value);

        } catch (Throwable $th) {

            report($th);

            return false;
        }
    }
}
