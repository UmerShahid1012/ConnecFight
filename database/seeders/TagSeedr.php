<?php

namespace Database\Seeders;

use App\Models\SubTag;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TagSeedr extends Seeder
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
          'name'=>'Matchmaker',
          'is_active'=>1,
          'created_at'=>Carbon::now(),
          'updated_at'=>Carbon::now(),
      ],
        [
            'id'=>4,
          'name'=>'Athlete',
          'is_active'=>1,
          'created_at'=>Carbon::now(),
          'updated_at'=>Carbon::now(),
      ],


    ];
    $sub_tags = [
        [
            'id'=>5,
            'name'=>'Boxer',
            'is_active'=>1,
            'tag_id'=>4,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now(),
        ],
        [
            'id'=>2,
            'name'=>'Manager',
            'is_active'=>1,
            'tag_id'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now(),
        ],
        [
            'id'=>3,
            'name'=>'Promoter',
            'is_active'=>1,
            'tag_id'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now(),
        ],
        [
            'id'=>6,
            'name'=>'Wrestler',
            'is_active'=>1,
            'tag_id'=>4,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now(),
        ],
        [
            'id'=>7,
            'name'=>'Kickboxer',
            'is_active'=>1,
            'tag_id'=>4,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now(),
        ],
        [
            'id'=>8,
            'name'=>'MMA',
            'is_active'=>1,
            'tag_id'=>4,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now(),
        ],
    ];

            Tag::insert($tags);
            SubTag::insert($sub_tags);

    }
}
