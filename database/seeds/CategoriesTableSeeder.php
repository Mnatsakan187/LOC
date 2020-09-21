<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert(
            [
                [
                    'name' => 'Music',
                ],

                [
                    'name' => 'Theatre & Performance',
                ],

                [
                    'name' => 'Comedy',
                ],

                [
                    'name' => 'Sporting',
                ],

                [
                    'name' => 'Educational',
                ],

                [
                    'name' => 'Youth',
                ],

                [
                    'name' => 'Lectures/Seminars',
                ],

                [
                    'name' => 'Lifestyle/Expo',
                ],

                [
                    'name' => 'Community/Government',
                ]
            ]

        );
    }
}





