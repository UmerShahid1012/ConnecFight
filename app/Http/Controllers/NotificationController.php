<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function sendPushNotification($ids, $text, $data){
        $filters = [];

        //array_push($filters, ["field" => "tag", "key" => "user_id", "relation" => "=", "value" => "$user_id"]);
        foreach ($ids as $i => $id){
            if($i > 0)
                array_push($filters, ["operator" => "OR"]);
            array_push($filters, ["field" => "tag", "key" => "user_id", "relation" => "=", "value" => "$id"]);
        }

        Log::info($filters);

        $content = array(
            "en" => $text
        );

        $fields = array(
            'app_id' => config('onesignal.app_id'),
            'included_segments' => array('Active Users'),
            'filters' => $filters,
            'data' => $data,
            'contents' => $content
        );

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic  '.config('onesignal.rest_api_key')));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);
        //print("\nJSON sent:\n");
        //print($fields);
        //echo true;
        Log::info('NotificationController => function sendPushNotification');
        Log::info($response);

        return $response;
    }

    public function getNotifications(){
        Notification::where('on_user',Auth::id())->where('is_read',0)->update(['is_read'=> 1]);
        $data['notifications'] = Notification::where('on_user',Auth::id())->orderBy('created_at','desc')->get();
        return sendSuccess('Got notifications',$data);
    }
}
