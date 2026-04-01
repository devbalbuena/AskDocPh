<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Seed the default admin account.
     */
    public function run(): void
    {
        // Use updateOrCreate so running the seeder twice won't crash
        User::updateOrCreate(
            ['email' => 'admin@askdocph.com'],
            [
                'password'      => Hash::make('Admin@1234'),
                'username'      => 'superadmin',
                'fname'         => 'Super',
                'mname'         => null,
                'lname'         => 'Admin',
                'gender'        => null,
                'bday'          => null,
                'role'          => 'admin',           // Crucial to set the role!
                'doctor_status' => 'none',
            ]
        );
    }
}
