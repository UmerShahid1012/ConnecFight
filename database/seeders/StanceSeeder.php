<?php

namespace Database\Seeders;

use App\Models\Stance;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = [
            [
                'id'=>1,
                'title'=>'Orthodox',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>2,
                'title'=>'Forward stance',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>3,
                'title'=>'Ginga (capoeira)',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>4,
                'title'=>'Horse stance',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>5,
                'title'=>'Natural stance',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],

        ];

        Stance::insert($tags);


    }
}
