<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Http\Request;
use Xentixar\EsewaSdk\Esewa;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::user()->id;
        $patient = Patient::where('user_id', $userId)->first();

        if (Auth::user()->roles == 'patient') {
            $payments = Payment::where('patient_id', $patient->id)
                ->with(['patient', 'appointment'])->paginate(5);
            $data['payments'] = $payments;
        }
        if(Auth::user()->roles == 'admin'){

            $data['payments'] = Payment::with(['patient', 'appointment'])->paginate(5);
        }
        return view('payments.index', $data);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }

    public function pay(Request $request, $paymentId)
    {
        // Fetch the payment details from the database
        $payment = Payment::findOrFail($paymentId);

        // dd($payment->toArray());

        $esewa = new Esewa();

        $transaction_id = 'TXN-' . uniqid();
        $payment->update(['transaction_id' => $transaction_id]);

        $esewa->config(
            route('payment.success'),
            route('payment.failure'),
            $payment->amount,
            $transaction_id
        );

        return $esewa->init();
    }


    public function success(Request $request)
    {
        // Decode the eSewa response
        $esewa = new Esewa();
        $response = $esewa->decode();

        // dd($response);

        if ($response) {
            // Check if transaction_uuid is present in the response
            if (isset($response['transaction_uuid'])) {
                $transactionUuid = $response['transaction_uuid'];

                // Find the payment record in the database
                $payment = Payment::where('transaction_id', $transactionUuid)->first();
                dd($payment);

                if ($payment) {
                    // Update the payment status to 'success'
                    $payment->update([
                        'payment_status' => 'completed',
                        'payment_type' => 'online',
                    ]);

                    return redirect()->route('payments.index')->with('message', 'Payment successful!');
                }

                return redirect()->route('payments.index')->with('error', 'Payment record not found!');
            }

            return redirect()->route('payments.index')->with('error', 'Invalid response from eSewa!');
        }

    }



    public function failure(Request $request)
    {
        dd($request);
        // Handle payment failure
        return redirect()->route('payments.index')->with('error', 'Payment from failure failed!');
    }
}
