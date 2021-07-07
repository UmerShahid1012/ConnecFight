<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
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
                'title'=>'Mix Marshal Arts',
                'sub_tag_id'=>7,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>2,
                'title'=>'TNT',
                'sub_tag_id'=>6,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>4,
                'title'=>'Boxing',
                'sub_tag_id'=>5,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
        ];

        Event::insert($tags);
    }
}
