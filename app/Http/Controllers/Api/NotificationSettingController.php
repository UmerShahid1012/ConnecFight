<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Fight;
use App\Models\Highlight;
use App\Models\Like;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\Sparring;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'follow' => 'required|boolean',
            'post' => 'required|boolean',
            'fight' => 'required|boolean',
            'message' => 'required|boolean',
            'daily_updates' => 'required|boolean',

        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $setting = NotificationSetting::where('user_id',Auth::id())->update([
            'follow'=>$request->follow,
            'post'=>$request->post,
            'fight'=>$request->fight,
            'message'=>$request->message,
            'daily_updates'=>$request->daily_updates
        ]);
        $user = Auth::user();
        $data['result'] =  $response1 = User::withCount('followers','followings')->with('tags','tags.tag_id','tags.sub_tags.sub_tag_id','notification_setting','docs','stance_id:id,title')->with(['tags.sub_tags' => function($a) use($user) {
            $a->where('user_id', $user->id);
        }])->where('id',$user->id)->first();

        return sendSuccess('Notification setting updated successfully!',$data);
    }

    public function like_unlike(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'like' => 'required|boolean',
            'type' => 'required',
            'id' => 'required|integer',

        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }
        $sender = Auth::user();

        if($request->type == "fight"){


            $check = Fight::find($request->id);
            if (!$check){
                return sendError("No fight found!", null);
            }
            if ($request->like == true){
                $check2 = Like::where(['liked_by'=>Auth::id(),'fight_id'=>$request->id])->first();
                if ($check2){
                    return sendError("You already liked this fight!", null);
                }
                Like::create(['liked_by'=>Auth::id(),'fight_id'=>$request->id]);
                $defender = User::find($check->defender);
                $challenger = User::find($check->challenger);
                $posted_by = User::find($check->posted_by);
                if ($defender->notification_setting['post'] == 1) {
                    $receiver_id = $check->defender;
                    $notification = new Notification();
                    $notification->sender_name = $sender->first_name . ' ' . $sender->last_name;
                    $notification->user_id = $sender->id;
                    $notification->on_user = $check->defender;
                    $notification->type = 'post';
                    $notification->notification_text = $sender->first_name . ' ' . $sender->last_name . ' has liked your fight.';
                    $notification->profile_photo = $sender->profile_image;
                    $notification->save();
                    $data['notification'] = $notification;
                    $noti_con = new NotificationController;
                    $noti_con->sendPushNotification([$receiver_id], $notification->notification_text, $notification);

                }
                if ($challenger->notification_setting['post'] == 1) {
                    $receiver_id = $check->challenger;
                    $notification = new Notification();
                    $notification->sender_name = $sender->first_name . ' ' . $sender->last_name;
                    $notification->user_id = $sender->id;
                    $notification->on_user = $check->challenger;
                    $notification->type = 'post';
                    $notification->notification_text = $sender->first_name . ' ' . $sender->last_name . ' has liked your fight.';
                    $notification->profile_photo = $sender->profile_image;
                    $notification->save();
                    $data['notification'] = $notification;
                    $noti_con = new NotificationController;
                    $noti_con->sendPushNotification([$receiver_id], $notification->notification_text, $notification);

                }
                if ($posted_by->notification_setting['post'] == 1) {
                    $receiver_id = $check->posted_by;
                    $notification = new Notification();
                    $notification->sender_name = $sender->first_name . ' ' . $sender->last_name;
                    $notification->user_id = $sender->id;
                    $notification->on_user = $check->posted_by;
                    $notification->type = 'post';
                    $notification->notification_text = $sender->first_name . ' ' . $sender->last_name . ' has liked your fight.';
                    $notification->profile_photo = $sender->profile_image;
                    $notification->save();
                    $data['notification'] = $notification;
                    $noti_con = new NotificationController;
                    $noti_con->sendPushNotification([$receiver_id], $notification->notification_text, $notification);

                }
                return sendSuccess('Liked Successfully!',null);

            }else{
                $check2 = Like::where(['liked_by'=>Auth::id(),'fight_id'=>$request->id])->forceDelete();
                return sendSuccess('Disliked Successfully!',null);
            }
        }elseif ($request->type == "highlight") {
            $check = Highlight::find($request->id);
            if (!$check) {
                return sendError("No highlights found!", null);
            }
            if ($request->like == true) {
                $check2 = Like::where(['liked_by' => Auth::id(), 'highlight_id' => $request->id])->first();
                if ($check2) {
                    return sendError("You already liked this highlights!", null);
                }
                Like::create(['liked_by' => Auth::id(), 'highlight_id' => $request->id]);
                if ($check->user->notification_setting['post'] == 1) {
                    $receiver_id = $check->posted_by;
                    $notification = new Notification();
                    $notification->sender_name = $sender->first_name . ' ' . $sender->last_name;
                    $notification->user_id = $sender->id;
                    $notification->on_user = $check->user_id;
                    $notification->type = 'post';
                    $notification->notification_text = $sender->first_name . ' ' . $sender->last_name . ' has liked your highlights.';
                    $notification->profile_photo = $sender->profile_image;
                    $notification->save();
                    $data['notification'] = $notification;
                    $noti_con = new NotificationController;
                    $noti_con->sendPushNotification([$receiver_id], $notification->notification_text, $notification);
                }
                return sendSuccess('Liked Successfully!',null);


            }else{
                $check2 = Like::where(['liked_by'=>Auth::id(),'highlight_id'=>$request->id])->forceDelete();
                return sendSuccess('Disliked Successfully!',null);
            }
        }elseif ($request->type == "sparring") {
            $check = Sparring::find($request->id);
            if (!$check) {
                return sendError("No sparring found!", null);
            }
            if ($request->like == true) {
                $check2 = Like::where(['liked_by' => Auth::id(), 'sparring_id' => $request->id])->first();
                if ($check2) {
                    return sendError("You already liked this sparring!", null);
                }
                Like::create(['liked_by' => Auth::id(), 'sparring_id' => $request->id]);
                if ($check->user->notification_setting['post'] == 1) {
                    $receiver_id = $check->posted_by;
                    $notification = new Notification();
                    $notification->sender_name = $sender->first_name . ' ' . $sender->last_name;
                    $notification->user_id = $sender->id;
                    $notification->on_user = $check->user_id;
                    $notification->type = 'post';
                    $notification->notification_text = $sender->first_name . ' ' . $sender->last_name . ' has liked your sparring.';
                    $notification->profile_photo = $sender->profile_image;
                    $notification->save();
                    $data['notification'] = $notification;
                    $noti_con = new NotificationController;
                    $noti_con->sendPushNotification([$receiver_id], $notification->notification_text, $notification);
                }
                return sendSuccess('liked Successfully!',null);


            }else{
                $check2 = Like::where(['liked_by'=>Auth::id(),'sparring_id'=>$request->id])->forceDelete();
                return sendSuccess('Disliked Successfully!',null);
            }
        }else{
            sendError('Undefined Error',null);
        }


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NotificationSetting  $notificationSetting
     * @return \Illuminate\Http\Response
     */
    public function show(NotificationSetting $notificationSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NotificationSetting  $notificationSetting
     * @return \Illuminate\Http\Response
     */
    public function edit(NotificationSetting $notificationSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NotificationSetting  $notificationSetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NotificationSetting $notificationSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NotificationSetting  $notificationSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(NotificationSetting $notificationSetting)
    {
        //
    }
}
