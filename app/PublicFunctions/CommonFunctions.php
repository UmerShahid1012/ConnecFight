<?php

use Illuminate\Support\Facades\Response;

function sendSuccess($message, $data) {
    return Response::json(['status' => true, 'message' => $message, 'data' => $data], 200);
}

function sendError($message, $data) {
    return Response::json(['status' => false, 'message' => $message, 'data' => $data], 200);
}

function getAdminMeta($key){
    return \App\AdminMeta::where('key', $key)->first();
}

function getAdminMetaCalculate($key, $amount, $is_increment){
    $am = \App\AdminMeta::where('key', $key)->first();
    if($am != null && $am->type == 'percentage'){
        return ($is_increment) ? (($am->value/100)*$amount) + $amount : $amount -(($am->value/100)*$amount) ;
    }
    return ($is_increment) ? ($amount + $am->value ) : ($amount - $am->value );
}


function getUser(){
    return \App\User::withCount('posts', 'bookings', 'reviews')->with('profession', 'services', 'gallery', 'availability', 'defaultCard')->selectRaw('users.*, round(
                        ((select count(id)*1 from reviews where profile_id = users.id and rating = 1)+
                         (select count(id)*2 from reviews where profile_id = users.id and rating = 2)+
                         (select count(id)*3 from reviews where profile_id = users.id and rating = 3)+
                         (select count(id)*4 from reviews where profile_id = users.id and rating = 4)+
                         (select count(id)*5 from reviews where profile_id = users.id and rating = 5))/
                        (select count(id) from reviews where profile_id = users.id)) as rating,
                        case when exists (SELECT * FROM `favorites` where user_id = '.\Illuminate\Support\Facades\Auth::id().' and profile_id = users.id) then 1 else 0 end as is_favorite');
}

function getBlackout($user_id){
    return \App\BlackoutDate::where('user_id', $user_id)->selectRaw('group_id AS id, user_id, title, MIN(blackout_date) as start, MAX(blackout_date) as end, is_repeat, created_at, updated_at')->groupBy('group_id');
}

function getService(){
    return \App\Service::with(['user', 'gallery'=>function($query){
        $query->selectRaw('post_media.id, post_media.path, post_media.type, post_media.ratio, post_media.thumbnail');
    }]);
}

function getEvent(){
    return \App\Event::with(['bookings'=>function($query){
        $query->with('user', 'service.user', 'dates');
    }]);
}

function filterBookingTime($service_id, $date){
    $service = \App\Service::where('id', $service_id)->first();
    if($service) {
        $user = \App\User::where('id', $service->user_id)->first();
        $data['date'] = $date;

        //if($user->start_hour != null && $user->end_hour != null  && $user->week_days != null ){
        if ($user->is_professional) {
            $blackout = \App\BlackoutDate::where(function ($q) use ($date, $user) {
                $q->where('user_id', $user->id)
                    ->where('is_repeat', false)
                    ->whereDate('blackout_date', $date);
            })->orWhere(function ($q) use ($date, $user) {
                $q->where('user_id', $user->id)
                    ->where('is_repeat', true)
                    ->whereDay('blackout_date', \Carbon\Carbon::create($date)->format('d'));
            })->first();
            if ($blackout) {
                $data['status'] = 3;
                $data['message'] = $blackout->title;

            } else {
                $dayOfWeek = getdate(strtotime($date))['wday'];
                //$week_days = $user->week_days;
                //if(strpos($week_days, "$dayOfWeek") !== false){
                $week = \App\Availability::where('user_id', $user->id)->where('week_day', $dayOfWeek)->first();
                if ($week) {
                    $times = [];
                    for($i = $week->start_hour; $i <= $week->end_hour; $i++) {
                        $booking = \App\Booking::where('service_id', $service_id)
                            ->where('status', '=', 'accepted')
                            ->whereHas('dates', function (\Illuminate\Database\Eloquent\Builder $query) use ($date, $i) {
                                $query->whereDate('booking_date', $date)
                                    ->where('booking_hour', $i);
                            })->first();
                        array_push($times, ['time'=>$i.':00', 'is_booked'=> (!$booking) ? false : true]);
                    }
                    $data['status'] = 1;
                    $data['message'] = 'On day';
                    $data['calender_time'] = $times;
                    /*if(count($times) == 0){
                      $data['booking'] = isset($booking) ? $booking : null;
                    }*/

                } else {
                    $data['status'] = 2;
                    $data['message'] = 'Off day';
                    $data['calender_time'] = [];
                }
            }
        } else {
            $data['status'] = 0;
            $data['message'] = 'User is not professional.';
            $data['calender_time'] = [];
        }
    }else{
        $data['status'] = -1;
        $data['message'] = 'Service not found.';
        $data['calender_time'] = [];
    }
    return $data;
}

function getVideoInformation($video_information) {
    $regex_duration = "/Duration: ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}).([0-9]{1,2})/";
    if (preg_match($regex_duration, implode(" ", $video_information), $regs)) {
        $hours = $regs [1] ? $regs [1] : null;
        $mins = $regs [2] ? $regs [2] : null;
        $secs = $regs [3] ? $regs [3] : null;
        $ms = $regs [4] ? $regs [4] : null;
        $random_duration = sprintf("%02d:%02d:%02d", rand(0, $hours), rand(0, $mins), rand(0, $secs));
        $original_duration = $hours . ":" . $mins . ":" . $secs;
        $parsed = date_parse($original_duration);
        $seconds = ($parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second']) > 20 ? true : false;
        return [$original_duration, $random_duration, $seconds];
    }
}

function subscribed_user($stripe_id) {
    $user = \App\Subscription::where('stripe_id', $stripe_id)->with('getUser')->first();
    if ($user) {
        return $user->getUser->name;
    } else {
        return false;
    }
}
function subscribed_mile_coverd($stripe_id) {
    $miles = \App\Subscription::where('stripe_id', $stripe_id)->first();
    if ($miles) {
        return $miles;
    } else {
        return false;
    }
}

function get_user_charge($user_id) {
    try {
        $charge = \App\Payment::where('user_id', $user_id)->first();

        return $charge;
    } catch (Stripe_CardError $e) {
        $e->getMessage();
        return false;
    } catch (Stripe_InvalidRequestError $e) {
        $e->getMessage();
        return false;
    } catch (Stripe_AuthenticationError $e) {
        $e->getMessage();
        return false;
    } catch (Stripe_ApiConnectionError $e) {
        $e->getMessage();
        return false;
    } catch (Stripe_Error $e) {
        $e->getMessage();
        return false;
    } catch (Exception $e) {
        $e->getMessage();
        return false;
    }
}

function get_subscription($user_id) {
    $subccription = \App\Subscription::where('user_id', $user_id)->first();
    if ($subccription) {
        try {
            \Stripe\Stripe::setApiKey('sk_test_5U2DWCQfgd0issmQbyH3MSOi');

            $signle_plan = \Stripe\Subscription::retrieve($subccription->stripe_id);
            return $signle_plan;
        } catch (Stripe_CardError $e) {
            $e->getMessage();
            return false;
        } catch (Stripe_InvalidRequestError $e) {
            $e->getMessage();
            return false;
        } catch (Stripe_AuthenticationError $e) {
            $e->getMessage();
            return false;
        } catch (Stripe_ApiConnectionError $e) {
            $e->getMessage();
            return false;
        } catch (Stripe_Error $e) {
            $e->getMessage();
            return false;
        } catch (Exception $e) {
            $e->getMessage();
            return false;
        }
    } else {
        return false;
    }
}
