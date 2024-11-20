<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'f_name' => 'Ashish',
            'l_name' => 'Dhakal',
            'email' => 'ashish@test.com',
            'roles' => 'admin',
            'password' => bcrypt('password')
        ]);

        $doctor = User::create([
            'f_name' => 'Ashish',
            'l_name' => 'Doctor',
            'email' => 'doctor@test.com',
            'roles' => 'doctor',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
            'address' => 'Kathmandu',

        ]);

        Doctor::create([
            'user_id' => $doctor->id,
            'experience' => 10,
            'qualification' => 'MBBS',
            'specialization' => 'Cardiology',
            'department' => 'Cardiology',
        ]);


        $patient = User::create([
            'f_name' => 'Ashish',
            'l_name' => 'Patient',
            'email' => 'patient@test.com',
            'roles' => 'patient',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
            'address' => 'Kathmandu',

        ]);

        Patient::create([
            'user_id' => $patient->id,
            'medical_history' => 'None',
            'allergies' => 'None',
            'blood_group' => 'None',
            'medications' => 'None',
        ]);


      
    }
}
