<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::create([
            'id' => 1,
            'name' => 'Test User One',
            'email' => 'userone@gmail.com',
            'password' => Hash::make('password'),
        ]);
        \App\Models\User::create([
            'id' => 2,
            'name' => 'Test User Two',
            'email' => 'usertwo@gmail.com',
            'password' => Hash::make('password'),
        ]);
        \App\Models\User::create([
            'id' => 3,
            'name' => 'Test User Three',
            'email' => 'userthree@gmail.com',
            'password' => Hash::make('password'),
        ]);
        \App\Models\ChatMessage::create([
            'sender_id' => 1,
            'receiver_id' => 2,
            'message' => 'hi',
            'created_at' => Carbon::now(),
        ]);
        \App\Models\ChatMessage::create([
            'sender_id' => 2,
            'receiver_id' => 1,
            'message' => 'hello',
            'created_at' => Carbon::now(),
        ]);
        \App\Models\ChatMessage::create([
            'sender_id' => 1,
            'receiver_id' => 3,
            'message' => 'hey',
            'created_at' => Carbon::now(),
        ]);
        \App\Models\ChatMessage::create([
            'sender_id' => 2,
            'receiver_id' => 3,
            'message' => 'hola'
        ]);
    }
}
