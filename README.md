<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Project Documentation

## 1. Requirements

The application will be a Appointment Management System that allows patients to book appointments with doctors, make payments, and shedule appointment ,doctor review. The key features are:

### 1.1 Minimum Viable Product (MVP)

-   **User Registration and Authentication:**
    -   Users (patients, doctors, admin) must be able to register, log in, and authenticate.
-   **Patient Appointment Booking:**
    -   Patients can view available time slots for doctors and book appointments.
-   **Doctor Schedule Management:**
    -   Doctors can manage their availability by creating and updating appointment slots.
-   **Reviews:**
    -   Doctors can leave reviews on User medical health after an appointment is completed.
-   **Payment System:**
    -   Patients can make payments for their appointments.
-   **Appointment Status:**
    -   Patients can view the status of their appointments (e.g., Pending, Completed, Rescheduled).
-   **Admin Role:**
    -   Admins can manage users, appointments, and reviews.

## 2. Database Schema

The database schema is designed to support the core features of the appointment system. Below is an outline of the schema.

### 2.1 User Table

**Table Name**: `users`

| Field      | Type          | Description                               |
| ---------- | ------------- | ----------------------------------------- |
| `id`       | INT           | Primary Key, unique user identifier       |
| `f_name`   | VARCHAR       | Name of the user                          |
| `l_name`   | VARCHAR       | Name of the user                          |
| `email`    | VARCHAR       | Email address of the user                 |
| `password` | VARCHAR       | User password                             |
| `phone`    | VARCHAR       | User phone number                         |
| `address`  | VARCHAR       | User phone number                         |
| `role`     | ENUM(A, P, D) | Role of the user (Admin, Patient, Doctor) |

-   **Roles**:

    -   `A` = Admin
    -   `P` = Patient
    -   `D` = Doctor

-   **Relationships**:
    -   `Patient`
        ```php
        public function patient() {
            return $this->hasOne(Patient::class);
        }
        ```
    -   `Doctor`
        ```php
        public function doctor() {
            return $this->hasOne(Doctor::class);
        }
        ```
    -   `Appointments`
        ```php
        public function appointments()
        {
            return $this->hasMany(Appointment::class);
        }
        ```
    -   `Payments`
        ```php
        public function payments()
        {
            return $this->hasMany(Payment::class);
        }
        ```

### 2.2 Patients Table

**Table Name**: `patients`

| Field             | Type       | Description                                     |
| ----------------- | ---------- | ----------------------------------------------- |
| `id`              | INT        | Primary Key, unique patient identifier          |
| `user_id`         | INT        | Foreign Key (users.id), links to the User table |
| `medical_history` | INT        | Patient's past medical conditoin                |
| `allergies`       | VARCHAR    | Patient's allergies                             |
| `medications`     | VARCHAR    | Patient's medications                           |
| `blood_group`     | INVARCHART | Patient's blood_group                           |

-   **Relationships**:
    -   `User`
        ```php
        public function user()
        {
            return $this->belongsTo(User::class);
        }
        ```
    -   `Appointment`
        ```php
        public function appointments()
        {
            return $this->hasMany(Appointment::class);
        }
        ```
    -   `Patient History`
        ```php
        public function patientHistories()
        {
            return $this->hasMany(PatientHistory::class);
        }
        ```
    -   `Payments`
        ```php
        public function payments()
        {
            return $this->hasMany(Payment::class);
        }
        ```

### 2.3 Doctor Table

**Table Name**: `doctors`

| Field           | Type    | Description                                     |
| --------------- | ------- | ----------------------------------------------- |
| `id`            | INT     | Primary Key, unique doctor identifier           |
| `user_id`       | INT     | Foreign Key (users.id), links to the User table |
| `qualification` | VARCHAR | Doctor's qualifications                         |
| `experience`    | INT     | Doctor's experience                             |
| `department`    | VARCHAR | Department of the doctor                        |
| `hourly_rate`   | FLOAT   | Doctor's hourly rate                            |

-   **Relationships**:
    -   `User`
        ```php
        public function user()
        {
            return $this->belongsTo(User::class);
        }
        ```
    -   `Appointment`
        ```php
        public function appointments()
        {
            return $this->hasMany(Appointment::class);
        }
        ```
    -   `Appointment Slot`
        ```php
        public function appointmentSlots()
        {
            return $this->hasMany(ApointmentSlot::class);
        }
        ```
    -   `Patient History`
        ```php
        public function patientHistories()
        {
            return $this->hasMany(PatientHistory::class);
        }
        ```
    -   `Speciality`
        ```php
        public function speciality()
        {
            return $this->belongsTo(Speciality::class);
        }
        ```

### 2.4 Reviews Table

**Table Name**: `reviews`

| Field             | Type    | Description                                                   |
| ----------------- | ------- | ------------------------------------------------------------- |
| `id`              | INT     | Primary Key, unique review identifier                         |
| `appointments_id` | INT     | Foreign Key (appointments.id), links to the Appointment table |
| `comment`         | VARCHAR | Comment on appointment                                        |
| `pdf`             | VARCHAR | Patient pdf on that appointment                               |

-   **Relationships**:

    -   `Appointment`
        ```php
        public function appointments()
        {
            return $this->belongsTo(Appointment::class);
        }
        ```

### 2.5 Patient History Table

**Table Name**: `patient_history`

| Field            | Type | Description                                                   |
| ---------------- | ---- | ------------------------------------------------------------- |
| `id`             | INT  | Primary Key, unique record identifier                         |
| `patient_id`     | INT  | Foreign Key (patients.id), links to the Patient table         |
| `appointment_id` | INT  | Foreign Key (appointments.id), links to the Appointment table |
| `review_id`      | INT  | Foreign Key (reviews.id), links to the Review table           |
| `payment_id`     | INT  | Foreign Key (payment.id), links to the Payment table          |

