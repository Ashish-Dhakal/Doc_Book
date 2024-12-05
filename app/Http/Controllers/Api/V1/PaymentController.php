<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Http\Request;
use Xentixar\EsewaSdk\Esewa;
use App\Mail\PaymentCompleteMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Api\V1\BaseController;

class PaymentController extends BaseController
{

    /**
     * Fetch all Payments
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
        if (Auth::user()->roles == 'admin') {

            $data['payments'] = Payment::with(['patient', 'appointment'])->paginate(5);
        }

        return $this->successResponse($data, 'Payments retrieved successfully');
    }

    /**
     * Make payment
    */
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
    /**
     * Handle payment success
    */

    public function success(Request $request)
    {
        // Decode the eSewa response
        $esewa = new Esewa();
        $response = $esewa->decode();

        dd($response);

        if ($response) {
            // Check if transaction_uuid is present in the response
            if (isset($response['transaction_uuid'])) {
                $transactionUuid = $response['transaction_uuid'];

                // Find the payment record in the database
                $payment = Payment::where('transaction_id', $transactionUuid)->first();
                // Mail::to($payment->patient->user->email)->queue(new PaymentCompleteMail($payment));

                if ($payment) {
                    // Update the payment status to 'success'
                    $payment->update([
                        'payment_status' => 'completed',
                        'payment_type' => 'online',
                    ]);

                    return $this->successResponse($payment, 'Payment successful!');
                }

                return $this->errorResponse('Payment record not found!', 404);
            }

            return $this->errorResponse('Invalid response from eSewa!', 400);
        }
    }


    /**
     * Handle payment failure
    */

    public function failure(Request $request)
    {
        return $this->errorResponse('Payment from failure failed!', 400);
    }
}
