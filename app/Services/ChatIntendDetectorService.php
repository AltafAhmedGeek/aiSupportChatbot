<?php

namespace App\Services;

use App\Constants\Faq;
use App\Enums\Chat\BasicIntendEnum;
use App\Services\Intent\RuleBasedIntentClassifier;

class ChatIntendDetectorService
{
    public function detectBasicintend($message): ?string
    {
        $classifier = new RuleBasedIntentClassifier;
        $result     = $classifier->classify($message);

        if (str_starts_with($result['intent'], BasicIntendEnum::ORDER->value)) {
            return BasicIntendEnum::ORDER->value;
        }

        if ($this->isFaqRelated(strtolower($message))) {
            return BasicIntendEnum::FAQ->value;
        }

        return null;
    }

    public function detectAdvancedintend(BasicIntendEnum $intend, $message): ?string
    {
        if ($intend == BasicIntendEnum::FAQ) {
            $tags = Faq::TAGS;
            foreach ($tags as $keyword => $tag) {
                if (str_contains(strtolower($message), strtolower($keyword))) {
                    return $tag;
                }
            }
        }

        if ($intend == BasicIntendEnum::ORDER) {
            $classifier = new RuleBasedIntentClassifier;
            $result     = $classifier->classify($message);
            if ($result['intent'] !== 'unknown') {
                return $result['intent'];
            }
        }

        return null;
    }

    private function isFaqRelated($message): bool
    {
        $keywords = Faq::TAGS;

        foreach ($keywords as $key => $keyword) {
            if (str_contains($message, strtolower($key))) {
                return true;
            }
        }

        return false;
    }
}
