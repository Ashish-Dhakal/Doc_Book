<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Notification</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light font-sans">

    <div class="container mt-5">
        <div class="card shadow-lg border-light">
            <!-- Header -->
            <div class="card-header bg-primary text-white text-center">
                <h3 class="font-weight-bold">New Appointment Scheduled</h3>
            </div>

            <!-- Content -->
            <div class="card-body">
                <!-- Conditional Greeting -->
                @if ($recipientType == 'doctor')
                    <p class="h5">Dear Dr. <strong class="text-primary">{{ $appointment->doctor->user->f_name }} {{ $appointment->doctor->user->l_name }} </strong>,</p>
                    <p class="lead mb-4">A new appointment has been scheduled with the following details:</p>
                    <ul class="list-unstyled">
                        <li><strong class="font-weight-bold">Patient Name:</strong> {{ $appointment->patient->user->f_name }} {{ $appointment->patient->user->l_name }}</li>
                        <li><strong class="font-weight-bold">Date:</strong> {{ \Carbon\Carbon::parse($appointment->date)->format('F j, Y, g:i a') }}</li>
                    </ul>
                    <p class="lead mb-4">Please log in to your dashboard for more details.</p>
                @elseif ($recipientType == 'patient')
                    <p class="h5">Dear {{ $appointment->patient->user->f_name }} {{ $appointment->patient->user->l_name }},</p>
                    <p class="lead mb-4">You have a new appointment scheduled with Dr. <strong class="text-primary">{{ $appointment->doctor->user->f_name }} {{ $appointment->doctor->user->l_name }}</strong>.</p>
                    <ul class="list-unstyled">
                        <li><strong class="font-weight-bold">Appointment Date:</strong> {{ \Carbon\Carbon::parse($appointment->date)->format('F j, Y, g:i a') }}</li>
                    </ul>
                    <p class="lead mb-4">Please make sure to log in to your dashboard for more details.</p>
                @endif

                <p class="lead">Thank you!</p>
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
