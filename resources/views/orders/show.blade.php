<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Order Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg transition-all duration-300">
                
                {{-- Header --}}
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-2 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-2xl font-semibold">Order #{{ $order->order_number }}</h3>
                    <p class="text-sm text-gray-500">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
                </div>

                {{-- Order Details --}}
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                        @php
                            $details = [
                                'Status' => ucfirst($order->status),
                                'Customer' => $order->user->name ?? 'N/A',
                                'Delivery Agent' => $order->deliveryAgent->name ?? 'Not Assigned',
                                'Address' => $order->delivery_address . ', ' . $order->delivery_city,
                                'Total' => '₹' . number_format($order->total_amount, 2),
                                'Discount' => '₹' . number_format($order->discount_amount, 2),
                                'Delivery Fee' => '₹' . number_format($order->delivery_fee, 2),
                                'Final Amount' => '₹' . number_format($order->final_amount, 2),
                                'Payment Method' => strtoupper($order->payment_method),
                                'Payment Status' => ucfirst($order->payment_status),
                                'Estimated Delivery' => optional($order->estimated_delivery_at)->format('d M Y, h:i A') ?? 'N/A',
                                'Delivered At' => optional($order->delivered_at)->format('d M Y, h:i A') ?? 'N/A',
                                'Notes' => $order->meta['notes'] ?? '-',
                            ];
                        @endphp

                        @foreach($details as $label => $value)
                            <div class="flex justify-between items-center text-sm border-b border-dotted border-gray-400 py-2 
                                        hover:bg-gray-100 dark:hover:bg-gray-700 hover:scale-[1.01] transition-all duration-150 ease-in-out rounded-md px-2">
                                <span class="font-medium text-gray-700 dark:text-gray-300 w-1/2">{{ $label }}</span>
                                <span class="text-right text-gray-900 dark:text-gray-100 w-1/2 break-words">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Back link --}}
                <div class="p-6">
                    <a href="{{ route('orders.index') }}"
                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 font-medium">
                        ← Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
