<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $users = [
            [
                'name' => 'Junaid Shoaib',
                'email' => 'junaid.shoaib@mzco.com.pk',
                'password' => Hash::make('12345678'),
                'role' => 0,
                // O => Super Admin
            ],
            [
                'name' => 'Ainna Fatima',
                'email' => 'ainna.fatima@mzco.com.pk',
                'password' => Hash::make('mzk123456'),
                'role' => 1,
                // O => Manager
            ],
            [
                'name' => 'Employee',
                'email' => 'employee@mzco.com.pk',
                'password' => Hash::make('mzk123456'),
                'role' => 2,
                // O => Employee
            ],
            
        ];


        foreach($users as $user){
            User::create($user);
        }
        //
    }
}
