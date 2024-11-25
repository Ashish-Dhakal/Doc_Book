<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Validate the request
        $validated = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'comment' => 'required|string|max:1000',
            'pdf' => 'nullable|mimes:pdf|max:10240',
        ]);

        // Handle PDF upload if it exists
        $pdfPath = null;
        if ($request->hasFile('pdf')) {
            if ($request->file('pdf')->isValid()) {
                $pdfPath = $request->file('pdf')->store('reviews/pdfs', 'public');
            } else {
                \Log::error('PDF upload failed: Invalid file or error during upload');
                return redirect()->back()->with('error', 'The uploaded file is invalid.');
            }
        }

        // Create a new review and associate it with the appointment
        try {
            $review = new Review();
            $review->appointment_id = $request->appointment_id;
            $review->comment = $request->comment;
            if ($pdfPath) {
                $review->pdf = $pdfPath;
            }
            $review->save();

            return redirect()->route('appointments.show', $request->appointment_id)->with('success', 'Review submitted successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to save review: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to save review. Please try again.');
        }
    }





    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        //
    }
}
