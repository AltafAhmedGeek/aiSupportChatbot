<?php

namespace App\Services\Intent;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderIntentHandler
{
    /**
     * Handle intent with slots and optional user identifier
     *
     * @param  mixed  $user
     * @return array [status=> 'done'|'clarify'|'error'|'not_found'|'conflict'|'unavailable'|'noop', message=>string, data=>array]
     */
    public function handle(string $intent, array $slots = []): array
    {
        return match ($intent) {
            'order.track_location' => $this->trackLocation($slots),
            'order.cancel' => $this->cancel($slots),
            'order.request_refund' => $this->requestRefund($slots),
            'order.status' => $this->status($slots),
            'order.update_delivered' => $this->updateDelivered($slots),
            'order.estimate_delivery' => $this->estimateDelivery($slots),
            'order.payment_status' => $this->paymentStatus($slots),
            'order.payment_method' => $this->paymentMethod($slots),
            'order.details_amount' => $this->detailsAmount($slots),
            'order.details' => $this->status($slots),
            'order.agent_info' => $this->agentInfo($slots),
            default => ['status' => 'error', 'message' => 'Unsupported order intent', 'data' => []],
        };
    }

    protected function findOrderBySlots(array $slots): ?Order
    {
        if (! empty($slots['order_number'])) {
            return Order::where('order_number', $slots['order_number'])->first();
        }

        if (! empty($slots['transaction_id'])) {
            return Order::where('transaction_id', $slots['transaction_id'])->first();
        }

        return null;
    }

    protected function trackLocation(array $slots): array
    {
        if (empty($slots['order_number']) && empty($slots['transaction_id'])) {
            return ['status' => 'clarify', 'message' => 'Provide order_number or transaction_id to track location.', 'data' => []];
        }

        $order = $this->findOrderBySlots($slots);
        if (! $order) {
            return ['status' => 'not_found', 'message' => 'Order not found for tracking, Enter Order number eg.#GHJ56GHNM.', 'data' => []];
        }

        $data = [
            'order_number' => $order->order_number,
            'status' => $order->status ?? null,
            'tracking_number' => $order->tracking_number ?? null,
            'carrier' => $order->carrier ?? null,
            'current_location' => $order->current_location ?? null,
            'eta' => $order->eta ?? null,
        ];

        return ['status' => 'done', 'message' => 'Tracking info fetched.', 'data' => $data];
    }

    protected function cancel(array $slots): array
    {
        if (empty($slots['order_number']) && empty($slots['transaction_id'])) {
            return ['status' => 'clarify', 'message' => 'Provide order_number to cancel the order.', 'data' => []];
        }

        $order = $this->findOrderBySlots($slots);
        if (! $order) {
            return ['status' => 'not_found', 'message' => 'Order not found for tracking, Enter Order number eg.#GHJ56GHNM.', 'data' => []];
        }

        $lower = strtolower((string) ($order->status ?? ''));
        if (in_array($lower, ['shipped', 'delivered', 'cancelled'], true)) {
            return ['status' => 'conflict', 'message' => 'Order cannot be cancelled at its current status.', 'data' => ['status' => $order->status]];
        }

        $order->status = 'cancelled';
        $order->save();
        Log::info('Order cancelled', ['order_number' => $order->order_number]);

        return ['status' => 'done', 'message' => 'Order cancelled.', 'data' => []];
    }

    protected function requestRefund(array $slots): array
    {
        if (empty($slots['order_number']) && empty($slots['transaction_id'])) {
            return ['status' => 'clarify', 'message' => 'Provide order_number to request a refund.', 'data' => []];
        }

        $order = $this->findOrderBySlots($slots);
        if (! $order) {
            return ['status' => 'not_found', 'message' => 'Order not found for tracking, Enter Order number eg.#GHJ56GHNM.', 'data' => []];
        }

        $reason = $slots['refund_reason'] ?? null;
        $order->status = 'refund requested';
        if ($reason !== null) {
            $order->refund_reason = $reason;
        }

        if (! empty($slots['transaction_id'])) {
            $order->refund_reference = $slots['transaction_id'];
        }
        $order->save();

        return [
            'status' => 'done',
            'message' => 'Refund request recorded.',
            'data' => [
                'order_number' => $order->order_number,
                'refund_reason' => $order->refund_reason ?? null,
            ],
        ];
    }

    protected function status(array $slots): array
    {
        if (empty($slots['order_number']) && empty($slots['transaction_id'])) {
            return ['status' => 'clarify', 'message' => 'Provide order_number to get status.', 'data' => []];
        }

        $order = $this->findOrderBySlots($slots);
        if (! $order) {
            return ['status' => 'not_found', 'message' => 'Order not found for tracking, Enter Order number eg.#GHJ56GHNM.', 'data' => []];
        }

        return [
            'status' => 'done',
            'message' => "Order status: {$order->status}",
            'data' => [
                'order_details' => $order->only([
                    'order_number',
                    'status',
                    'tracking_number',
                    'carrier',
                    'current_location',
                    'eta',
                    'payment_status',
                    'total',
                ]),
            ],
        ];
    }

