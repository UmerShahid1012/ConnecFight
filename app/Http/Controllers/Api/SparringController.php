<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CheckIn;
use App\Models\Dispute;
use App\Models\Fight;
use App\Models\FightFacility;
use App\Models\FightMedia;
use App\Models\Sparring;
use App\Models\SparringOffer;
use App\Models\User;
use App\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SparringController extends Controller
{
    public function send_offer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sparring_id' => 'required|integer|exists:sparrings,id',

        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check = Sparring::find($request->sparring_id);

        if (!$check) {
            return sendError('No sparring job found!', null);
        }

        if ($check->assigned_to != null) {
            return sendError('Sparring job is assigned to another fighter!', null);
        }

        $check2 = SparringOffer::where(['sparring_id' => $check->id, 'offer_by' => Auth::id()])->first();

        if (!Auth::user()->stripe_payout_account_id ){
            return sendError('Connect to stripe before posting sparring offer..!', null);

        }
        if (!Auth::user()->defaultCard){
            return sendError('Add your card before posting sparring offer..!', null);

        }
        if ($check2) {
            return sendError('You have already applied to this job!', null);

        }

        $offer = SparringOffer::create(['sparring_id' => $check->id, 'offer_by' => Auth::id(), 'status' => 8]);


        return sendSuccess('Offer sent successfully!', null);


    }

    public function accept_offer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sparring_id' => 'required|integer',
            'user_id' => 'required|integer',

        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check = Sparring::find($request->sparring_id);

        if (!$check) {
            return sendError('No sparring job found!', null);
        }

        if ($check->assigned_to != null) {
            return sendError('Sparring job is assigned to another fighter!', null);

        }
        $check->status = 7;
        $check->assigned_to = $request->user_id;
        $check->save();

        $check2 = SparringOffer::where(['sparring_id' => $check->id, 'offer_by' => $request->user_id])->first();

        if (!$check2) {
            return sendError('No offer found from this user!', null);
        }
        Sparring::where('id', $request->sparring_id)->update(['assigned_to' => $request->user_id]);

        $check2->status = 7;
        $check2->save();


        SparringOffer::where('sparring_id', $request->sparring_id)->where('offer_by', '!=', $request->user_id)->update(['status' => 6]);


        return sendSuccess('Offer accepted successfully!', null);


    }
    public function no_response_checkin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',

        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check = CheckIn::find($request->id);

        if (!$check) {
            return sendError('No checkin found!', null);
        }

