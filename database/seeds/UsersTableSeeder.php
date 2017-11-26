<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Default User
        DB::table('users')->insert([
            'fullname' => 'ASAAT Admin',
            'username' => 'admin',
            'email' => 'admin@email.com',
            'password' => Hash::make('admin'),
            'level' => 0,
            'api_token' => 'JV3VxhQSdkCpZfmVQGcCvoNADxDf4hBUP727jZekR7dh9JZiCeJTXgEVa9bS',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
    }
}
