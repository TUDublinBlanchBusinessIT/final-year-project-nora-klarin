<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'carer@test.com'],
            [
                'name' => 'Test Carer',
                'password' => Hash::make('password'),
                'role' => 'carer',
            ]
        );
    }
}
