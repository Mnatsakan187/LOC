<?php

use Illuminate\Database\Seeder;

class PlanFeatureTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('plan_features')->insert(
        [
          [
            'plan_id' => 1,
            'name' => "Creative Profile",
            'check' => false,
            'info' => "1x",
            'included' => true
          ],
          [
            'plan_id' => 1,
            'name' => "Project Profile",
            'check' => false,
            'info' =>  "1x",
            'included' => true
          ],
          [
            'plan_id' => 1,
            'name' => "Group",
            'check' => false,
            'info' => "1x",
            'included' => true
          ],
          [
            'plan_id' => 1,
            'name' => "Profile & Project Analytics",
            'check' => true,
            'info' => "",
            'included' => true,
          ],
          [
            'plan_id' => 1,
            'name' => "Access to General Analytics Reports",
            'check' => true,
            'info' => "",
            'included' => true
          ],
          [
            'plan_id' => 1,
            'name' => "Personalised Data Reports",
            'check' => false,
            'info' => "",
            'included' => false
          ],
          [
            'plan_id' => 1,
            'name' => "TEAM Consultation & Advocasy",
            'check' => false,
            'info' => "",
            'included' => false
          ],
          [
            'plan_id' => 1,
            'name' => "Direct-To-Strategy Analytics Facilitation*",
            'check' => false,
            'info' => "",
            'included' => false
          ],




         [
             'plan_id' => 2,
             'name' => "Creative Profile",
             'check' => false,
             'info' => "4x",
             'included' => true
         ],
         [
             'plan_id' => 2,
             'name' => "Project Profile",
             'check' => false,
             'info' =>  "1x",
             'included' => true
         ],
         [
             'plan_id' => 2,
             'name' => "Group",
             'check' => false,
             'info' => "3x",
             'included' => true
         ],
         [
             'plan_id' => 2,
             'name' => "Profile & Project Analytics",
             'check' => true,
             'info' => "",
             'included' => true,
         ],
         [
             'plan_id' => 2,
             'name' => "Access to General Analytics Reports",
             'check' => true,
             'info' => "",
             'included' => true
         ],
         [
             'plan_id' => 2,
             'name' => "Personalised Data Reports",
             'check' => true,
             'info' => "",
             'included' => true,
         ],
         [
             'plan_id' => 2,
             'name' => "TEAM Consultation & Advocasy",
             'check' => true,
             'info' => "",
             'included' => true
         ],
         [
             'plan_id' => 2,
             'name' => "Direct-To-Strategy Analytics Facilitation*",
             'check' => false,
             'info' => "",
             'included' => false
         ],



         [
            'plan_id' => 3,
            'name' =>  "Creative Profile",
            'check' => false,
            'info' =>  "5x",
            'included' => true
         ],

          [
            'plan_id' => 3,
            'name' => "Project Profile",
            'check' =>  false,
            'info' =>  "10x",
            'included' => true
          ],

          [
            'plan_id' => 3,
            'name' =>  "Group",
            'check' =>  false,
            'info' =>  "5x",
            'included' => true
          ],

          [
            'plan_id' => 3,
            'name' =>   "Profile & Project Analytics",
            'check' =>  true,
            'info' => "",
            'included' => true
          ],

          [
            'plan_id' => 3,
            'name' =>  "Access to General Analytics Reports",
            'check' => true,
            'info' => "",
            'included' => true
          ],
          [
             'plan_id' => 3,
             'name' =>   "Personalised Data Reports",
             'check' => true,
             'info' => "",
             'included' => true
          ],
          [
            'plan_id' => 3,
            'name' => "TEAM Consultation & Advocasy",
            'check' =>  true,
            'info' => "",
            'included' => true
          ],
          [
            'plan_id' => 3,
            'name' =>"Direct-To-Strategy Analytics Facilitation*",
            'check' =>  true,
            'info' =>  "",
            'included' => true
          ],
        ]

        );
    }
}





