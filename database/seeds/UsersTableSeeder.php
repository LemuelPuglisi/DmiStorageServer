<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'superadmin',
            'role' => 3,
            'email' => 'superadmin@dmi.com',
            'password' => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now(),
        ]); 

        DB::table('users')->insert([
            'name' => 'admin',
            'role' => 2,
            'email' => 'admin@dmi.com',
            'password' => Hash::make('secret'),
            'created_at' => now(), 
            'updated_at' => now(),
        ]); 

        DB::table('users')->insert([
            'name' => 'guest',
            'role' => 1,
            'email' => 'guest@dmi.com',
            'password' => Hash::make('secret'),
            'created_at' => now(), 
            'updated_at' => now(),
        ]); 
    }
}
