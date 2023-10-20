<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->insert([
            'id' => 1,
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role'=>'admin',
            'email_verified_at' => now(),
            'password' => Hash::make('123456')
        ]);
        \DB::table('users')->insert([
            'id' => 2,
            'name' => 'Test',
            'email' => 'test@gmail.com',
            'role'=>'user',
            'email_verified_at' => now(),
            'password' => Hash::make('123456')
        ]);
        \App\Models\User::factory(50)->create();
    }
}
