<?php

namespace App\Services;

use App\Constants\Faq;
use App\Enums\Chat\BasicIntendEnum;

class ChatIntendDetectorService
{
    public function detectBasicintend($message): ?string
    {
        $message = strtolower($message);
        if ($this->isFaqRelated($message)) {
            return 'faq';
        } elseif ($this->isOrderRelated($message)) {
            return 'order';
        } else {
            return null;
        }
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
        } elseif ($intend == BasicIntendEnum::ORDER) {
            $keywords = ['cancel', 'track', 'status', 'return', 'refund'];
            foreach ($keywords as $keyword) {
                if (str_contains(strtolower($message), strtolower($keyword))) {
                    return $keyword;
                }
            }
        }

        return null;
    }

    private function isOrderRelated($message): bool
    {
        $keywords = ['order', 'status', 'track', 'tracking', 'delivery', 'shipment', 'shipped', 'arrived', 'where is my order', 'cancel order', 'return order'];
        foreach ($keywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function isFaqRelated($message): bool
    {
        $keywords = ['how to', 'what is', 'faq', 'question', 'help', 'support', 'contact', 'customer care', 'payment', 'refund', 'return', 'cancel', 'track order', 'where is my order'];
        foreach ($keywords as $keyword) {
            if (str_contains($message, strtolower($keyword))) {
                return true;
            }
        }

        return false;
    }
}
