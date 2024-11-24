<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Appointment Details Container -->
            <div class="bg-white p-8 shadow-lg rounded-lg">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Appointment Details</h2>

                <!-- Appointment Details Table -->
                <table class="min-w-full table-auto border-separate border-spacing-4">
                    <tr>
                        <th class="text-left font-medium text-gray-700">Patient</th>
                        <td class="text-gray-800">{{ $appointment->patient->user->f_name }}
                            {{ $appointment->patient->user->l_name }}</td>
                    </tr>
                    <tr>
                        <th class="text-left font-medium text-gray-700">Doctor</th>
                        <td class="text-gray-800">{{ $appointment->doctor->user->f_name }}
                            {{ $appointment->doctor->user->l_name }}</td>
                    </tr>
                    <tr>
                        <th class="text-left font-medium text-gray-700">Date</th>
                        <td class="text-gray-800">{{ $appointment->date }}</td>
                    </tr>
                    <tr>
                        <th class="text-left font-medium text-gray-700">Time</th>
                        <td class="text-gray-800">{{ $appointment->start_time }}</td>
                    </tr>
                    <tr>
                        <th class="text-left font-medium text-gray-700">Time</th>
                        <td class="text-gray-800">{{ $appointment->end_time }}</td>
                    </tr>
                    <tr>
                        <th class="text-left font-medium text-gray-700">Status</th>
                        <td class="text-gray-800 capitalize">{{ $appointment->status }}</td>
                    </tr>
                    <tr>
                        <th class="text-left font-medium text-gray-700">Review PDF</th>
                        <td class="text-gray-800">
                            @if ($appointment->reviews && $appointment->reviews->isEmpty())
                                No reviews yet.
                            @elseif ($appointment->reviews)
                                @foreach ($appointment->reviews as $review)
                                    <div class="mb-2">
                                        <p><strong>Comment:</strong> {{ $review->comment }}</p>
                                        <p><strong>Submitted on:</strong> {{ $review->created_at->format('M d, Y') }}
                                        </p>

                                        @if ($review->pdf)
                                            <a href="{{ Storage::url($review->pdf) }}" target="_blank"
                                                class="text-blue-600 hover:underline">
                                                View Review PDF
                                            </a>
                                        @else
                                            <p>No PDF uploaded.</p>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                No reviews found.
                            @endif
                        </td>





                    </tr>

                </table>

                <!-- Action Buttons -->
                <div class="mt-6 flex space-x-4">
                    <a href="{{ route('appointments.edit', $appointment->id) }}"
                        class="px-6 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        Edit
                    </a>
                    <a href="{{ route('appointments.index') }}"
                        class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Back to Appointments
                    </a>

                    @can('doctor_access')
                        @if ($appointment->status == 'booked')
                            <button id="leaveReviewButton"
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Leave a Review
                            </button>
                        @endif



                        <form action="{{ route('appointments.updateStatus', $appointment->id) }}" method="POST"
                            class="inline-block ml-2">
                            @csrf
                            @method('POST')
                            <div class="flex items-center space-x-2">

                                <select name="status"
                                    class=" full-width px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
                                    <option value="completed" {{ $appointment->status == 'completed' ? 'selected' : '' }}>
                                        Completed</option>
                                </select>
                                <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200">
                                    Update
                                </button>
                            </div>
                        </form>
                    @endcan
                </div>

            </div>

        </div>
    </div>

    <!-- Modal for Writing Review -->
    <div id="reviewModal"
        class="fixed inset-0 flex items-center justify-center shadow-transparent z-50 bg-gray-800 bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-transparent p-6 w-1/3">
            <h3 class="text-xl font-semibold mb-4">Write a Review</h3>

            <!-- Check if a review exists for this appointment -->
            <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

                <!-- Review Textarea -->
                <textarea name="comment" rows="4" class="w-full p-2 border rounded-md" placeholder="Write your review here...">{{ old('comment') }}</textarea>

                <!-- PDF Upload Field -->
                <div class="mt-4">
                    <label for="pdf" class="block text-sm font-medium text-gray-700">Upload PDF (optional)</label>
                    <input type="file" name="pdf" id="pdf" accept="application/pdf"
                        class="mt-1 block w-full border rounded-md">
                </div>

                <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Submit Review
                </button>
            </form>




            <!-- Close button for the modal -->
            <button id="closeModal"
                class="mt-2 px-4 py-2 bg-gray-400 text-white rounded-md hover:bg-gray-500">Close</button>
        </div>
    </div>

    <script>
        // Show the review modal when the button is clicked
        document.getElementById('leaveReviewButton').addEventListener('click', function() {
            document.getElementById('reviewModal').classList.remove('hidden');
        });

        // Close the modal when the close button is clicked
        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('reviewModal').classList.add('hidden');
        });
    </script>


</x-app-layout>
