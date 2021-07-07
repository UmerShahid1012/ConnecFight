<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\BlockUser;
use App\Models\CheckIn;
use App\Models\Dispute;
use App\Models\Event;
use App\Models\Fight;
use App\Models\FightFacility;
use App\Models\FightMedia;
use App\Models\Notification;
use App\Models\Sparring;
use App\Models\SparringOffer;
use App\Models\Stance;
use App\Models\User;
use App\Models\UserTags;
use App\Support\Collection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FightController extends Controller
{
    public function all_fighters(Request $request)
    {
        $tags = UserTags::where('tag_id', 4)->where('sub_tag_id', '!=', null)->where('user_id', '!=', Auth::id())->distinct('user_id')->pluck('user_id');
        $users = array();
        foreach ($tags as $key => $tag) {
            $check = BlockUser::where(['block_by' => Auth::id(), 'blocked_user' => $tag])->first();
            $user = null;
            if ($check == null) {

                $user = User::withCount('followers', 'followings')->with('tags', 'tags.tag_id', 'tags.sub_tags.sub_tag', 'notification_setting', 'docs', 'stance_id:id,title')->with(['tags.sub_tags' => function ($a) use ($tag) {
                    $a->where('user_id', $tag);
                }])->where('id', $tag)->first();

            }

            if ($user != null) {
                $users[] = $user;
            }
        }
        $per_page = isset($request->per_page) ? $request->per_page : 20;


        $data['result'] = (new Collection($users))->paginate($per_page);


        return sendSuccess("All fighters!", $data);
    }

    public function search_fighters(Request $request)
    {
        $tags = UserTags::where('tag_id', 4)->where('sub_tag_id', '!=', null)->where('user_id', '!=', Auth::id())->distinct('user_id')->pluck('user_id');
        $users = array();
        $name = $request->name;
        foreach ($tags as $key => $tag) {
            $check = BlockUser::where(['block_by' => Auth::id(), 'blocked_user' => $tag])->first();
            $user = null;
            if ($check == null) {

                $user = User::withCount('followers', 'followings')->with('tags', 'tags.tag_id', 'tags.sub_tags.sub_tag', 'notification_setting', 'docs', 'stance_id:id,title')->with(['tags.sub_tags' => function ($a) use ($tag) {
                    $a->where('user_id', $tag);
                }])->where('id', $tag)->where(function ($q) use ($name) {
                    $q->Where('first_name', 'like', '%' . $name . '%');
                    $q->orWhere('last_name', 'like', '%' . $name . '%');
                })->first();

            }

            if ($user != null) {
                $users[] = $user;
            }
        }
        $per_page = isset($request->per_page) ? $request->per_page : 20;


        $data['result'] = (new Collection($users))->paginate($per_page);


        return sendSuccess("Searched fighters!", $data);
    }


    public function all_events()
    {
        $user = Event::all();
        $response['result'] = $user;

        return sendSuccess("All events!", $response);
    }

    public function all_stances()
    {
        $user = Stance::all();
        $response['result'] = $user;

        return sendSuccess("All stances!", $response);
    }

//    public function apply_sparring(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'sparring_id' => 'required'
//        ]);
//
//        if ($validator->fails()) {
//
//            return sendError($validator->errors()->all()[0], null);
//
//        }
//        $check = Sparring::find($request->sparring_id);
//        if (!$check) {
//            return sendError("Sparring fight not found!", null);
//
//        }
//        $offer = SparringOffer::create(['sparring_id' => $request->sparring_id, 'offer_by' => Auth::id()]);
//
//        return sendSuccess("Your request is sent.!", null);
//
//    }

    public function challenges(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required'
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }
        $per_page = isset($request->per_page) ? $request->per_page : 20;


        if ($request->type == 'sent') {


            $challenges = Fight::with('challenger', 'defender', 'defender', 'media', 'posted_by', 'event_id:id,title', 'status', 'defender.stance_id:id,title', 'challenger.stance_id:id,title', 'posted_by.stance_id:id,title', 'winner.stance_id:id,title', 'facilities', 'facilities.facility_id:id,name')->where('challenger', Auth::id())->paginate(20);

            return sendSuccess('Sent Challenges List.!', $challenges);

        } elseif ($request->type == 'received') {


            $challenges = Fight::with('challenger', 'defender', 'challenger', 'posted_by', 'event_id:id,title', 'media', 'status', 'defender.stance_id:id,title', 'challenger.stance_id:id,title', 'posted_by.stance_id:id,title', 'winner.stance_id:id,title', 'facilities', 'facilities.facility_id:id,name')->where('defender', Auth::id())->paginate(20);

            return sendSuccess('Received Challenges List.!', $challenges);

        } elseif ($request->type == 'arrange_by') {

            $challenges = Fight::with('challenger', 'defender', 'challenger', 'posted_by', 'event_id:id,title', 'media', 'status', 'defender.stance_id:id,title', 'challenger.stance_id:id,title', 'posted_by.stance_id:id,title', 'winner.stance_id:id,title', 'facilities', 'facilities.facility_id:id,name')->where('posted_by', Auth::id())->paginate(20);

            return sendSuccess('My Arranged Fights List.!', $challenges);

        } else {

            return sendError('Undefined Type!', null);
        }
    }

    public function send_challenge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'challenger_id' => 'required|integer|exists:users,id',
            'defender_id' => 'required|integer|exists:users,id',
            'event_id' => 'required|integer|exists:events,id',
            'event_host' => 'required',
            'location' => 'required',
            'match_date' => 'required',
            'match_fund' => 'required|integer',
            'no_of_rounds' => 'required',
