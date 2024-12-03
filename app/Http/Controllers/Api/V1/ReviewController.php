<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Doctor;
use App\Models\Review;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\V1\BaseController;
use Illuminate\Support\Facades\Validator;


class ReviewController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::user()->id;
        $doctor = Doctor::where('user_id', $userId)->first();
        $patient = Patient::where('user_id', $userId)->first();

        if ($doctor) {
            $appointments = Appointment::where('doctor_id', $doctor->id)->get();
            $appointmentIds = $appointments->pluck('id'); 

            $reviews = Review::with('appointment')->whereIn('appointment_id', $appointmentIds)->paginate(5);
        } elseif ($patient) {
            $appointments = Appointment::where('patient_id', $patient->id)->get();
            $appointmentIds = $appointments->pluck('id');
            $reviews = Review::with('appointment')->whereIn('appointment_id', $appointmentIds)->paginate(5);
        }
        // return view('reviews.index', compact('reviews'));

        return $this->successResponse($reviews, 'Reviews retrieved successfully.');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Validate the request
        $validateReview = Validator::make($request->all(), [
            'appointment_id' => 'required|exists:appointments,id',
            'comment' => 'required|string|max:1000',
            'pdf' => 'nullable|mimes:pdf|max:10240',
        ]);

        if ($validateReview->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validateReview->errors()->all(),
            ], 422); // 422 is commonly used for validation errors
        }

        // Handle PDF upload if it exists
        $pdfPath = null;
        if ($request->hasFile('pdf')) {
            if ($request->file('pdf')->isValid()) {
                $pdfPath = $request->file('pdf')->store('reviews/pdfs', 'public');
            } else {
                return $this->errorResponse('The uploaded file is invalid.');
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

     

            return $this->successResponse($review, 'Review submitted successfully.');

        } catch (\Exception $e) {
  
            return $this->errorResponse('Failed to save review. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