    protected function updateDelivered(array $slots): array
    {
        if (empty($slots['order_number']) && empty($slots['transaction_id'])) {
            return ['status' => 'clarify', 'message' => 'Provide order_number to mark delivered.', 'data' => []];
        }

        $order = $this->findOrderBySlots($slots);
        if (! $order) {
            return ['status' => 'not_found', 'message' => 'Order not found for tracking, Enter Order number eg.#GHJ56GHNM.', 'data' => []];
        }

        if (strtolower((string) $order->status) === 'delivered') {
            return ['status' => 'noop', 'message' => 'Order already delivered.', 'data' => ['order_number' => $order->order_number]];
        }

        $deliveredAt = $slots['delivered_at'] ?? now()->toIso8601String();
        $order->status = 'delivered';
        $order->delivered_at = $deliveredAt;
        $order->save();

        return ['status' => 'done', 'message' => 'Order marked delivered.', 'data' => ['order' => $order->toArray()]];
    }

    protected function estimateDelivery(array $slots): array
    {
        if (empty($slots['order_number']) && empty($slots['transaction_id'])) {
            return ['status' => 'clarify', 'message' => 'Provide order_number to estimate delivery.', 'data' => []];
        }

        $order = $this->findOrderBySlots($slots);
        if (! $order) {
            return ['status' => 'not_found', 'message' => 'Order not found for tracking, Enter Order number eg.#GHJ56GHNM.', 'data' => []];
        }

        $eta = $order->eta ?? null;
        if (! $eta) {
            return ['status' => 'unavailable', 'message' => 'ETA not available for this order.', 'data' => ['order_number' => $order->order_number]];
        }

        return ['status' => 'done', 'message' => 'ETA fetched.', 'data' => ['order_number' => $order->order_number, 'eta' => $eta]];
    }

    protected function paymentStatus(array $slots): array
    {
        if (empty($slots['order_number']) && empty($slots['transaction_id'])) {
            return ['status' => 'clarify', 'message' => 'Provide order_number to fetch payment status.', 'data' => []];
        }

        $order = $this->findOrderBySlots($slots);
        if (! $order) {
            return ['status' => 'not_found', 'message' => 'Order not found for tracking, Enter Order number eg.#GHJ56GHNM.', 'data' => []];
        }

        $data = [
            'order_number' => $order->order_number,
            'payment_status' => $order->payment_status ?? 'unknown',
            'paid_amount' => $order->paid_amount ?? 0,
            'currency' => $order->currency ?? null,
            'attempts' => $order->payment_attempts ?? 0,
            'gateway' => $order->payment_gateway ?? null,
            'transactions' => $order->payment_transactions ?? [],
            'transaction_id' => $slots['transaction_id'] ?? null,
        ];

        return ['status' => 'done', 'message' => 'Payment status fetched.', 'data' => $data];
    }

    protected function paymentMethod(array $slots): array
    {
        if (empty($slots['order_number']) && empty($slots['transaction_id'])) {
            return ['status' => 'clarify', 'message' => 'Provide order_number to fetch payment method.', 'data' => []];
        }

        $order = $this->findOrderBySlots($slots);
        if (! $order) {
            return ['status' => 'not_found', 'message' => 'Order not found for tracking, Enter Order number eg.#GHJ56GHNM.', 'data' => []];
        }

        $data = [
            'order_number' => $order->order_number,
            'method' => $order->payment_method ?? null,
            'brand' => $order->card_brand ?? null,
            'last4' => $order->card_last4 ?? null,
            'upi' => $order->upi ?? null,
            'wallet' => $order->wallet ?? null,
        ];

        return ['status' => 'done', 'message' => 'Payment method fetched.', 'data' => $data];
    }

    protected function detailsAmount(array $slots): array
    {
        if (empty($slots['order_number']) && empty($slots['transaction_id'])) {
            return ['status' => 'clarify', 'message' => 'Provide order_number to fetch amount details.', 'data' => []];
        }

        $order = $this->findOrderBySlots($slots);
        if (! $order) {
            return ['status' => 'not_found', 'message' => 'Order not found for tracking, Enter Order number eg.#GHJ56GHNM.', 'data' => []];
        }

        $data = [
            'order_number' => $order->order_number,
            'subtotal' => $order->subtotal ?? 0,
            'tax' => $order->tax ?? 0,
            'discount' => $order->discount ?? 0,
            'shipping' => $order->shipping ?? 0,
            'total' => $order->total ?? 0,
            'currency' => $order->currency ?? null,
            'items' => $order->items ?? [],
        ];

        return ['status' => 'done', 'message' => 'Amount details fetched.', 'data' => $data];
    }
    
    protected function agentInfo(array $slots): array
    {
        if (empty($slots['order_number']) && empty($slots['transaction_id'])) {
            return ['status' => 'clarify', 'message' => 'Provide order_number to fetch agent info.', 'data' => []];
        }

        $order = $this->findOrderBySlots($slots);
        if (! $order) {
            return ['status' => 'not_found', 'message' => 'Order not found for tracking, Enter Order number eg.#GHJ56GHNM.', 'data' => []];
        }

        $data = [
            'order_number' => $order->order_number,
            'agent_name' => $order->agent_name ?? null,
            'agent_phone' => $order->agent_phone ?? null,
            'agent_vehicle' => $order->agent_vehicle ?? null,
            'agent_rating' => $order->agent_rating ?? null,
            'last_location' => $order->current_location ?? null,
        ];

        return ['status' => 'done', 'message' => 'Agent info fetched.', 'data' => $data];
    }

    public function dataToString(array $data): string
    {
        $keyValuePairs = [];
        foreach ($data as $key => $value) {
            $key = str_replace('_', ' ', ucfirst($key));
            if ($value) {
                $value = is_array($value) ? $this->dataToString($value) : ucfirst($value);
            } else {
                $value = 'N/A';
            }
            $keyValuePairs[] = " $key is $value ";
        }

        $resultString = implode('&', $keyValuePairs);

        return $resultString;
    }
}
