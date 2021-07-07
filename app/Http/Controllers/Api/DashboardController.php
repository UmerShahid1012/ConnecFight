<?php

namespace App\Http\Controllers\Api;

use App\CampaignImage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\BlockUser;
use App\Models\CheckIn;
use App\Models\Event;
use App\Models\Fight;
use App\Models\FightFacility;
use App\Models\FightMedia;
use App\Models\Follower;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Highlight;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Offer;
use App\Models\Plan;
use App\Models\Review;
use App\Models\Sparring;
use App\Models\SparringOffer;
use App\Models\User;
use App\Models\UserPlanCounter;
use App\Models\UserTags;
use App\Support\Collection;
use Carbon\Carbon;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required'
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }
        $followings = Follower::where('follower_id', Auth::id())->pluck('following');
        if ($request->type == 'highlights') {
            $per_page = isset($request->per_page) ? $request->per_page : 20;

            $highlights = Highlight::withCount('likes')->with('media', 'user', 'user.stance_id:id,title')->get();
            foreach ($highlights as $s) {
                $check2 = Like::where(['highlight_id' => $s->id, 'liked_by' => Auth::id()])->first();
                if (!$check2) {
                    $s->is_liked = false;

                } else {
                    $s->is_liked = true;


                }
            }

            $per_page = isset($request->per_page) ? $request->per_page : 20;

            $data['highlights'] = (new Collection($highlights))->paginate($per_page);
            return sendSuccess("All Highlights!", $data);

        }
        if ($request->type == 'sparrings') {

            $sparrings = Sparring::where('user_id', '!=', Auth::id())
                ->whereNull('assigned_to')
                ->withCount('likes')->with('user', 'user.stance_id:id,title', 'event_id:id,title', 'assigned_to:id,first_name,last_name', 'media', 'status','facilities','facilities.facility_id:id,name')
                ->get();
            $temp = array();
            foreach ($sparrings as $ngo) {
//                dd(Auth::id());
                $check = SparringOffer::where(['sparring_id' => $ngo->id, 'offer_by' => Auth::id()])->first();
//                dd($check);
                if (!$check) {
                    $ngo->is_applied = false;

                } else {
                    $ngo->is_applied = true;

                }
                $check2 = Like::where(['sparring_id' => $ngo->id, 'liked_by' => Auth::id()])->first();
                if (!$check2) {
                    $ngo->is_liked = false;
                } else {
                    $ngo->is_liked = true;

                }
            }


            $per_page = isset($request->per_page) ? $request->per_page : 20;
            $collection['sparrings'] = (new Collection($sparrings))->paginate($per_page);
            return sendSuccess("All Sparring Matches!", $collection);


        }
        if ($request->type == 'fights') {
            $per_page = isset($request->per_page) ? $request->per_page : 20;

            $fights = Fight::withCount('likes')->with('event_id:id,title', 'status:id,name', 'defender', 'defender.stance_id:id,title', 'challenger.stance_id:id,title', 'posted_by.stance_id:id,title', 'challenger', 'posted_by', 'winner', 'winner.stance_id:id,title', 'media', 'status','facilities','facilities.facility_id:id,name')->whereIn('defender', $followings)->orWhereIn('challenger', $followings)->orWhereIn('posted_by', $followings)->get();
            foreach ($fights as $s) {
                $check2 = Like::where(['fight_id' => $s->id, 'liked_by' => Auth::id()])->first();
                if (!$check2) {
                    $s->is_liked = false;
                } else {
                    $s->is_liked = true;

                }
            }

            $per_page = isset($request->per_page) ? $request->per_page : 20;

            $data['fights'] = (new Collection($fights))->paginate($per_page);
            return sendSuccess("All Fights!", $data);

        }

        return sendError('Undefined Type!', null);

    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required'
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }
        $event = isset($request->event_id) ? $request->event_id : null;
        $location = isset($request->location) ? $request->location : null;
        $weight = isset($request->weight) ? $request->weight : null;
        $price = isset($request->price) ? $request->price : null;
        $name = isset($request->name) ? $request->name : null;
