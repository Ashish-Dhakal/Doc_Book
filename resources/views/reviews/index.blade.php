<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-lg my-8 p-8">

                <h3 class="text-xl font-semibold mb-4">Reviews</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 border-b">User Name</th>
                                    <th class="px-4 py-2 border-b">Comment</th>
                                    <th class="px-4 py-2 border-b">PDF</th>
                                    <th class="px-4 py-2 border-b">Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $review)
                                <tr>
                                    <td class="px-4 py-2 border-b">{{ $review->appointment->patient->user->f_name }} {{ $review->appointment->patient->user->l_name }}</td>
                                    <td class="px-4 py-2 border-b">{{ $review->comment ?? 'No comment' }}</td>
                                    <td class="px-4 py-2 border-b">
                                        @if ($review->pdf)
                                            <a href="{{ asset('storage/' . $review->pdf) }}"
                                                class="text-blue-500 hover:underline" target="_blank">View PDF</a>
                                        @else
                                            No PDF
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 border-b">{{ $review->created_at->format('M d, Y') }}</td>
                                </tr>
                                    
                                @empty
                                <tr class="text-center"><td colspan="4">No reviews found.</td></tr>
                                    
                                @endforelse
                            </tbody>
                        </table>
                    </div>
            </div>
            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
