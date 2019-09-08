<?php

use Illuminate\Database\Seeder;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('courses')->insert([
            'name' => "Algoritmi",
            'year' => "2",
            'cfu' => 9,
            'creator_id' => 1, 
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('courses')->insert([
            'name' => "Analisi 1",
            'year' => "1",
            'cfu' => 6,
            'creator_id' => 1, 
            'created_at' => now(),
            'updated_at' => now(),
        ]);        

        DB::table('courses')->insert([
            'name' => "Fondamenti di informatica",
            'year' => "1",
            'cfu' => 9,
            'creator_id' => 1, 
            'created_at' => now(),
            'updated_at' => now(),
        ]);     
    }
}