//        dd($location);
        $followings = Follower::where('follower_id', 1)->pluck('following');
        if ($request->type == 'sparrings') {
            $per_page = isset($request->per_page) ? $request->per_page : 20;
            if ($name) {
                $ids = User::Where('first_name', 'like', '%' . $name . '%')->orWhere('last_name', 'like', '%' . $name . '%')->distinct('id')->pluck('id');
                $events = Event::Where('title', 'like', '%' . $name . '%')->distinct('id')->pluck('id');

            } else {
                $ids = [];
                $events = [];
            }
            $sparrings = Sparring::with('event_id:id,title', 'assigned_to', 'media', 'user', 'user.stance_id:id,title', 'status:id,name','facilities','facilities.facility_id:id,name')
                ->whereNull('assigned_to')
                ->where(function ($q) use ($ids, $events, $location, $price, $event) {
                    if (!empty($ids)) {
                        $q->whereIn('user_id', $ids);
                    }
                    if (!empty($events)) {
                        $q->orWhereIn('event_id', $events);
                    }
                    if (!empty($location)) {
                        $q->orWhere('location', 'like', '%' . $location . '%');
                    }
                    if (!empty($price)) {
                        $q->orWhere('budget_per_week', 'like', '%' . $price . '%');
                    }
                    if (!empty($event)) {
                        $q->orWhere('event_id', $event);
                    }
                })->get();

            foreach ($sparrings as $s) {
                $check = SparringOffer::with('status')->where(['sparring_id' => $s->id, 'offer_by' => Auth::id()])->first();
                if (!$check) {
                    $s->is_applied = false;
                    $s->my_offer_status = null;
                } else {
                    $s->is_applied = true;
                    $s->my_offer_status = $check->status->name;

                }
                $check2 = Like::where(['sparring_id' => $s->id, 'liked_by' => Auth::id()])->first();
                if (!$check2) {
                    $s->is_liked = false;
                } else {
                    $s->is_liked = true;

                }
            }
            $data['sparrings'] = (new Collection($sparrings))->paginate($per_page);
        } elseif ($request->type == 'fights') {
            if ($name) {
                $ids = User::Where('first_name', 'like', '%' . $name . '%')->orWhere('last_name', 'like', '%' . $name . '%')->distinct('id')->pluck('id');
                $events = Event::Where('title', 'like', '%' . $name . '%')->distinct('id')->pluck('id');

            } else {
                $ids = [];
                $events = [];
            }
//            dd($price);
//            dd($ids,$events,$event,$price,$location);
            $fights = Fight::with('event_id:id,title', 'status:id,name', 'defender', 'challenger', 'posted_by', 'event_id:id,title', 'winner', 'media', 'defender.stance_id:id,title', 'challenger.stance_id:id,title', 'posted_by.stance_id:id,title', 'winner.stance_id:id,title','facilities','facilities.facility_id:id,name')
                ->where(function ($q) use ($ids, $events, $event, $price, $location) {
                    if (!empty($ids)) {
                        $q->whereIn('defender', $ids);
                        $q->orwhereIn('challenger', $ids);
                        $q->orwhereIn('posted_by', $ids);
                    }
                    if (!empty($events)) {
                        $q->orWhereIn('event_id', $events);
                    }
                    if (!empty($event)) {
                        $q->orWhere('event_id', $event);
                    }
                    if (!empty($price)) {
                        $q->orWhere('fund', 'like', '%' . $price . '%');
                    }
                    if (!empty($location)) {

                        $q->orWhere('location', 'like', '%' . $location . '%');
                    }
                })->get();
            foreach ($fights as $s) {
                $check2 = Like::where(['fight_id' => $s->id, 'liked_by' => Auth::id()])->first();
                if (!$check2) {
                    $s->is_liked = false;
                } else {
                    $s->is_liked = true;

                }
            }

            $per_page = isset($request->per_page) ? $request->per_page : 20;

            $data['fights'] = (new Collection($fights))->paginate($per_page);
        } elseif ($request->type == 'matchmakers') {
            $per_page = isset($request->per_page) ? $request->per_page : 20;
            $tags = UserTags::where('tag_id', 1)->where('sub_tag_id', '!=', null)->where('user_id', '!=', Auth::id())->distinct('user_id')->pluck('user_id');
            $users = array();
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


            $data['matchmakers'] = (new Collection($users))->paginate($per_page);

        } elseif ($request->type == 'athletes') {
            $per_page = isset($request->per_page) ? $request->per_page : 20;
            $tags = UserTags::where('tag_id', 4)->where('sub_tag_id', '!=', null)->where('user_id', '!=', Auth::id())->distinct('user_id')->pluck('user_id');
            $users = array();
            foreach ($tags as $tag) {
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

                if (!empty($user)) {
                    $users[] = $user;
                }

            }

            $data['athletes'] = (new Collection($users))->paginate($per_page);
        } else {
            return sendError("Type undefined!", null);

        }
        return sendSuccess("Search Result!", $data);

    }

    public function addPostMedia(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'media' => 'required',
                'type' => 'required'

            ]);
        if ($validator->fails()) {
            return sendError($validator->errors()->all()[0], null);

        }
        if ($request->type == 'image') {
            if ($request->hasfile('media')) {

                $postData = $request->only('media');

                $file = $postData['media'];

                $fileArray = array('media' => $file);

                // Tell the validator that this file should be an image
                $rules = array(
                    'media' => 'mimes:jpeg,jpg,png,gif|required|max:10000' // max 10000kb
                );

                // Now pass the input and rules into the validator
                $validator = Validator::make($fileArray, $rules);


                // Check to see if validation fails or passes
                if ($validator->fails()) {
                    return sendError('upload image only (jpeg,jpg,png,gif)(10MB)', null);
                }

                $destinationpath = public_path("post/" . $request->media);
                File::delete($destinationpath);
                $file = $request->file('media');
                $filename = str_replace(' ', '', $file->getClientOriginalName());
                $ext = $file->getClientOriginalExtension();
                $imgname = uniqid() . $filename;
                $destinationpath = public_path('post');
                $file->move($destinationpath, $imgname);
            }

            $media = new FightMedia();
            $media->media = asset('post') . '/' . $imgname;
            $media->type = $request->type;
            if ($media->save()) {
                $data['media'] = FightMedia::find($media->id);
                return sendSuccess('Success', $data);
            }
        } elseif ($request->type == "video") {

            if ($request->hasfile('media')) {
                $postData = $request->only('media');

                $file = $postData['media'];

                $fileArray = array('media' => $file);

                // Tell the validator that this file should be an image
                $rules = array(//                '
                    'media' => 'mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts|max:100040|required'
                );

                // Now pass the input and rules into the validator
                $validator = Validator::make($fileArray, $rules);


                // Check to see if validation fails or passes
                if ($validator->fails()) {
                    return sendError('upload video only (mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts)(100MB)', null);
                }
                $file = $request->file('media');
                $filename = str_replace(' ', '', $file->getClientOriginalName());
                $ext = $file->getClientOriginalExtension();
                $video = uniqid() . $filename;
                $destinationpath = public_path('post/');
                $file->move($destinationpath, $video);
            }


            $path = $destinationpath . $video;
            $name = Str::random(15) . '.jpg';
            $thumbnail = $destinationpath . $name;
            $ffmpeg = FFMpeg::create();
            $video1 = $ffmpeg->open($path);

            $video1
                ->frame(TimeCode::fromSeconds(1))
                ->save($thumbnail);


            $dataa['poster'] = asset('post') . '/' . $name;

            $media = new FightMedia();
            $media->media = asset('post') . '/' . $video;
            $media->type = $request->type;
            $media->thumbnail = $dataa['poster'];
            if ($media->save()) {
                $data['media'] = FightMedia::find($media->id);
                return sendSuccess('Success', $data);
            }


        } else {
            return sendError('Type should be video or image.', null);

        }
        return sendError('There is some problem.', null);


    }

    public function addPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required'
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }
        $plan = Plan::find(Auth::user()->subscription_plan->stripe_plan);
