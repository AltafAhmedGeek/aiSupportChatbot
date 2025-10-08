<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Orders') }}
        </h2>
    </x-slot>

    < <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Orders Overview</h3>
                </div>

                {{-- Table --}}
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="mx-auto text-sm text-left text-gray-800 dark:text-gray-200">
                            <thead>
                                <tr
                                    class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 uppercase text-xs tracking-wider">
                                    <th class="px-6 py-4 font-semibold">Order #</th>
                                    <th class="px-6 py-4 font-semibold">Customer</th>
                                    <th class="px-6 py-4 font-semibold">Status</th>
                                    <th class="px-6 py-4 font-semibold">Total</th>
                                    <th class="px-6 py-4 font-semibold">Payment</th>
                                    <th class="px-6 py-4 font-semibold">Created</th>
                                    <th class="px-6 py-4 text-right font-semibold">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($orders as $order)
                                    <tr class="group hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-150 ease-in-out cursor-pointer rounded-lg"
                                        onclick="window.location='{{ route('orders.show', $order->id) }}'">

                                        <td
                                            class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100 group-hover:translate-x-1 transition-all">
                                            #{{ $order->order_number }}
                                        </td>

                                        <td class="px-6 py-4">{{ $order->user->name ?? 'N/A' }}</td>

                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                                    @if($order->status == 'delivered') bg-green-100 text-green-800
                                                    @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                                    @else bg-blue-100 text-blue-800 @endif">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">
                                            ₹{{ number_format($order->final_amount, 2) }}
                                        </td>

                                        <td class="px-6 py-4 capitalize">
                                            {{ $order->payment_status }}
                                        </td>

                                        <td class="px-6 py-4 text-gray-500 text-xs">
                                            {{ $order->created_at->format('d M Y, h:i A') }}
                                        </td>

                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('orders.show', $order->id) }}"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 text-sm font-medium">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7"
                                            class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                                            No orders found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
        </div>
</x-app-layout>