//        CheckIn::where('id',$request->id)->update(['status'=>19]);
        $check->status = 19;
        $check->save();





        return sendSuccess('Status Updated!', null);


    }

    public function cancel_offer(Request $request){
        $validator = Validator::make($request->all(), [
            'sparring_id' => 'required|integer',
            'is_poster' => 'required|boolean',

        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check = Sparring::find($request->sparring_id);

        if (!$check) {
            return sendError('No sparring job found!', null);
        }

        if ($check->assigned_to == null) {
            return sendError('Sparring job is not assigned to any fighter!', null);

        }

        if ($request->is_poster == true) {
            SparringOffer::where(['offer_by' => $check->assigned_to, 'sparring_id' => $check->id])->update(['status' => 10]);
            $check->status = 10;

        }else{
            SparringOffer::where(['offer_by' => $check->assigned_to, 'sparring_id' => $check->id])->update(['status' => 11]);
            $check->status = 11;
        }
        $check->assigned_to = null;
        $check->save();

        return sendSuccess('Offer canceled successfully!', null);



    }

    public function disputeFight(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'is_sparring' => 'required|boolean',
            'reason' => 'required|string',

        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        if ($request->is_sparring == true){
            $check = Sparring::find($request->id);
            if (!$check){
                return sendError('No sparring found',null);
            }

            Dispute::create([
                'sparring_id'=> $request->id,
                'reason'=> $request->reason,
                'user_id'=> Auth::id(),
            ]);

            return sendSuccess('Dispute Submitted to Admin Successfully!',null);
        }else{
            $check = Fight::find($request->id);
            if (!$check){
                return sendError('No fight found',null);
            }

            Dispute::create([
                'fight_id'=> $request->id,
                'reason'=> $request->reason,
                'user_id'=> Auth::id(),
            ]);

            return sendSuccess('Dispute Submitted to Admin Successfully!',null);

        }

        return sendError('Something went wrong..!',null);



    }

    public function edit_sparring(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check = Sparring::find($request->id);

        if (!$check) {
            return sendError('No sparring found!', null);
        }


        $title = isset($request->title) ? $request->title : $check->title;
        $description = isset($request->description) ? $request->description : $check->description;
        $location = isset($request->location) ? $request->location : $check->location;
        $start_date = isset($request->start_date) ? $request->start_date : $check->start_date;
        $end_date = isset($request->end_date) ? $request->end_date : $check->end_date;
        $event_id = isset($request->event_id) ? $request->event_id : $check->event_id;
        $budget_per_week = isset($request->budget_per_week) ? $request->budget_per_week : $check->budget_per_week;
        $no_of_weeks = isset($request->no_of_weeks) ? $request->no_of_weeks : $check->no_of_weeks;
        $gender = isset($request->gender) ? $request->gender : $check->gender;
        $session_per_week = isset($request->session_per_week) ? $request->session_per_week : $check->session_per_week;


        $sparring = Sparring::find($check->id);
        $sparring->title = $title;
        $sparring->location = $location;
        $sparring->start_date = $start_date;
        $sparring->end_date = $end_date;
        $sparring->budget_per_week = $budget_per_week;
        $sparring->no_of_weeks = $no_of_weeks;
        $sparring->event_id = $event_id;
        $sparring->description = $description;
        $sparring->gender = $gender;
        $sparring->session_per_week = $session_per_week;
        if ($sparring->save()) {
            if ($request->has('facilities')) {
                FightFacility::where('sparring_id', $check->id)->delete();
                foreach ($request->facilities as $f) {
                    FightFacility::create(['sparring_id'=>$sparring->id,'facility_id'=>$f]);
                }
            }
            if ($request->has('ids')) {
                FightMedia::where('sparring_id', $check->id)->delete();
                foreach ($request->ids as $id) {
                    FightMedia::where('id', $id)->update(['sparring_id' => $sparring->id]);
                }
            }

            $data['sparrings'] = Sparring::with('event_id:id,title', 'media', 'status:id,name', 'assigned_to:id,first_name,last_name', 'user', 'user.stance_id:id,title','facilities','facilities.facility_id:id,name,checkins')->where('id', $sparring->id)->first()->toArray();
            return sendSuccess("Sparring Updated Successfully!", $data);


        }
    }

    public function offers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required'
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        if ($request->type == "applied") {
            $per_page = isset($request->per_page) ? $request->per_page : 20;
            $ids= SparringOffer::where('offer_by', Auth::id())->pluck('sparring_id');
//            $off = SparringOffer::with('sparring', 'sparring.status', 'sparring.event_id', 'sparring.media', 'status', 'user','sparring.facilities','sparring.facilities.facility_id:id,name')->where('offer_by', Auth::id())->orderBy('id', 'desc')->get();
//            foreach ($off as $o) {
//                $o->sparring;
//                $o->sparring->user = User::withCount('followers', 'followings', 'group')->with('tags', 'tags.tag_id', 'tags.sub_tags.sub_tag', 'notification_setting', 'docs', 'stance_id:id,title')->with(['tags.sub_tags' => function ($a) use ($o) {
//                    $a->where('user_id', (int)$o->sparring->user_id);
//                }])->where('id', $o->sparring->user_id)->first();
//            }
            $off = Sparring::withCount('offers')->with('offers', 'offers.status', 'media', 'event_id:id,title',
                'status','facilities','facilities.facility_id:id,name','checkins','checkins.status')
//                ->with('user','user.tags', 'user.tags.tag_id', 'user.tags.sub_tags.sub_tag', 'user.notification_setting', 'user.docs', 'user.stance_id:id,title')->with(['user.tags.sub_tags' => function ($a) use ($per_page) {
//                    $a->where('user_id', Auth::id());
//                }])
                ->with('user','user.followers', 'user.followings', 'user.group','user.tags', 'user.tags.tag_id', 'user.tags.sub_tags.sub_tag', 'user.notification_setting', 'user.docs', 'user.stance_id:id,title')->with(['user.tags.sub_tags' => function ($a) use ($per_page) {
                    $a->where('user_id', Auth::id());
                }])
//                ->with('assigned_to','assigned_to.tags', 'assigned_to.tags.tag_id', 'assigned_to.tags.sub_tags.sub_tag', 'assigned_to.notification_setting', 'assigned_to.docs', 'assigned_to.stance_id:id,title')->with(['assigned_to.tags.sub_tags' => function ($a) use ($i) {
//                    $a->where('user_id', Auth::id());
//                }])
                ->whereIn('id', $ids)
                ->orderBy('id', 'desc')
                ->get();
            foreach ($off as $o) {
                $o->assigned_to = User::withCount('followers', 'followings', 'group')->with('tags', 'tags.tag_id', 'tags.sub_tags.sub_tag', 'notification_setting', 'docs', 'stance_id:id,title')->with(['tags.sub_tags' => function ($a) use ($o) {
                    $a->where('user_id', (int)$o->assigned_to);
                }])->where('id', $o->assigned_to)->first();
                if ($o->offers) {
                    foreach ($o->offers as $i) {
                        $i->user = User::withCount('followers', 'followings', 'group')->with('tags', 'tags.tag_id', 'tags.sub_tags.sub_tag', 'notification_setting', 'docs', 'stance_id:id,title')->with(['tags.sub_tags' => function ($a) use ($i) {
                            $a->where('user_id', (int)$i->offer_by);
                        }])->where('id', $i->offer_by)->first();
                    }
                }
            }
            $offers = (new Collection($off))->paginate($per_page);


            return sendSuccess('Sparring Applied!', $offers);
        } elseif ($request->type == "posted") {
            $per_page = isset($request->per_page) ? $request->per_page : 20;

            $off = Sparring::withCount('offers')->with('offers', 'offers.status', 'media', 'event_id:id,title',
                 'status','facilities','facilities.facility_id:id,name','checkins','checkins.status')
//                ->with('user','user.tags', 'user.tags.tag_id', 'user.tags.sub_tags.sub_tag', 'user.notification_setting', 'user.docs', 'user.stance_id:id,title')->with(['user.tags.sub_tags' => function ($a) use ($per_page) {
//                    $a->where('user_id', Auth::id());
//                }])
                ->with('user','user.followers', 'user.followings', 'user.group','user.tags', 'user.tags.tag_id', 'user.tags.sub_tags.sub_tag', 'user.notification_setting', 'user.docs', 'user.stance_id:id,title')->with(['user.tags.sub_tags' => function ($a) use ($per_page) {
                    $a->where('user_id', Auth::id());
                }])
//                ->with('assigned_to','assigned_to.tags', 'assigned_to.tags.tag_id', 'assigned_to.tags.sub_tags.sub_tag', 'assigned_to.notification_setting', 'assigned_to.docs', 'assigned_to.stance_id:id,title')->with(['assigned_to.tags.sub_tags' => function ($a) use ($i) {
//                    $a->where('user_id', Auth::id());
//                }])
                ->where('user_id', Auth::id())
                ->orderBy('id', 'desc')
                ->get();
            foreach ($off as $o) {
                $o->assigned_to = User::withCount('followers', 'followings', 'group')->with('tags', 'tags.tag_id', 'tags.sub_tags.sub_tag', 'notification_setting', 'docs', 'stance_id:id,title')->with(['tags.sub_tags' => function ($a) use ($o) {
                    $a->where('user_id', (int)$o->assigned_to);
                }])->where('id', $o->assigned_to)->first();
                if ($o->offers) {
                    foreach ($o->offers as $i) {
                        $i->user = User::withCount('followers', 'followings', 'group')->with('tags', 'tags.tag_id', 'tags.sub_tags.sub_tag', 'notification_setting', 'docs', 'stance_id:id,title')->with(['tags.sub_tags' => function ($a) use ($i) {
                            $a->where('user_id', (int)$i->offer_by);
                        }])->where('id', $i->offer_by)->first();
                    }
                }
            }
//            $offers = Sparring::withCount('offers')->with('offers', 'offers.status', 'offers.user','offers.user.tags','offers.user.tags.tag_id','offers.user.tags.sub_tags.sub_tag','offers.user.stance_id', 'media', 'event_id:id,title', 'assigned_to:id,first_name,last_name', 'status')->where('user_id', Auth::id())->orderBy('id', 'desc')->paginate($per_page);
            $offers = (new Collection($off))->paginate($per_page);

            return sendSuccess('Sparring Posted!', $offers);


        } else {
            return sendError('Type Undefined..!', null);
        }
    }


}

