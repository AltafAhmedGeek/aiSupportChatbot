<?php

namespace App\Service\Intent\AdvancedIntent;

use App\Constants\Faq;
use App\Contracts\Intent\AdvancedIntendDetector;
use App\Enums\Chat\BasicIntendEnum;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\ValueObjects\Messages\SystemMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Throwable;

class AiBasedAdvancedIntendService implements AdvancedIntendDetector
{
    public function detectAdvancedintend(BasicIntendEnum $basicIntendEnum, string $message): ?string
    {
        // AI-based intent detection logic to be implemented here
        return match ($basicIntendEnum) {
            BasicIntendEnum::ORDER => $this->detectOrderIntent($message),
            BasicIntendEnum::FAQ   => $this->detectFaqIntent($message),
        };
    }

    protected function detectOrderIntent(string $message): ?string
    {
        try {
            $response = Prism::text()
                ->using(Provider::Gemini, 'gemini-2.5-flash')
                ->withSystemPrompt(new SystemMessage(config('app.prism.system_prompts.advanced_order_intent_detection')))
                ->withMessages([
                    new UserMessage($message),
                ])
                ->asText();

            Log::info('AI-based Order intent detection for message '.$message.'. Response: '.$response->text);

            $intend = trim($response->text);

            if ($intend === 'unknown' || empty($intend)) {
                return null;
            }

            return $intend;

        } catch (Throwable $th) {

            Log::error('Error occurred during AI-based Order intent detection for message '.$message.': '.$th->getMessage());

            report($th);

            return null;
        }
    }

    protected function detectFaqIntent(string $message): ?string
    {
        try {
            $response = Prism::text()
                ->using(Provider::Gemini, 'gemini-2.5-flash')
                ->withSystemPrompt(new SystemMessage(config('app.prism.system_prompts.advanced_faq_intent_detection')))
                ->withMessages([
                    new UserMessage($message),
                ])
                ->asText();

            Log::info('AI-based FAQ intent detection for message '.$message.'. Response: '.$response->text);

            $intend = trim($response->text);

            if (! $intend || ! in_array($intend, Faq::TAGS)) {
                return null;
            }

            return $intend;

        } catch (Throwable $th) {

            Log::error('Error occurred during AI-based FAQ intent detection for message '.$message.': '.$th->getMessage());

            report($th);

            return null;
        }
    }
}
