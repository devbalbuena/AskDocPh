<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed the default admin account
        $this->call(AdminSeeder::class);

        // Seed a test patient user
        User::updateOrCreate(
            ['email' => 'patient@askdocph.com'],
            [
                'password' => Hash::make('Patient@1234'),
                'username' => 'testpatient',
                'fname'    => 'Test',
                'mname'    => null,
                'lname'    => 'Patient',
                'role'     => 'patient',
                'doctor_status' => 'none',
            ]
        );

        // Seed an approved doctor user for testing
        User::updateOrCreate(
            ['email' => 'doctor@askdocph.com'],
            [
                'password' => Hash::make('Doctor@1234'),
                'username' => 'testdoctor',
                'fname'    => 'Test',
                'mname'    => null,
                'lname'    => 'Doctor',
                'role'     => 'doctor',
                'doctor_status' => 'approved',
            ]
        );
    }
}
