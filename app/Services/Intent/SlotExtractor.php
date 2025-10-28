<?php

namespace App\Services\Intent;

class SlotExtractor
{
    /**
     * Extract only: order_number, refund_reason, transaction_id
     */
    public function extract(string $message): array
    {
        $m = strtolower($message);
        $slots = [];

        // order_number
        if (preg_match('/order[\\s\\-#:=>]*(?:(?:num(?:ber)?|id)[\\s#:]*)?(?:is[\\s]*)?([A-Za-z0-9]{3,12})/i', $m, $matches)) {
            $slots['order_number'] = $matches[1];
        } elseif (preg_match('/#([a-z|0-9]{5,12})\b/', $m, $matches)) {
            $slots['order_number'] = $matches[1];
        }

        // transaction_id
        if (preg_match('/\\b(txn[a-zA-Z0-9]{3,})\\b/i', $m, $matches)) {
            $slots['transaction_id'] = $matches[1];
        }

        // refund_reason
        $patterns = [
            '/refund[\\s_-]*reason[\\s#:=>]*?(?:is|was)?[\\s]*([a-zA-Z0-9 ,\\-]+)/i',
            '/refund[\\s_-]*because(?: of| to)?[\\s]*([a-zA-Z0-9 ,\\-]+)/i',
            '/refund due to[\\s]*([a-zA-Z0-9 ,\\-]+)/i',
            '/refund for [A-Za-z0-9#\\s]+(?:because|due to|because of|because to)[\\s]*([a-zA-Z0-9 ,\\-]+)/i',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $m, $matches)) {
                $slots['refund_reason'] = trim($matches[1]);
                break;
            }
        }

        return array_filter($slots, function ($v) {
            return ! empty(trim((string) $v));
        });
    }
}
