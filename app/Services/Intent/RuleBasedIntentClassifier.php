<?php

namespace App\Services\Intent;

class RuleBasedIntentClassifier
{
    /**
     * Return array with keys: intent (string)
     */
    public function classify(string $message): array
    {
        $m = strtolower($message);

        if (preg_match('/(?:cancel|i want to cancel|please cancel)\b.*(?:order|purchase)?/i', $m)) {
            return ['intent' => 'order.cancel'];
        }

        if (preg_match('/(?:where(?:\'s| is)|track|tracking|where is my)\b.*(?:order|package|parcel|shipment)/i', $m)) {
            return ['intent' => 'order.track_location'];
        }

        if (preg_match('/\b(return|refund|money back|i want a refund)\b/i', $m)) {
            return ['intent' => 'order.request_refund'];
        }

        if (preg_match('/\b(status|order status|what is the status)\b/i', $m)) {
            return ['intent' => 'order.status'];
        }

        if (preg_match('/\b(change address|update address|change shipping address|change delivery address)\b/i', $m)) {
            return ['intent' => 'order.update_address'];
        }

        if (preg_match('/\b(deliver(ed|y)?|delivered|mark as delivered|received|delivery confirmation)\b/i', $m)) {
            return ['intent' => 'order.update_delivered'];
        }

        if (preg_match('/\b(estimated delivery|when will.*arrive|expected date|delivery time|when to deliver)\b/i', $m)) {
            return ['intent' => 'order.estimate_delivery'];
        }

        if (preg_match('/\b(payment status|paid|unpaid|pending payment|is my payment received)\b/i', $m)) {
            return ['intent' => 'order.payment_status'];
        }

        if (preg_match('/\b(payment method|how did i pay|pay using|use wallet|change payment method)\b/i', $m)) {
            return ['intent' => 'order.payment_method'];
        }

        if (preg_match('/\b(total amount|discount|final amount|invoice|price details|order amount)\b/i', $m)) {
            return ['intent' => 'order.details_amount'];
        }

        if (preg_match('/\b(agent|delivery agent|who will deliver|courier info|delivery person|delivery boy)\b/i', $m)) {
            return ['intent' => 'order.agent_info'];
        }

        if (preg_match('/\b(note|add note|special instructions|remarks|add message)\b/i', $m)) {
            return ['intent' => 'order.add_note'];
        }

        if (preg_match('/\b(details|detail|information|info)\b/i', $m)) {
            return ['intent' => 'order.details'];
        }

        if (preg_match('/\b(track|tracking|location|where)\b/i', $m)) {
            return ['intent' => 'order.track_location'];
        }

        return ['intent' => 'unknown'];
    }
}
