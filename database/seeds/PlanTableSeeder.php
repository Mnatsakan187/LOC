<?php

use Illuminate\Database\Seeder;

class PlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('plans')->insert(
            [
                [
                    'name' => 'Creator',
                    'price' => '20$ per month',
                ],

                [
                    'name' => 'Creator Plus',
                    'price' => '22$ per month',
                ],

                [
                    'name' => 'Creator Pro',
                    'price' => '24$ per month',
                ]
            ]
        );
    }
}





