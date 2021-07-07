<?php

namespace Database\Seeders;

use App\Models\Faqs;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
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
                'category'=>'Cancellation',
                'question'=>"this is dumy question #1?",
                'answer'=>"this is dumy answer",
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>2,
                'category'=>'Cancellation',
                'question'=>"this is dumy question #2?",
                'answer'=>"this is dumy answer",
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
            [
                'id'=>4,
                'category'=>'Dispute',
                'question'=>"this is dumy question #3?",
                'answer'=>"this is dumy answer",
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ],
        ];

        Faqs::insert($tags);
    }
}
