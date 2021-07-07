<?php

namespace Database\Seeders;

use App\Models\Stance;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
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
                'name'=>'Pending from both fighters',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>2,
                'name'=>'Pending from defender',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>3,
                'name'=>'Pending from challenger',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>4,
                'name'=>'Rejected from challenger',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>5,
                'name'=>'Rejected from defender',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>6,
                'name'=>'Rejected',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>7,
                'name'=>'Accepted',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>8,
                'name'=>'Pending',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>9,
                'name'=>'Completed',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>10,
                'name'=>'Canceled by job poster',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>11,
                'name'=>'Canceled by athlete',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>12,
                'name'=>'Expired',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>13,
                'name'=>'Active',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>14,
                'name'=>'Canceled',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>15,
                'name'=>'Disputed',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>16,
                'name'=>'Requested Check In',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>17,
                'name'=>'Checked In',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>18,
                'name'=>'Missing',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>19,
                'name'=>'No Response',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],

        ];

        Status::insert($tags);

    }
}
