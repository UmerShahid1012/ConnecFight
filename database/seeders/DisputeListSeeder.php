<?php

namespace Database\Seeders;

use App\Models\DisputeList;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DisputeListSeeder extends Seeder
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
                'name'=>'Wrong Check Ins',
                'type'=>'sparrings',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>2,
                'name'=>'Arrived Late at Location',
                'type'=>'sparrings',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>3,
                'name'=>'Not Arrived Yet',
                'type'=>'sparrings',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>4,
                'name'=>'Cancel my Contract',
                'type'=>'sparrings',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>5,
                'name'=>'Other',
                'type'=>'sparrings',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>6,
                'name'=>'Overweight',
                'type'=>'fights',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>7,
                'name'=>'Under Weight',
                'type'=>'fights',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>8,
                'name'=>'Refusal to participate',
                'type'=>'fights',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>9,
                'name'=>'Want to Cancel Fight',
                'type'=>'fights',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>10,
                'name'=>'Failed Physical Test',
                'type'=>'fights',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>11,
                'name'=>'Other',
                'type'=>'fights',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>12,
                'name'=>'Not Arrived Yet',
                'type'=>'fights',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>13,
                'name'=>'Payment Dispute',
                'type'=>'payments',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],[
                'id'=>14,
                'name'=>'',
                'type'=>'customer support',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ]
        ];

        DisputeList::insert($tags);

    }
}
