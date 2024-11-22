<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

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
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'comment' => 'required|string|max:1000',
        ]);

        // Check if a review already exists for this appointment
        $review = Review::where('appointment_id', $request->appointment_id)->first();

        if ($review) {
            // Update the existing review
            $review->comment = $request->comment;
            $review->save();
            $message = 'Review updated successfully!';
        } else {
            // Create a new review
            $review = new Review();
            $review->appointment_id = $request->appointment_id;
            $review->comment = $request->comment;
            $review->save();
            $message = 'Review submitted successfully!';
        }

        // Redirect back to the appointment page with success message
        return redirect()->route('appointments.show', $request->appointment_id)->with('success', $message);
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