//        dd($plan);

        if ($request->type == 'highlights') {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required'
            ]);

            if ($validator->fails()) {

                return sendError($validator->errors()->all()[0], null);

            }
            $highlight = new Highlight();
            $highlight->user_id = Auth::id();
            $highlight->title = $request->title;
            $highlight->description = $request->description;

            if ($highlight->save()) {
                if ($request->has('ids')) {
                    foreach ($request->ids as $id) {
                        FightMedia::where('id', $id)->update(['highlights_id' => $highlight->id]);
                    }
                }
                $per_page = isset($request->per_page) ? $request->per_page : 20;

                $data['highlights'] = Highlight::with('media', 'user')->paginate($per_page)->toArray();
                return sendSuccess("Highlights Posted Successfully!", $data);

            }
        }
        if ($request->type == 'sparrings') {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'location' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'event_id' => 'required',
                'budget_per_week' => 'required|integer',
                'no_of_weeks' => 'required',
                'weekly_data' => 'required',
                'gender' => 'required',
                'session_per_week' => 'required|integer'
            ]);


            if ($validator->fails()) {

                return sendError($validator->errors()->all()[0], null);

            }
            $datetime1 = strtotime($request->start_date);

            $datetime2 = strtotime($request->end_date);

            $week = $request->no_of_weeks * 7;
            $days = (int)(($datetime2 - $datetime1)/86400);
