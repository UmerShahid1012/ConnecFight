<?php

namespace Database\Seeders;

use App\Models\Facility;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
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
                'name'=>'Travel',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>2,
                'name'=>'Food',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>3,
                'name'=>'Hotel',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            ];

        Facility::insert($tags);

    }
}
