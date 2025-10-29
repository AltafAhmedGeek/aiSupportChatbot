<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getSampleData() ?? [] as $faq) {
            Faq::create($faq);
        }
    }

    private function getSampleData(): array
    {
        return [
            [
                'question' => 'How can I track my order?',
                'answer'   => 'You can track your order using the tracking link sent to your registered email or by visiting the “My Orders” section in your account dashboard.',
                'meta'     => ['tags' => ['track order', 'order status', 'delivery tracking']],
            ],
            [
                'question' => 'What should I do if my order is delayed?',
                'answer'   => 'If your order is delayed beyond the estimated delivery date, please contact our support team or check if the delivery agent has updated the latest status.',
                'meta'     => ['tags' => ['late delivery', 'delay', 'where is my order']],
            ],
            [
                'question' => 'How can I cancel my order?',
                'answer'   => 'Orders can be canceled from the “My Orders” page before they are dispatched. Once dispatched, cancellation requests must be approved by support.',
                'meta'     => ['tags' => ['cancel order', 'stop order', 'cancel request']],
            ],
            [
                'question' => 'How do I apply a discount coupon?',
                'answer'   => 'You can apply valid discount coupons at checkout before completing your payment. Coupons cannot be added after placing the order.',
                'meta'     => ['tags' => ['discount code', 'coupon', 'promo']],
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer'   => 'We accept Credit/Debit Cards, UPI, Wallets, and Cash on Delivery (COD) for most orders.',
                'meta'     => ['tags' => ['payment options', 'card payment', 'UPI', 'COD']],
            ],
            [
                'question' => 'I was charged but my order did not go through. What should I do?',
                'answer'   => 'In rare cases, if payment is deducted but the order is not confirmed, the amount is automatically refunded within 3–5 business days.',
                'meta'     => ['tags' => ['payment failed', 'refund', 'money deducted']],
            ],
            [
                'question' => 'Can I change my delivery address after placing an order?',
                'answer'   => 'Yes, you can update your delivery address before the order is dispatched by contacting customer support.',
                'meta'     => ['tags' => ['change address', 'edit address', 'update delivery']],
            ],
            [
                'question' => 'How do I request a refund?',
                'answer'   => 'Refunds can be requested from your order details page once the product is marked as “returned” or “cancelled.” Refunds take 5–7 business days to process.',
                'meta'     => ['tags' => ['refund', 'money back', 'return']],
            ],
            [
                'question' => 'Do you offer international shipping?',
                'answer'   => 'Currently, we only deliver within India. International shipping will be available soon.',
                'meta'     => ['tags' => ['international shipping', 'outside India']],
            ],
            [
                'question' => 'How can I contact customer support?',
                'answer'   => 'You can reach our support team via the chat option on the website or by emailing support@example.com.',
                'meta'     => ['tags' => ['contact', 'support', 'help', 'customer care']],
            ],
        ];
    }
}
