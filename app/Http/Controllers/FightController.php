<?php

namespace App\Http\Controllers;

use App\Models\Fight;
use App\Models\Highlight;
use App\Models\Sparring;
use App\Models\User;
use App\Models\UserTags;
use Illuminate\Http\Request;
use Stripe\Charge;

class FightController extends Controller
{
    public function sparrings()
    {
        $artists = Sparring::with('assign', 'event')->get();
        $data['title'] = 'Sparring List';
        return view('admin.fights.sparrings', $data)->with('sparrings', $artists);
    }

    public function highlights()
    {
        $artists = Highlight::with('user')->get();
        $data['title'] = 'Highlights List';
        return view('admin.fights.highlights', $data)->with('highlights', $artists);
    }

    public function fights()
    {
        $artists = Fight::all();
        $data['title'] = 'Fight List';
        return view('admin.fights.fights', $data)->with('fights', $artists);
    }

    public function decide_winner(Request $request)
    {
        $fight = Fight::find($request->id);
        $fight->winner_id = $request->winner;
        $fight->status = 9;
        $fight->save();
        $f1 = $fight->challenger;
        $f2 = $fight->defender;
        $salary = $fight->fund/2;
        $f1_tags = UserTags::where('user_id', $f1)->whereNotNull('sub_tag_id')->get();
        $f1_tags_count = UserTags::where('user_id', $f1)->whereNotNull('sub_tag_id')->count();
        $f2_tags = UserTags::where('user_id', $f2)->whereNotNull('sub_tag_id')->count();
        $posted_by = User::find($fight->posted_by);

        if ($f1_tags_count == 1) {
            //$3000
            foreach ($f1_tags as $t) {
                $sub_tag = Fight::Join('events', 'events.id', '=', 'fights.event_id')
                    ->where(function ($q) use ($f1) {
                        $q->where('fights.challenger', $f1);
                        $q->orWhere('fights.defender', $f1);
                    })
                    ->where(['fights.status' => 9])
                    ->where('events.sub_tag_id', $t->sub_tag_id)
                    ->sum('fund');

                $sub_tag = $sub_tag / 2;

                if ($t->sub_tag_id == 5){
                    if ($sub_tag < 3000){
                        $amount = (8/100)*$salary;


                    }
                }
                $stripeCharge = Charge::create(array(
                    'customer' => $posted_by->stripe_id,
                    'source'=>$posted_by->defaultCard->stripe_id,
                    'amount' => (100*$amount),
                    'description' => 'Fight match salary!',
                    'currency' => 'usd',
                ));
            }


        }

        return redirect()->back()->with('success', 'Winner Selected Successfully');
    }

    public function ko(Request $request)
    {
        $fight = Fight::find($request->id);
        $fight->is_ko = $request->type;
        $fight->save();
        return redirect()->back()->with('success', 'Winner Selected Successfully');
    }

    public function destroy($id)
    {
        Sparring::where('id', $id)->forceDelete();
        return \redirect()->route('admin.sparring')->with('success', 'Sparring Deleted Successfully');
    }

    public function fightDestroy($id)
    {
        Fight::where('id', $id)->forceDelete();
        return \redirect()->route('admin.fights')->with('success', 'Fight Deleted Successfully');
    }
}