//            if ($days != $week){
//                return sendError("Your dates doesn't matched with your number of weeks",null);
//            }

//
//            if (!Auth::user()->stripe_payout_account_id or !Auth::user()->defaultCard) {
//                return sendError('Connect to stripe before posting sparring..!', null);
//
//            }


            if ($plan->no_of_sparrings != -1) {
                if (Auth::user()->plan_counter->no_of_sparrings < $plan->no_of_sparrings) {
                    $sparring = new Sparring();
                    $sparring->user_id = Auth::id();
                    $sparring->title = isset($request->title) ? $request->title : "";
                    $sparring->location = $request->location;
                    $sparring->start_date = $request->start_date;
                    $sparring->end_date = $request->end_date;
                    $sparring->budget_per_week = $request->budget_per_week;
                    $sparring->no_of_weeks = $request->no_of_weeks;
                    $sparring->event_id = $request->event_id;
                    $sparring->description = $request->description;
                    $sparring->gender = $request->gender;
                    $sparring->session_per_week = $request->session_per_week;
                    $sparring->status = 8;
                    if ($sparring->save()) {

                        // add weekly_data here
                        if($request->weekly_data){
                            $count = 0;
                            foreach ($request->weekly_data as $key1=>$one_week_data) {

//                                foreach ($one_week_data as $key => $w_data) {
//                                    dd($one_week_data,$w_data);
                                    $data_c['week'] = $one_week_data["week"];
                                    $data_c['day'] = $one_week_data["day"];
                                    $data_c['date'] = $one_week_data["date"];
                                    $data_c['user_id'] = Auth::id();
                                    $data_c['status'] = 8;
                                    $data_c['sparring_id'] = $sparring->id;
                                    CheckIn::create($data_c);
//                                }
                                $count++;
                            }
                        }


                        if ($request->has('facilities')) {
                            foreach ($request->facilities as $f) {
                                FightFacility::create(['sparring_id' => $sparring->id, 'facility_id' => $f]);
                            }
                        }
                        if ($request->has('ids')) {
                            foreach ($request->ids as $id) {
                                FightMedia::where('id', $id)->update(['sparring_id' => $sparring->id]);
                            }
                        }
                        $per_page = isset($request->per_page) ? $request->per_page : 20;

                        $data['sparrings'] = Sparring::with('event_id:id,title', 'media', 'status:id,name', 'assigned_to:id,first_name,last_name', 'user', 'user.stance_id:id,title', 'facilities', 'facilities.facility_id:id,name')->orderBy('id', 'desc')->paginate($per_page)->toArray();
                        return sendSuccess("Sparring Posted Successfully!", $data);

                    }
                } else {
                    return sendError("Your quota of posting sparring for this month is ended, renew or update your package to continue..!", null);

                }


            } else {
                $sparring = new Sparring();
                $sparring->user_id = Auth::id();
                $sparring->title = isset($request->title) ? $request->title : "";
                $sparring->location = $request->location;
                $sparring->start_date = $request->start_date;
                $sparring->end_date = $request->end_date;
                $sparring->budget_per_week = $request->budget_per_week;
                $sparring->no_of_weeks = $request->no_of_weeks;
                $sparring->event_id = $request->event_id;
                $sparring->description = $request->description;
                $sparring->gender = $request->gender;
                $sparring->session_per_week = $request->session_per_week;
                $sparring->status = 8;
                if ($sparring->save()) {
                    if ($request->has('facilities')) {
                        foreach ($request->facilities as $f) {
                            FightFacility::create(['sparring_id' => $sparring->id, 'facility_id' => $f]);
                        }
                    }
                    if ($request->has('ids')) {
                        foreach ($request->ids as $id) {
                            FightMedia::where('id', $id)->update(['sparring_id' => $sparring->id]);
                        }
                    }
                    $per_page = isset($request->per_page) ? $request->per_page : 20;

                    $data['sparrings'] = Sparring::with('event_id:id,title', 'media', 'status:id,name', 'assigned_to:id,first_name,last_name', 'user', 'user.stance_id:id,title', 'facilities', 'facilities.facility_id:id,name')->orderBy('id', 'desc')->paginate($per_page)->toArray();
                    return sendSuccess("Sparring Posted Successfully!", $data);

                }
            }
        }

        if ($request->type == 'fights') {

            $validator = Validator::make($request->all(), [
                'challenger' => 'required',
                'defender' => 'required',
                'posted_by' => 'required',
                'event_host' => 'required',
                'match_date' => 'required|date',
                'event_id' => 'required',
                'fund' => 'required',
                'no_of_rounds' => 'required',
//                'sports' => 'required',
                'description' => 'required',
                'location' => 'required',
//                'gender' => 'required',

            ]);

            $check = User::find($request->challenger);
            $check1 = User::find($request->defender);
            $check2 = User::find($request->posted_by);
            if (!$check){
                return sendError('Challenger not found',null);
            }else if (!$check1){
                return sendError('Defender not found',null);
            }else if (!$check2){
                return sendError('Matchmaker not found',null);
            }

            if ($validator->fails()) {
                return sendError($validator->errors()->all()[0], null);
            }
//            dd(Auth::user()->subscription_plan->plan->no_of_sparrings);
            if (Auth::user()->subscription_plan->plan->no_of_challenges != -1) {
                if (Auth::user()->plan_counter->no_of_challenges < Auth::user()->subscription_plan->plan->no_of_challenges) {

                    $fights = new Fight();
                    $fights->challenger = $request->challenger;
                    $fights->defender = $request->defender;
                    $fights->posted_by = $request->posted_by;
                    $fights->backup = $request->backup;
                    $fights->event_id = $request->event_id;
                    $fights->event_host = $request->event_host;
                    $fights->location = $request->location;
                    $fights->match_date = $request->match_date;
                    $fights->fund = $request->fund;
//            $fights->gender = $request->gender;
                    $fights->no_of_rounds = $request->no_of_rounds;
                    $fights->sports = isset($request->sports) ? $request->sports : "";
                    $fights->description = $request->description;
                    if (Auth::id() == $request->challenger) {
                        $fights->fighter_one_accepted = true;
                        $fights->status = 2;
                    } else {
                        $fights->status = 1;

                    }
                    $fights->posted_by = Auth::id();
                    if ($fights->save()) {
                        if ($request->has('facilities')) {
                            foreach ($request->facilities as $f) {
                                FightFacility::create(['fight_id'=>$fights->id,'facility_id'=>$f]);
                            }
                        }

                        $count = UserPlanCounter::where('user_id', Auth::id())->first();
                        $count->no_of_challenges = (int)$count->no_of_challenges + 1;
                        $count->save();
                        if ($request->has('ids')) {
                            foreach ($request->ids as $id) {
                                FightMedia::where('id', $id)->update(['fight_id' => $fights->id]);
                            }
                        }
                    }
                    $followings = Follower::where('follower_id', Auth::id())->pluck('following');
                    $per_page = isset($request->per_page) ? $request->per_page : 20;

                    $data['fights'] = Fight::with('defender','backup', 'status:id,name', 'event_id:id,title', 'challenger', 'posted_by', 'media', 'status:id,name', 'winner', 'defender.stance_id:id,title', 'challenger.stance_id:id,title','backup.stance_id:id,title', 'posted_by.stance_id:id,title', 'winner.stance_id:id,title','facilities','facilities.facility_id:id,name')
                        ->whereIn('defender', $followings)
                        ->orWhereIn('challenger', $followings)
                        ->orWhereIn('backup', $followings)
                        ->orWhereIn('posted_by', $followings)
                        ->orWhere('defender', Auth::id())
                        ->orWhere('challenger', Auth::id())
                        ->orWhere('posted_by', Auth::id())
                        ->orWhere('backup', Auth::id())
                        ->orderBy('id', 'desc')
                        ->paginate($per_page)
                        ->toArray();
                    return sendSuccess("Fight Posted Successfully!", $data);
                } else {
                    return sendError("Your quota of challenges for this month is ended, renew or update your package to continue..!", null);

                }
            } else {
                $fights = new Fight();
                $fights->challenger = $request->challenger;
                $fights->defender = $request->defender;
                $fights->posted_by = $request->posted_by;
                $fights->event_id = $request->event_id;
                $fights->event_host = $request->event_host;
                $fights->location = $request->location;
                $fights->match_date = $request->match_date;
                $fights->fund = $request->fund;
//            $fights->gender = $request->gender;
                $fights->no_of_rounds = $request->no_of_rounds;
                $fights->sports = isset($request->sports) ? $request->sports : "";
                $fights->description = $request->description;
                if (Auth::id() == $request->challenger) {
                    $fights->fighter_one_accepted = true;
                    $fights->status = 2;
                } else {
                    $fights->status = 1;

                }
                $fights->posted_by = Auth::id();
                if ($fights->save()) {
                    if ($request->has('facilities')) {
                        foreach ($request->facilities as $f) {
                            FightFacility::create(['fight_id'=>$fights->id,'facility_id'=>$f]);
                        }
                    }
                    if ($request->has('ids')) {
                        foreach ($request->ids as $id) {
                            FightMedia::where('id', $id)->update(['fight_id' => $fights->id]);
                        }
                    }
                }
                $followings = Follower::where('follower_id', Auth::id())->pluck('following');
                $per_page = isset($request->per_page) ? $request->per_page : 20;

                $data['fights'] = Fight::with('defender', 'status:id,name', 'event_id:id,title', 'challenger', 'posted_by', 'media', 'status:id,name', 'winner', 'defender.stance_id:id,title', 'challenger.stance_id:id,title', 'posted_by.stance_id:id,title', 'winner.stance_id:id,title','facilities','facilities.facility_id:id,name')
                    ->whereIn('defender', $followings)
                    ->orWhereIn('challenger', $followings)
                    ->orWhereIn('posted_by', $followings)
                    ->orWhere('defender', Auth::id())
                    ->orWhere('challenger', Auth::id())
                    ->orWhere('posted_by', Auth::id())
                    ->orderBy('id', 'desc')
                    ->paginate($per_page)
                    ->toArray();
                return sendSuccess("Fight Posted Successfully!", $data);
            }


        }
        return sendError("Something went wrong..!", null);

    }

    public function following(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required'
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }
        if ($request->type == "myFollowers") {
            $per_page = isset($request->per_page) ? $request->per_page : 20;

            $followers = Follower::withCount('follower')->with('follower', 'follower.tags', 'follower.tags.tag_id', 'follower.stance_id')->where('following', Auth::id())->orderBy('id', 'desc')->paginate($per_page);
            return sendSuccess("My Followers!", $followers);

        } elseif ($request->type == "myFollowing") {
            $per_page = isset($request->per_page) ? $request->per_page : 20;

            $followers = Follower::withCount('following')->with('following', 'following.tags', 'following.tags.tag_id', 'following.stance_id')->where('follower_id', Auth::id())->orderBy('id', 'desc')->paginate($per_page);
            return sendSuccess("My Following!", $followers);

        } elseif ($request->type == "followers") {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required'
            ]);

            if ($validator->fails()) {

                return sendError($validator->errors()->all()[0], null);

            }
            $per_page = isset($request->per_page) ? $request->per_page : 20;

            $followers = Follower::withCount('follower')->with('follower', 'follower.tags', 'follower.tags.tag_id', 'follower.stance_id')->where('following', $request->user_id)->orderBy('id', 'desc')->paginate($per_page);
            return sendSuccess("Followers!", $followers);
        } elseif ($request->type == "followings") {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required'
            ]);

            if ($validator->fails()) {

                return sendError($validator->errors()->all()[0], null);

            }
            $per_page = isset($request->per_page) ? $request->per_page : 20;

            $followers = Follower::withCount('following')->with('following', 'following.tags', 'following.tags.tag_id', 'following.stance_id')->where('follower_id', $request->user_id)->orderBy('id', 'desc')->paginate($per_page);
            return sendSuccess("Followings!", $followers);
        } else {
            return sendError('Type Undefined!', null);
        }

    }

    public function follow(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'following_id' => 'required',
            'type' => 'required'
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }
        $check = User::find($request->following_id);
        if (!$check) {
            return sendError('User not found!', null);
        }
        if ($request->type == 'follow') {
            $check2 = Follower::where(['follower_id' => Auth::id(), 'following' => $request->following_id])->first();
            if ($check2) {
                return sendError('You are already following this user!', null);

            }

            $follow = Follower::create(['follower_id' => Auth::id(), 'following' => $request->following_id]);
            $sender = Auth::user();
            $receiver_id = $request->following_id;
            $notification = new Notification();
            $notification->sender_name = $sender->first_name . ' ' . $sender->last_name;
            $notification->user_id = $sender->id;
            $notification->on_user = $receiver_id;
            $notification->type = 'message';
            $notification->notification_text = $sender->first_name . ' ' . $sender->last_name . ' has started following you.';
            $notification->profile_photo = $sender->profile_image;
            $notification->save();
            $data['notification'] = $notification;
            $noti_con = new NotificationController;
            $noti_con->sendPushNotification([$receiver_id], $notification->notification_text, $notification);
            if ($follow) {
                return sendSuccess('You have started following ' . $check->first_name . ' ' . $check->last_name, null);
            }
        } elseif ($request->type == "unfollow") {
            $check2 = Follower::where(['follower_id' => Auth::id(), 'following' => $request->following_id])->first();
            if (!$check2) {
                return sendError('You are not following this user!', null);

            }

            $follow = Follower::where(['follower_id' => Auth::id(), 'following' => $request->following_id])->delete();
            return sendSuccess('You have un-followed ' . $check->first_name . ' ' . $check->last_name, null);

        } else {
            return sendError('Undefined Type', null);
        }
    }


    public function profile_detail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check = User::find($request->user_id);

        if (!$check) {
            return sendError('No user found!', null);
        }

        $followers = Follower::where('following', $check->id)->count();
        $following = Follower::where('follower_id', $check->id)->count();
        $is_following = Follower::where(['follower_id' => Auth::id(), 'following' => $check->id])->first();
        if ($is_following) {
            $is_follow = true;
        } else {
            $is_follow = false;

        }
        $group = Group::with('members', 'members.member_id')->where('made_by', $check->id)->first();
        $member = GroupMember::where(['member_id' => Auth::id(), 'group_id' => isset($group->id) ? $group->id : 0])->first();
        if ($member) {
            $is_member = true;
        } else {
            $is_member = false;
        }
        $fighters = GroupMember::where(['group_id' => isset($group->id) ? $group->id : 0])->count();
        $sparring = Sparring::where(['user_id' => $check->id, 'status' => 8])->first();
        $offers = Offer::where('sparring_id', isset($sparring->id) ? $sparring->id : 0)->count();

        $myGroup = Group::where('made_by', Auth::id())->first();
        $myMember = GroupMember::where(['member_id' => $check->id, 'group_id' => isset($myGroup->id) ? $myGroup->id : 0])->first();

        if ($myMember) {
            $my_member = true;
        } else {
            $my_member = false;
        }

        $payout1 = Fight::where(['posted_by' => $check->id, 'winner' => $check->id])->sum('fund');
        $payout1 = $payout1 / 2;
        $payout2 = Fight::where(['posted_by' => $check->id])->where('winner', '!=', $check->id)->sum('fund');
        $payout3 = DB::table('sparrings')
            ->selectRaw('SUM(budget_per_week * no_of_weeks) as total')
            ->where(['user_id' => $check->id, 'status' => 7])
            ->pluck('total');
        $payout4 = 0;
        foreach ($payout3 as $p) {
            $payout4 += (int)$p;
        }
        $payout = (int)$payout1 + (int)$payout2 + (int)$payout4;

        $wins = Fight::where(['winner' => $check->id])->count();
        $ko = Fight::where(['winner' => $check->id, 'is_ko' => 1])->count();
        $loses = Fight::where('challenger', $check->id)->orWhere('defender', $check->id)->where('winner', '!=', $check->id)->where('winner', '!= ', null)->count();

        $user = User::withCount('followers', 'followings')->with('tags.tag_id:id,name', 'tags.sub_tags.sub_tag:id,name', 'notification_setting', 'docs', 'stance_id:id,title')->with(['tags.sub_tags' => function ($a) use ($check) {
            $a->where('user_id', $check->id);
        }])->where('id', $check->id)->first()->toArray();
        $result['result'] = array_merge($user, [
            'followers' => $followers,
            'followings' => $following,
            'is_follow' => $is_follow,
            'group' => $group,
            'is_member' => $is_member,
            'my_member' => $my_member,
            'fighters' => $fighters,
            'offers' => $offers,
            'payout' => $payout,
            'wins' => $wins,
            'ko' => $ko,
            'loses' => $loses,
        ]);

        return sendSuccess('User profile', $result);
    }

    public function profile_data(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'type' => 'required'
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check = User::find($request->user_id);

        if (!$check) {
            return sendError('No user found!', null);
        }

        if ($request->type == 'highlights') {
            $per_page = isset($request->per_page) ? $request->per_page : 20;
            $data['highlights'] = Highlight::where('user_id', $check->id)->withCount('likes')->with('media', 'user', 'user.stance_id')->paginate($per_page)->toArray();
            return sendSuccess("All highlights!", $data);

        } elseif ($request->type == 'reviews') {
            $per_page = isset($request->per_page) ? $request->per_page : 20;
            $data['reviews'] = Review::with('given_by', 'given_by.stance_id', 'sparring_id', 'sparring_id.event_id', 'fight_id', 'fight_id.event_id')->where('given_to', $check->id)->paginate($per_page)->toArray();
            return sendSuccess("All reviews!", $data);
        } elseif ($request->type == 'about') {
            $per_page = isset($request->per_page) ? $request->per_page : 20;
            $data['about'] = User::withCount('followers', 'followings')->with('tags.tag_id:id,name', 'tags.sub_tags.sub_tag:id,name', 'notification_setting', 'docs', 'stance_id:id,title')->with(['tags.sub_tags' => function ($a) use ($request) {
                $a->where('user_id', $request->user_id);
            }])->find($request->user_id);
            return sendSuccess("All reviews!", $data);
        } else {
            return sendError('Undefined Type!', null);
        }

        return sendError('Something went wrong!', null);
    }


    public function edit_highlight(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check = Highlight::find($request->id);

        if (!$check) {
            return sendError('No highlight found!', null);
        }


        $title = isset($request->title) ? $request->title : $check->title;
        $description = isset($request->description) ? $request->description : $check->description;

        $highlight = Highlight::find($check->id);
        $highlight->title = $title;
        $highlight->description = $description;
        if ($highlight->save()) {
            if ($request->has('ids')) {
                FightMedia::where('highlights_id', $check->id)->delete();
                foreach ($request->ids as $id) {
                    FightMedia::where('id', $id)->update(['highlights_id' => $highlight->id]);
                }
            }

            $highlights = Highlight::withCount('likes')->with('media', 'user', 'user.stance_id:id,title')->get();
            foreach ($highlights as $s) {
                $check2 = Like::where(['highlight_id' => $s->id, 'liked_by' => Auth::id()])->first();
                if (!$check2) {
                    $s->is_liked = false;

                } else {
                    $s->is_liked = true;


                }
            }

            $per_page = isset($request->per_page) ? $request->per_page : 20;

            $data['highlights'] = (new Collection($highlights))->paginate($per_page);
            return sendSuccess("Highlight Updated Successfully!", $data);


        }
    }

    public function delete_highlight(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check = Highlight::find($request->id);

        if (!$check) {
            return sendError('No highlight found!', null);
        }

        $highlight = Highlight::where('id', $request->id)->delete();
        if ($highlight) {
            FightMedia::where('highlights_id', $request->id)->delete();
        }


        return sendSuccess("Highlight deleted Successfully!", null);


    }
    public function my_highlights(Request $request)
    {
        $per_page = isset($request->per_page) ? $request->per_page : 20;

        $highlights = Highlight::withCount('likes')->with('media', 'user', 'user.stance_id:id,title')->where('user_id',Auth::id())->get();
        foreach ($highlights as $s) {
            $check2 = Like::where(['highlight_id' => $s->id, 'liked_by' => Auth::id()])->first();
            if (!$check2) {
                $s->is_liked = false;

            } else {
                $s->is_liked = true;


            }
        }

        $per_page = isset($request->per_page) ? $request->per_page : 20;

        $data['highlights'] = (new Collection($highlights))->paginate($per_page);
        return sendSuccess("All Highlights!", $data);

    }

}