//            'gender' => 'required',
//            'sports' => 'required',
            'description' => 'required',
            'facilities' => 'required',
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }
        $challenge = new Fight();
        $challenge->challenger = $request->challenger_id;
        $challenge->defender = $request->defender_id;
        $challenge->event_id = $request->event_id;
        $challenge->event_host = $request->event_host;
        $challenge->location = $request->location;
        $challenge->match_date = $request->match_date;
        $challenge->fund = $request->match_fund;
        $challenge->no_of_rounds = $request->no_of_rounds;
        $challenge->sports = isset($request->sports) ? $request->sports : "";
//        $challenge->gender = $request->gender;
        $challenge->description = $request->description;
        $challenge->description = $request->description;
        if (Auth::id() == $request->challenger_id) {
            $challenge->fighter_one_accepted = true;
            $challenge->status = 2;
        }
        $challenge->posted_by = Auth::id();
        $challenge->status = 1;

        if ($challenge->save()) {

            foreach ($request->facilities as $f) {
                FightFacility::create(['fight_id' => $challenge->id, 'facility_id' => $f]);
            }

            $response['result'] = Fight::with('event_id:id,title', 'defender', 'challenger', 'posted_by', 'winner', 'status:id,name', 'defender.stance_id:id,title', 'challenger.stance_id:id,title', 'posted_by.stance_id:id,title', 'winner.stance_id:id,title', 'facilities', 'facilities.facility_id:id,name')->where('id', $challenge->id)->get();
            return sendSuccess('Challenge sent successfully!', $response);
        }


    }

    public function accept_reject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fight_id' => 'required|integer|exists:fights,id',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }
        $check = Fight::with('event_id:id,title', 'defender', 'challenger', 'posted_by', 'winner', 'status:id,name', 'defender.stance_id:id,title', 'challenger.stance_id:id,title', 'posted_by.stance_id:id,title', 'winner.stance_id:id,title')->find($request->fight_id);
