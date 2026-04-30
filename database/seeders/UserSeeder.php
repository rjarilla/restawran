<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('rt_users')->truncate();

        $users = [
            [
                'UserName' => 'admin',
                'Password' => Hash::make('password'),
                'Role' => 'Admin',
            ],
            [
                'UserName' => 'manager',
                'Password' => Hash::make('password'),
                'Role' => 'Manager',
            ],
            [
                'UserName' => 'staff',
                'Password' => Hash::make('password'),
                'Role' => 'Staff',
            ],
        ];

        DB::table('rt_users')->insert($users);
    }
}