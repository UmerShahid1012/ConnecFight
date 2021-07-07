<?php

namespace Database\Seeders;

use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Stripe\Price;
use Stripe\Stripe;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $tax_rate = \Stripe\TaxRate::create([
            'display_name' => 'Cnf tax',
            'description' => 'cnf percentage',
//                'jurisdiction' => 'CA - SF',
            'percentage' => 0,
            'inclusive' => false,
        ]);
        $price = Price::create([
            'product' => env('STRIPE_PRODUCT_KEY'),
            'unit_amount' => (100 * 0),
            'nickname' => 'Free',
            'currency' => 'usd',
            'recurring' => [
                'interval' => 'month',
            ],

        ]);
        $tags = [

            [
                'id'=>1,
                'title'=>'Free',
                'no_of_sparrings'=>6,
                'no_of_applications'=>2,
                'no_of_challenges'=>2,
                'tax'=>0,
                'tax_id'=>$tax_rate->id,
                'stripe_plan_id'=>$price->id,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),




            ],

        ];
        Plan::insert($tags);

    }
}