-   **Relationships**:
    -   `Patient`
        ```php
        public function patient() {
            return $this->belongsTo(Patient::class);
        }
        ```
    -   `Doctor`
        ```php
        public function doctor() {
            return $this->belongsTo(Doctor::class);
        }
        ```
    -   `Appointments`
        ```php
        public function appointments()
        {
            return $this->belongsTo(Appointment::class);
        }
        ```
    -   `Payments`
        ```php
        public function payments()
        {
            return $this->belongsTo(Payment::class);
        }
        ```
    -   `Review`
        ```php
        public function review()
        {
            return $this->belongsTo(Review::class);
        }
        ```

### 2.6 Appointments Table

**Table Name**: `appointments`

| Field        | Type          | Description                                                      |
| ------------ | ------------- | ---------------------------------------------------------------- |
| `id`         | INT           | Primary Key, unique appointment identifier                       |
| `patient_id` | INT           | Foreign Key (patients.id), links to the Patient table            |
| `doctor_id`  | INT           | Foreign Key (doctors.id), links to the Doctor table              |
| `date`       | DATE          | Date of the appointment                                          |
| `start_time` | TIME          | Date of the appointment                                          |
| `end_time`   | TIME          | Date of the appointment                                          |
| `status`     | ENUM(P, C, R) | Appointment status (P = Pending, C = Completed, R = Rescheduled) |

-   **Status**:

    -   `P` = Pending
    -   `C` = Completed
    -   `R` = Rescheduled

-   **Relationships**:
    -   `Patient`
        ```php
        public function patient() {
            return $this->belongsTo(Patient::class);
        }
        ```
    -   `Doctor`
        ```php
        public function doctor() {
            return $this->belongsTo(Doctor::class);
        }
        ```
    -   `Payments`
        ```php
        public function payments()
        {
            return $this->hasOne(Payment::class);
        }
        ```
    -   `Review`
        ```php
        public function reviews()
        {
            return $this->hasMany(Review::class);
        }
        ```

### 2.7 Payment Table

**Table Name**: `payment`

| Field            | Type       | Description                                                   |
| ---------------- | ---------- | ------------------------------------------------------------- |
| `id`             | INT        | Primary Key, unique payment identifier                        |
| `patient_id`     | INT        | Foreign Key (patients.id), links to the Patient table         |
| `appointment_id` | INT        | Foreign Key (appointments.id), links to the Appointment table |
| `payment_status` | ENUM(P, C) | Payment status (P = Pending, C = Complete)                    |
| `payment_type`   | ENUM(O, C) | Payment status (O = Online, C = Cash)                         |
| `transaction_id` | VARCHAR    | Unique Transaction Id                                         |

-   **Payment Status**:

    -   `P` = Pending
    -   `C` = Complete

-   **Payment Type**:

    -   `O` = Online
    -   `C` = Cash

-   **Relationships**:
    -   `User`
        ````php
        public function user() {
            return $this->belongsTo(User::class);
        }

    -   `Appointment`
        ````php
        public function user() {
            return $this->belongsTo(Appointment::class);
        }

    -   `Patient`
        ````php
        public function user() {
            return $this->belongsTo(Patient::class);
        }


### 2.9 Appointments Slot Table

**Table Name**: `appointments_slot`

| Field        | Type       | Description                                         |
| ------------ | ---------- | --------------------------------------------------- |
| `id`         | INT        | Primary Key, unique slot identifier                 |
| `doctor_id`  | INT        | Foreign Key (doctors.id), links to the Doctor table |
| `date`       | DATE       | Date of the available appointment slot              |
| `start_time` | TIME       | Start time of the appointment slot                  |
| `end_time`   | TIME       | End time of the appointment slot                    |
| `status`     | ENUM(B, A) | Slot status (B = Booked, A = Available)             |

-   **Status**:

    -   `B` = Booked
    -   `A` = Available


-   **Relationships**:
    -   `Doctor`
        ````php
        public function doctor() {
            return $this->belongsTo(Doctor::class);
        }

### 3.0 Speciality Table

**Table Name**: `speciaities`

| Field  | Type | Description                         |
| ------ | ---- | ----------------------------------- |
| `id`   | INT  | Primary Key, unique slot identifier |
| `name` | INT  | Speciality Name                     |

-   **Relationships**:
    -   `Doctor`
        ````php
        public function doctor() {
            return $this->belongsTo(Doctor::class);
        }
## 3. Problem to Be Solved

The system addresses several problems commonly encountered in healthcare management:

1. **Appointment Scheduling**:

-   Patients often struggle to find available doctors and schedule appointments, while doctors may have difficulty managing their available times.
-   The system allows patients to view available time slots, while doctors can manage their availability.

2. **Patient-Doctor Interaction**:

-   After an appointment, there is often no formalized way to provide feedback about the doctor's performance or the quality of the visit.
-   Reviews allow patients to leave feedback on their experience, providing valuable information for other patients.

3. **Payment Management**:

-   Managing payments manually can lead to errors and confusion for both patients and doctors.
-   The payment system ensures that patients can easily make payments for their appointments and track their payment status.

4. **Medical History Tracking**:

-   Keeping track of patient history (appointments, reviews, payments) is essential for providing high-quality care.
-   The system links patients' appointments, reviews, and payments to build a comprehensive patient history.

5. **Role-Based Access Control**:

-   Different users (patients, doctors, admins) have different needs and permissions within the system.
-   The role-based access control ensures that users only see and interact with data relevant to their role.

## Conclusion

This system helps improve the efficiency and transparency of medical appointment scheduling, review management, and payment processing, benefiting both patients and healthcare providers.

```

```
