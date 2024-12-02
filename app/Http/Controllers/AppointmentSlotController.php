<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Models\AppointmentSlot;
use Illuminate\Support\Facades\Auth;

class AppointmentSlotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->roles == 'doctor') {
            $userId = Auth::user()->id;
            $doctor = Doctor::where('user_id', $userId)->first();
            $appointmentSlots = AppointmentSlot::where('doctor_id', $doctor->id)->paginate(5);
        } else {
            $appointmentSlots=AppointmentSlot::paginate(5);
        }
        return view('appointment-slots.index', compact('appointmentSlots'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $doctors = Doctor::all();
        return view('appointment-slots.create', compact('doctors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'doctor_id' => 'required',
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'required',
        ]);

        // Get the input times (24-hour format)
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');

        // Convert the times to Carbon instances (don't format to 12-hour yet)
        $startTimeCarbon = Carbon::createFromFormat('H:i', $startTime);
        $endTimeCarbon = Carbon::createFromFormat('H:i', $endTime);


        // Check if end time is before start time
        if ($endTimeCarbon->lt($startTimeCarbon)) {
            // If end time is earlier than start time, return an error response
            // return redirect()->back()->withErrors(['end_time' => 'End time should be after start time.']);
            return redirect()->back()->with('error', 'End time should be after start time.');
        }

        $conflictingSchedule = AppointmentSlot::where('doctor_id', $request->input('doctor_id'))
            ->whereDate('date', $request->input('date')) // Check for the same date
            ->where(function ($query) use ($startTime, $endTime) {
                // Check for overlaps:
                $query->whereBetween('start_time', [$startTime, $endTime])  // Existing start_time within the new time range
                    ->orWhereBetween('end_time', [$startTime, $endTime])    // Existing end_time within the new time range
                    ->orWhere(function ($query) use ($startTime, $endTime) {
                        // Full overlap: Existing schedule fully covers the new time range
                        $query->where('start_time', '<', $startTime)
                            ->where('end_time', '>', $endTime);
                    });
            })
            ->exists(); // Check if any conflict exists

        // If there's a conflict, return error response
        if ($conflictingSchedule) {
            // return response()->json([
            //     'error' => 'The requested time slot conflicts with an existing schedule for this doctor.',
            // ], 400); // Return a 400 Bad Request response with an error message
            return redirect()->back()->with('error', 'The requested time slot conflicts with an existing schedule for this doctor.');
        }

        $appointmentSlot = new AppointmentSlot();
        $appointmentSlot->doctor_id = $request->input('doctor_id');
        $appointmentSlot->date = $request->input('date');
        $appointmentSlot->start_time = $request->input('start_time');
        $appointmentSlot->end_time = $request->input('end_time');
        $appointmentSlot->status = $request->input('status');
        $appointmentSlot->save();
        // return redirect()->route('appointment-slots.index')->with('success', 'Appointment Slot created successfully');
        return redirect()->route('appointment-slots.index')->with('success', 'Appointment Slot created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AppointmentSlot $appointmentSlot)
    {
        return view('appointment-slots.show', compact('appointmentSlot'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data['appointmentSlot'] =  AppointmentSlot::find($id);
        $data['doctors'] = Doctor::all();
        return view('appointment-slots.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        $request->validate([
            'doctor_id' => 'required',
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'required',
        ]);

        $appointmentSlot = AppointmentSlot::find($id);
        $appointmentSlot->doctor_id = $request->input('doctor_id');
        $appointmentSlot->date = $request->input('date');
        $appointmentSlot->start_time = $request->input('start_time');
        $appointmentSlot->end_time = $request->input('end_time');
        $appointmentSlot->status = $request->input('status');
        $appointmentSlot->save();

        return redirect()->route('appointment-slots.index')->with('success', 'Appointment Slot updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $appointmentSlot = AppointmentSlot::find($id);
        $appointmentSlot->delete();
        return redirect()->route('appointment-slots.index')->with('success', 'Appointment Slot deleted successfully');
    }
}
