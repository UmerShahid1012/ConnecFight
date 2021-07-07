<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\UserPlanCounter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Cashier\Subscription;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::all();
        $plan = Subscription::where(['user_id'=>Auth::id(),'status_id'=>13])->first();
//        dd($plan,Auth::id());
        foreach ($plans as $p){
        if ($p->id == $plan->stripe_plan){
            $p->is_plan = 1;
        }else{
            $p->is_plan = 0;
        }
        }
        $response['result'] = $plans;

        return sendSuccess("All plans!", $response);
    }

    public function newPlan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',

        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check =  Plan::find($request->id);
        if (!$check){
            return sendError('No plan found',null);
        }

        if (!Auth::user()->defaultCard){
            return sendError('No card found, Add card to continue',null);
        }

        $old_plan = Subscription::where(['user_id'=>Auth::id(),'status_id'=>13])->first();
//        dd($old_plan);

        if ($old_plan->stripe_plan == 1){
            return sendError('You can not renew free plan before expiry!',null);

        }
        $old_plan->status_id = 12;
        $old_plan->save();

        $subscription =  \Stripe\Subscription::create([
            "customer" => Auth::user()->stripe_id,
                "default_source" => Auth::user()->card->stripe_id,

            "items" => [
                ["price" => $check->stripe_plan_id],
            ],
            "tax_rates"=> [$check->tax_id],

        ]);

        if ($check->tax != 0) {
            $tax = ($check->tax / 100) * $check->price;
        }else{
            $tax = $check->price;
        }


        $month = Carbon::now()->addMonth()->month;
        $end_date = Carbon::create(Carbon::now()->year,$month , 5);
        $sub['subscription'] = Subscription::create([
            'user_id'=>Auth::id(),
            'name' => 'Free',
            'stripe_id' => $subscription->id,
            'stripe_status' => $subscription->status,
            'stripe_plan' => $check->id,
            'ends_at' =>$end_date,
        ]);
        UserPlanCounter::where(['user_id'=>Auth::user()->id])->update(['no_of_sparrings'=>0,'no_of_applications'=>0,'no_of_challenges'=>0]);

        Transaction::create([
            'from'=>Auth::id(),
            'type'=>'New Subscription',
            'charge_id'=> $subscription->id,
            'amount'=>$tax,
        ]);

        return sendSuccess('Plan purchased successfully..!',$sub);

    }

    public function cancel()
    {
        $plan =  Plan::find(1);
         Subscription::where(['user_id'=>Auth::id(),'status_id'=>13])->update(['status_id'=>14]);
         Subscription::where(['user_id'=>Auth::id(),'stripe_plan'=>1])->update(['status_id'=>13]);
         UserPlanCounter::where(['user_id'=>Auth::user()->id])->update(['no_of_sparrings'=>0,'no_of_applications'=>0,'no_of_challenges'=>0]);

         return sendSuccess('Subscription Canceled Successfully',null);
    }
    public function myPlan()
    {
        $subscription= Subscription::with('plan')->where(['user_id'=>Auth::id(),'status_id'=>13])->first()->toArray();
      $counter =  ['used_no_of_sparrings' => Auth::user()->plan_counter->no_of_sparrings,
        'used_no_of_applications' => Auth::user()->plan_counter->no_of_applications,
        'used_no_of_challenges' => Auth::user()->plan_counter->no_of_challenges
        ];

        $sub['subscription'] = array_merge($subscription,$counter);
         return sendSuccess('My Plan',$sub);
    }

}
