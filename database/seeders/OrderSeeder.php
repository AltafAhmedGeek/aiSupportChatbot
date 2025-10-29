<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure some users and delivery agents exist
        $customers = User::factory()->count(10)->create(['role' => 'customer']);
        $agents    = User::factory()->count(5)->create(['role' => 'delivery_agent']);

        $statuses       = ['pending', 'confirmed', 'dispatched', 'delivered', 'cancelled', 'refunded'];
        $paymentMethods = ['cod', 'credit_card', 'debit_card', 'wallet', 'upi'];

        foreach (range(1, end: 25) as $i) {
            $user          = $customers->random();
            $agent         = rand(0, 1) ? $agents->random() : null; // Some orders may not have assigned agents yet
            $status        = $statuses[array_rand($statuses)];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            $createdAt     = Carbon::now()->subDays(rand(0, 30));

            $total       = rand(500, 2500);
            $discount    = rand(0, 200);
            $deliveryFee = rand(20, 60);
            $final       = $total - $discount + $deliveryFee;

            $estimatedDelivery = $createdAt->copy()->addHours(rand(2, 48));
            $deliveredAt       = in_array($status, ['delivered', 'refunded']) ? $estimatedDelivery->copy()->addHours(rand(0, 2)) : null;

            $refundReason = $status == 'refunded' ? 'Customer didn\'t receive the product' : null;

            Order::create([
                'user_id'               => $user->id,
                'delivery_agent_id'     => $agent?->id,
                'order_number'          => strtoupper(Str::random(10)),
                'status'                => $status,
                'refund_reason'         => $refundReason,
                'total_amount'          => $total,
                'delivery_fee'          => $deliveryFee,
                'discount_amount'       => $discount,
                'final_amount'          => $final,
                'payment_status'        => in_array($status, ['cancelled', 'refunded']) ? 'refunded' : 'paid',
                'payment_method'        => $paymentMethod,
                'transaction_id'        => 'TXN'.strtoupper(Str::random(8)),
                'delivery_address'      => fake()->streetAddress(),
                'delivery_city'         => fake()->city(),
                'delivery_state'        => fake()->state(),
                'delivery_zip'          => fake()->postcode(),
                'latitude'              => fake()->latitude(18.5, 28.5),
                'longitude'             => fake()->longitude(72.5, 77.5),
                'estimated_delivery_at' => $estimatedDelivery,
                'delivered_at'          => $deliveredAt,
                'meta'                  => [
                    'notes'    => fake()->sentence(),
                    'priority' => fake()->boolean(10) ? 'high' : 'normal',
                ],
                'created_at' => $createdAt,
                'updated_at' => $deliveredAt ?? $createdAt,
            ]);
        }
    }
}
