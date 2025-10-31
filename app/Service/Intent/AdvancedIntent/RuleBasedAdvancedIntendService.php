<?php

namespace App\Service\Intent\AdvancedIntent;

use App\Constants\Faq;
use App\Contracts\Intent\AdvancedIntendDetector;
use App\Enums\Chat\BasicIntendEnum;
use App\Services\Intent\RuleBasedIntentClassifier;
use Throwable;

class RuleBasedAdvancedIntendService implements AdvancedIntendDetector
{
    public function detectAdvancedintend(BasicIntendEnum $basicIntendEnum, string $message): ?string
    {
        return match ($basicIntendEnum) {
            BasicIntendEnum::FAQ   => $this->detectFaqIntent($message),
            BasicIntendEnum::ORDER => $this->detectOrderIntent($message),
        };
    }

    protected function detectFaqIntent(string $message): ?string
    {
        try {
            $tags = Faq::TAGS;

            foreach ($tags as $keyword => $tag) {

                if (str_contains(strtolower($message), strtolower($keyword))) {

                    return $tag;
                }
            }

            return null;

        } catch (Throwable $th) {

            report($th);

            return null;
        }
    }

    protected function detectOrderIntent(string $message): ?string
    {
        try {
            $classifier = new RuleBasedIntentClassifier;

            $result = $classifier->classify($message);

            if ($result['intent'] !== 'unknown') {
                return $result['intent'];
            }

            return null;

        } catch (Throwable $th) {

            report($th);

            return null;
        }
    }
}
