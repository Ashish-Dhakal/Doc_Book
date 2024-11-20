<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Models\AppointmentSlot;

class AppointmentSlotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appointmentSlots = AppointmentSlot::all();
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
        $request->validate([
            'doctor_id' => 'required',
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'required',
        ]);

        $appointmentSlot = new AppointmentSlot();
        $appointmentSlot->doctor_id = $request->input('doctor_id');
        $appointmentSlot->date = $request->input('date');
        $appointmentSlot->start_time = $request->input('start_time');
        $appointmentSlot->end_time = $request->input('end_time');
        $appointmentSlot->status = $request->input('status');
        $appointmentSlot->save();
        return redirect()->route('appointment-slots.index')->with('success', 'Appointment Slot created successfully');
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
