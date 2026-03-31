<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Seed the default admin account.
     */
    public function run(): void
    {
        Admin::create([
            'email'    => 'admin@askdocph.com',
            'password' => Hash::make('Admin@1234'),
            'fname'    => 'Super',
            'mname'    => null,
            'lname'    => 'Admin',
            'gender'   => null,
            'bday'     => null,
            'avatar_url' => null,
        ]);
    }
}
