<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Add content for the payment list here -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Check if there are any payments -->
                    {{-- @if ($payments->isEmpty())
                        <div class="alert alert-warning">No payments found.</div>
                    @else --}}
                        <!-- Payments table -->
                        <table class="table-auto w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-100 border-b">
                                    <th class="px-4 py-2 text-left">ID</th>
                                    <th class="px-4 py-2 text-left">Patient Name</th>
                                    <th class="px-4 py-2 text-left">Amount</th>
                                    <th class="px-4 py-2 text-left">Payment Type</th>
                                    <th class="px-4 py-2 text-left">Payment Status</th>
                                    <th class="px-4 py-2 text-left">Created At</th>
                                    <th class="px-4 py-2 text-left">Action</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)

                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $payment->id }}</td>
                                    <td class="px-4 py-2">{{ $payment->patient->user->f_name ?? 'N/A' }} {{ $payment->patient->user->l_name ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $payment->amount }}</td>
                                    <td class="px-4 py-2">{{ ucfirst($payment->payment_type) }}</td>
                                    <td class="px-4 py-2">{{ ucfirst($payment->payment_status) }}</td>
                                    <td class="px-4 py-2">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-2">
                                        <a href=""
                                            class="text-indigo-600 hover:text-indigo-800 transition duration-200">Pay</a>
                                    </td>
                                </tr>
                                    
                                @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-2 text-center">No payments found.</td>
                                </tr>
                                    
                                @endforelse
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