//        dd($check->fighter_one_accepted);

        if (empty($check)) {
            sendError('No fight found!', null);
        }


        if ($check->challenger == Auth::id()) {
            if ($check->fighter_one_accepted == 1) {
                return sendError("You already accepted this fight", null);
            }
            if ($check->status == 4) {
                return sendError("You already rejected this fight", null);
            }
            if ($request->status == 1) {

                $fight = Fight::with('event_id:id,title', 'defender', 'challenger', 'posted_by', 'winner', 'status:id,name')->find($request->fight_id);
                $fight->fighter_one_accepted = 1;
                $fight->status = 2;
                if ($fight->save()) {
                    $response['result'] = Fight::with('event_id:id,title', 'defender', 'challenger', 'posted_by', 'winner', 'status:id,name', 'defender.stance_id:id,title', 'challenger.stance_id:id,title', 'posted_by.stance_id:id,title', 'winner.stance_id:id,title', 'facilities', 'facilities.facility_id:id,name')->where('id', $fight->id)->first();
                    return sendSuccess('Challenge accepted successfully!', $response);
                }


            } else {
                $fight = Fight::with('event_id:id,title', 'defender', 'challenger', 'posted_by', 'winner', 'status:id,name', 'defender.stance_id:id,title', 'challenger.stance_id:id,title', 'posted_by.stance_id:id,title', 'winner.stance_id:id,title')->find($request->fight_id);
                $fight->fighter_one_accepted = 0;
                if ($check->fighter_two_accepted == 1) {
                    $fight->status = 7;
                } else {
                    $fight->status = 4;
                }

                if ($fight->save()) {
                    $response['result'] = Fight::with('event_id:id,title', 'defender', 'challenger', 'posted_by', 'winner', 'status:id,name', 'defender.stance_id:id,title', 'challenger.stance_id:id,title', 'posted_by.stance_id:id,title', 'winner.stance_id:id,title', 'facilities', 'facilities.facility_id:id,name')->where('id', $fight->id)->first();
                    return sendSuccess('Challenge rejected successfully!', $response);
                }
            }

        } elseif ($check->defender == Auth::id()) {

            if ($check->fighter_two_accepted == 1) {
                return sendError("You already accepted this fight", null);
            }
            if ($check->status == 5) {
                return sendError("You already rejected this fight", null);
            }
            if ($request->status == 1) {

                $fight = Fight::with('event_id:id,title', 'defender', 'challenger', 'posted_by', 'winner', 'status:id,name')->find($request->fight_id);
                $fight->fighter_two_accepted = 1;
                if ($check->fighter_one_accepted == 1) {
                    $fight->status = 7;

                } else {
                    $fight->status = 3;

                }
                if ($fight->save()) {
                    $response['result'] = Fight::with('event_id:id,title', 'defender', 'challenger', 'posted_by', 'winner:id,first_name,last_name', 'status:id,name', 'defender.stance_id:id,title', 'challenger.stance_id:id,title', 'posted_by.stance_id:id,title', 'winner.stance_id:id,title', 'facilities', 'facilities.facility_id:id,name')->where('id', $fight->id)->first();
                    return sendSuccess('Challenge accepted successfully!', $response);
                }
            } else {
                $fight = Fight::with('event_id:id,title', 'defender', 'challenger', 'posted_by', 'winner:id,first_name,last_name', 'status:id,name', 'defender.stance_id:id,title', 'challenger.stance_id:id,title', 'posted_by.stance_id:id,title', 'winner.stance_id:id,title')->find($request->fight_id);
                $fight->fighter_two_accepted = 0;
                $fight->status = 5;
                if ($fight->save()) {
                    $response['result'] = Fight::with('event_id:id,title', 'defender', 'challenger', 'posted_by', 'winner:id,first_name,last_name', 'status:id,name', 'defender.stance_id:id,title', 'challenger.stance_id:id,title', 'posted_by.stance_id:id,title', 'winner.stance_id:id,title', 'facilities', 'facilities.facility_id:id,name')->where('id', $fight->id)->first();
//                    $response['result'] = $fight;
                    return sendSuccess('Challenge rejected successfully!', $response);
                }
            }
        } else {
            return sendError('You are not among the fighters you can not accept or reject fight!', null);
        }
    }

    public function filter_according_to_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_sparring' => 'required|boolean',
            'status' => 'required',
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        if ($request->is_sparring == true) {
            $validator = Validator::make($request->all(), [
                'is_applied' => 'required|boolean',
            ]);

            if ($validator->fails()) {

                return sendError($validator->errors()->all()[0], null);

            }

            if ($request->is_applied == true) {

                $per_page = isset($request->per_page) ? $request->per_page : 20;
                if($request->status == 'all'){
                    $ids= SparringOffer::where('offer_by', Auth::id())->pluck('sparring_id');
                } else {
                    $ids= SparringOffer::where('offer_by', Auth::id())->where('status',$request->status)->pluck('sparring_id');
                }
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
            } else {
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
                    ->where('user_id', Auth::id());

                    if($request->status != 'all'){
                        $off->where('status',$request->status);
                    }

                    $off->orderBy('id', 'desc')
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
            }
        } else {
            $validator = Validator::make($request->all(), [
                'type' => 'required',
            ]);

            if ($validator->fails()) {

                return sendError($validator->errors()->all()[0], null);

            }

            $per_page = isset($request->per_page) ? $request->per_page : 20;


            if ($request->type == 'sent') {


                $challenges = Fight::with('challenger', 'defender', 'defender', 'media', 'posted_by', 'event_id:id,title', 'status', 'defender.stance_id:id,title', 'challenger.stance_id:id,title', 'posted_by.stance_id:id,title', 'winner.stance_id:id,title', 'facilities', 'facilities.facility_id:id,name')
                    ->where('challenger', Auth::id());

                    if($request->status != 'all'){
                        $challenges->where('status',$request->status);
                    }

                    $challenges->paginate(20);

                return sendSuccess('Sent Challenges List.!', $challenges);

            } elseif ($request->type == 'received') {


                $challenges = Fight::with('challenger', 'defender', 'challenger', 'posted_by', 'event_id:id,title', 'media', 'status', 'defender.stance_id:id,title', 'challenger.stance_id:id,title', 'posted_by.stance_id:id,title', 'winner.stance_id:id,title', 'facilities', 'facilities.facility_id:id,name')
                    ->where('defender', Auth::id());
                    if($request->status != 'all'){
                        $challenges->where('status',$request->status);
                    }
                    $challenges->where('status', $request->status)
                    ->paginate(20);

                return sendSuccess('Received Challenges List.!', $challenges);

            } elseif ($request->type == 'arrange_by') {

                $challenges = Fight::with('challenger', 'defender', 'challenger', 'posted_by', 'event_id:id,title', 'media', 'status', 'defender.stance_id:id,title', 'challenger.stance_id:id,title', 'posted_by.stance_id:id,title', 'winner.stance_id:id,title', 'facilities', 'facilities.facility_id:id,name')
                    ->where('posted_by', Auth::id());
                    if($request->status != 'all'){
                        $challenges->where('status',$request->status);
                    }
                $challenges->paginate(20);

                return sendSuccess('My Arranged Fights List.!', $challenges);

            } else {

                return sendError('Undefined Type!', null);
            }
        }
    }

    public function checkin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'id' => 'required',
            'is_fight' => 'required|boolean'
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }
        if ($request->is_fight == true) {
            $fight = Fight::find($request->id);
            if (!$fight) {
                return sendError('No Fight Found!', null);
            }

            if ($request->type == "checkin") {
                CheckIn::create(['fight_id' => $fight->id, 'checkin' => Carbon::now(), 'is_confirmed' => false]);
                $sender = Auth::user();
                $notification = new Notification();
                $notification->sender_name = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                $notification->user_id = Auth::id();
                $notification->on_user = $fight->challenger;
                $notification->type = 'fight';
                $notification->notification_text = $sender->first_name . ' ' . $sender->last_name . ' has check-in in fight please confirm to continue.';
                $notification->profile_photo = $sender->profile_image;
                $notification->save();
                $data['notification'] = $notification;
                $noti_con = new NotificationController;
                $noti_con->sendPushNotification([$fight->challenger], $notification->notification_text, $notification);

                $notification = new Notification();
                $notification->sender_name = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                $notification->user_id = Auth::id();
                $notification->on_user = $fight->posted_by;
                $notification->type = 'fight';
                $notification->notification_text = $sender->first_name . ' ' . $sender->last_name . ' has check-in in fight please confirm to continue.';
                $notification->profile_photo = $sender->profile_image;
                $notification->save();
                $data['notification'] = $notification;
                $noti_con = new NotificationController;
                $noti_con->sendPushNotification([$fight->posted_by], $notification->notification_text, $notification);

                return sendSuccess('Checkin successfully!', null);

            } elseif($request->type == 'start'){
                $validator = Validator::make($request->all(), [
                    'is_overweight' => 'required|boolean',
                    'is_defender' => 'required|boolean',
                ]);
                if ($request->is_defender == false) {
                    Fight::where('id', $fight->id)->update(['is_overweight' => $request->is_overweight, 'start' => Carbon::now()]);
                }else{
                    Fight::where('id', $fight->id)->update(['is_overweight' => $request->is_overweight, 'def_start' => Carbon::now()]);

                }
                return sendSuccess('Fight started successfully!', null);

            } elseif ($request->type == 'end'){
                Fight::where('id', $fight->id)->update(['end' => Carbon::now()]);
                return sendSuccess('Fight ended successfully!', null);

            } elseif ($request->type == 'confirm'){
                $validator = Validator::make($request->all(), [
                    'status' => 'required|boolean',
                ]);

                if ($validator->fails()) {

                    return sendError($validator->errors()->all()[0], null);

                }
                CheckIn::where('fight_id', $fight->id)->update(['is_confirmed', $request->status]);
                return sendSuccess('Updated Successfully', null);
            }
        } else {
            $sparring = Sparring::find($request->sparring_id);
            if ($sparring->assigned_to == null) {
                return sendError('Sparring not assigned to any fighter', $sparring);
            }
            if ($request->type == "checkin") {
//                $check = CheckIn::where('id',$request->id)->whereNull('checkin')->first();
//                if(empty($check)){
                    $check = CheckIn::where('id',$request->id)->update(['checkin'=> Carbon::now(),'status'=>16]);
//                }
            } else if ($request->type == "checkout") {
//                $check = CheckIn::where('id',$request->id)->whereNotNull('checkout')->first();
//                if(empty($check)){
                    $check = CheckIn::where('id',$request->id)->update(['checkout'=> Carbon::now()]);
//                }
            }else if ($request->type == "confirm") {
                $check = CheckIn::where('id',$request->id)->first();
//                if(empty($check)){
                    $check = CheckIn::where('id',$request->id)->update(['status'=> 17]);
//                }
            }

            return sendSuccess(ucwords($request->type).' Successfully', null);

        }
    }
    public function reportIssue(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|integer',
            'description' => 'required|string',

        ]);

        if ($validator->fails()) {
            return sendError($validator->errors()->all()[0], null);
        }

        $d =  Dispute::create([
            'reason'=> $request->reason,
            'description'=> $request->description,
            'user_id'=> Auth::id(),
        ]);
        if ($request->has('ids')) {
            FightMedia::where('dispute_id', $d->id)->delete();
            foreach ($request->ids as $id) {
                FightMedia::where('id', $id)->update(['dispute_id' => $d->id]);
            }
        }

        return sendSuccess("Report Submitted Successfully",null);
    }


    public function disputeFight(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'is_sparring' => 'required|boolean',
            'reason' => 'required|integer',
            'description' => 'required|string',

        ]);

        if ($validator->fails()) {
            return sendError($validator->errors()->all()[0], null);
        }

        if ($request->is_sparring == true){
            $validator = Validator::make($request->all(), [
                'type' => 'required',

            ]);

            if ($validator->fails()) {
                return sendError($validator->errors()->all()[0], null);
            }

            $check = Sparring::find($request->id);
            if (!$check){
                return sendError('No sparring found',null);
            }
            if ($request->type == 'checkin'){
                $validator = Validator::make($request->all(), [
                    'check_in_id' => 'required|required',

                ]);

                if ($validator->fails()) {
                    return sendError($validator->errors()->all()[0], null);
                }
              $d =  Dispute::create([
                    'sparring_id'=> $request->id,
                    'reason'=> $request->reason,
                    'description'=> $request->description,
                    'user_id'=> Auth::id(),
                    'check_in_id' => $request->check_in_id,
            ]);
                if ($request->has('ids')) {
                    FightMedia::where('dispute_id', $d->id)->delete();
                    foreach ($request->ids as $id) {
                        FightMedia::where('id', $id)->update(['dispute_id' => $d->id]);
                    }
                }
               $checkin =  CheckIn::find($request->check_in_id);
                $checkin->status = 15;
                $checkin->save();
                return sendSuccess('Dispute Submitted to Admin Successfully!',null);


            }
         $d =   Dispute::create([
                'sparring_id'=> $request->id,
                'reason'=> $request->reason,
                'user_id'=> Auth::id(),
                'description'=> $request->description,

            ]);
            if ($request->has('ids')) {
                FightMedia::where('dispute_id', $d->id)->delete();
                foreach ($request->ids as $id) {
                    FightMedia::where('id', $id)->update(['dispute_id' => $d->id]);
                }
            }



            return sendSuccess('Dispute Submitted to Admin Successfully!',null);
        }else{

            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'is_sparring' => 'required|boolean',
                'reason' => 'required|integer',
                'physician_full_name' => 'required',
                'physician_number' => 'required',
                'physician_email' => 'required',
                'promoter_full_name' => 'required',
                'promoter_number' => 'required',
                'promoter_email' => 'required',

            ]);

            if ($validator->fails()) {
                return sendError($validator->errors()->all()[0], null);
            }

            $check = Fight::find($request->id);
            if (!$check){
                return sendError('No fight found',null);
            }

            //here Promoter Info && Physician Info

            $physician['physician_full_name'] = $request->physician_full_name;
            $physician['physician_number'] = $request->physician_number;
            $physician['physician_email'] = $request->physician_email;
            $promoter['promoter_full_name'] = $request->promoter_full_name;
            $promoter['promoter_number'] = $request->promoter_number;
            $promoter['promoter_email'] = $request->promoter_email;

           $d = Dispute::create([
                'fight_id'=> $request->id,
                'reason'=> $request->reason,
                'user_id'=> Auth::id(),
                'description'=> $request->description,
                'physician_info'=> json_encode($physician),
                'promoter_info'=> json_encode($promoter),
            ]);
            if ($request->has('ids')) {
                FightMedia::where('dispute_id', $d->id)->delete();
                foreach ($request->ids as $id) {
                    FightMedia::where('id', $id)->update(['dispute_id' => $d->id]);
                }
            }


            return sendSuccess('Dispute Submitted to Admin Successfully!',null);

        }

        return sendError('Something went wrong..!',null);

    }

    public function withdraw_dispute(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return sendError($validator->errors()->all()[0], null);
        }

        Dispute::where('id',$request->id)->update([
          'status' => 14
        ]);

        return sendSuccess('Dispute Withdraw Successfully!',null);

    }
}
