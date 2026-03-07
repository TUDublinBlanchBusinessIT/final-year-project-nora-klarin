<?php



namespace Database\Seeders;



use Illuminate\Database\Seeder;

use App\Models\User;

use Illuminate\Support\Facades\Hash;



class UserSeeder extends Seeder

{

    public function run(): void

    {

        // Carer (your account)

User::updateOrCreate(

    ['email' => 'knownasammy@gmail.com'],

    [

        'name' => 'Sammy',

        'username' => 'sammy',

        'password' => Hash::make('password123'),

        'role' => 'carer',

    ]

);



User::updateOrCreate(

    ['email' => 'socialworker@test.com'],

    [

        'name' => 'Test Social Worker',

        'username' => 'socialworker',

        'password' => Hash::make('password'),

        'role' => 'social_worker',

    ]

);



User::updateOrCreate(

    ['email' => 'admin@test.com'],

    [

        'name' => 'Admin',

        'username' => 'admin',

        'password' => Hash::make('password'),

        'role' => 'admin',

    ]

);


User::updateOrCreate(

    ['email' => 'youngperson@test.com'],

    [

        'name' => 'Test Young Person',

        'username' => 'youngperson',

        'password' => Hash::make('password'),

        'role' => 'young_person',

    ]

);

    }

}