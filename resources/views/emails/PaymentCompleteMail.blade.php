<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patment Complete Notification</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light font-sans">

    <div class="container mt-5">
        <div class="card shadow-lg border-light">

            <!-- Header -->
            <div class="card-header bg-primary text-white text-center">
                <h3 class="font-weight-bold">Payment Completion Details</h3>
            </div>

            <!-- Content -->
            <div class="card-body">
                <!-- Patient Info -->
                <div class="mb-4">
                    <h5 class="text-primary font-weight-bold">Patient Details</h5>
                    <p><strong class="font-weight-bold">Full Name:</strong> {{ $payment->patient->user->f_name }}
                        {{ $payment->patient->user->l_name }}</p>
                </div>

                <!-- Patient Info -->
                <div class="mb-4">
                    <h5 class="text-primary font-weight-bold">Doctor Details</h5>
                    <p><strong class="font-weight-bold">Full Name:</strong>
                        {{ $payment->appointment->doctor->user->f_name }}
                        {{ $payment->appointment->doctor->user->f_name }}</p>
                </div>

                <!-- Doctor Info -->
                <div class="mb-4">
                    <!-- Payment Info -->
                    <div class="mb-4">
                        <h5 class="text-primary font-weight-bold">Payment Details</h5>
                        <p><strong class="font-weight-bold">Payment Amount:</strong> Rs:
                            {{ $payment->amount }}</p>
                    </div>

                    <!-- Appointment Info -->
                    <div class="mb-4">
                        <h5 class="text-primary font-weight-bold">Appointment Information</h5>
                        <p><strong class="font-weight-bold">Appointment Date:</strong>
                            {{ \Carbon\Carbon::parse($payment->appointment->date)->format('F j, Y') }}</p>
                        <p><strong class="font-weight-bold">Appointment Time:</strong>
                            {{ \Carbon\Carbon::parse($payment->appointment->start_time)->format('g:i A') }} -
                            {{ \Carbon\Carbon::parse($payment->appointment->end_time)->format('g:i A') }}
                        </p>
                    </div>

                    <!-- Action Button -->
                    <div class="d-flex justify-content-center">
                        <a href="#" class="btn btn-primary">View More Details</a>
                    </div>
                </div>

                <!-- Footer -->
                <div class="card-footer text-center text-muted">
                    <p>This is an automated notification from DocBook.</p>
                </div>

            </div>
        </div>

        {{-- <!-- Payment Info -->
        <div class="mb-4">
            <h5 class="text-primary font-weight-bold">Payment Details</h5>
            <p><strong class="font-weight-bold">Billed Amount:</strong> Rs: {{ $appointment->payment->amount }}</p>
        </div> --}}

        <!-- Appointment Info -->
        <div class="mb-4">
            <h5 class="text-primary font-weight-bold">Appointment Information</h5>
            <p><strong class="font-weight-bold">Appointment Date:</strong>
                {{ \Carbon\Carbon::parse($payment->appointment->date)->format('F j, Y') }}</p>
            <p><strong class="font-weight-bold">Appointment Time:</strong>
                {{ \Carbon\Carbon::parse($payment->appointment->start_time)->format('g:i A') }} -
                {{ \Carbon\Carbon::parse($payment->appointment->end_time)->format('g:i A') }}
            </p>
        </div>

        <!-- Action Button -->
        <div class="d-flex justify-content-center">
            <a href="#" class="btn btn-primary">View More Details</a>
        </div>
    </div>

    <!-- Footer -->
    <div class="card-footer text-center text-muted">
        <p>This is an automated notification from DocBook.</p>
    </div>

    </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
