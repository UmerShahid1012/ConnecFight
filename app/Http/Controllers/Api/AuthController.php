<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Admin;
use App\Models\DisputeList;
use App\Models\Facility;
use App\Models\Faqs;
use App\Models\NotificationSetting;
use App\Models\Plan;
use App\Models\SubTag;
use App\Models\Tag;
use App\Models\UserImage;
use App\Models\UserOfficialDocuments;
use App\Models\UserPlanCounter;
use App\Models\UserTags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Subscription;
use Stripe\Stripe;


use Carbon\Carbon;

// Models
use App\Models\User;
use App\Models\PasswordReset;

// use App\NotificationsStatus;

// Resources
use App\Http\Resources\UserResource;
use Stripe\Account;

class AuthController extends BaseApiController
{
    function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|unique:users,email',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'password' => 'required|string|min:6',
                'confirm_password' => 'required|string|same:password',
                'tags' => 'required|array',
                'sub_tags' => 'required|array'
            ]);


        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $code = $this->get_random_code();

        $user = new User();
        $user->email = $request->email;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->password = bcrypt($request->password);
        $user->createAsStripeCustomer();


        if ($user->save()) {
            if ($request->tags) {
                foreach ($request->tags as $t) {
                    $tags = new UserTags();
                    $tags->tag_id = $t;
                    $tags->user_id = $user->id;
                    $tags->save();
                }
            }
            if ($request->sub_tags) {
                foreach ($request->sub_tags as $t) {
                    $parent = SubTag::where('id',$t)->first();
                    $tags = new UserTags();
                    $tags->tag_id = $parent->tag_id;
                    $tags->sub_tag_id = $t;
                    $tags->user_id = $user->id;
                    $tags->save();
                }
            }
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            $price = Plan::find(1);


            $subscription =  \Stripe\Subscription::create([
                "customer" => $user->stripe_id,
//                "default_source" => $donation->card->stripe_id,

                "items" => [
                    ["price" => $price->stripe_plan_id],
                ],
            ]);

            $month = Carbon::now()->addMonth()->month;
            $end_date = Carbon::create(Carbon::now()->year,$month , 5);
            Subscription::create([
                'user_id'=>$user->id,
                'name' => 'Free',
                'stripe_id' => $subscription->id,
                'stripe_status' => $subscription->status,
                'stripe_plan' => $price->id,
                'ends_at' =>$end_date,
                'status_id' =>13

            ]);
            UserPlanCounter::create(['user_id'=>$user->id]);

            $setting = NotificationSetting::create(['user_id'=>$user->id]);
            $response1 = User::withCount('followers','followings')->with('subscription_plan','tags','tags.tag_id','tags.sub_tags.sub_tag','notification_setting','docs','stance_id:id,title')->with(['tags.sub_tags' => function($a) use($user) {
                $a->where('user_id', $user->id);
            }])->where('id',$user->id)->first();
            $access_token = $tokenResult->accessToken;
            $response['token_type'] = 'Bearer';
            $response['result'] = array_merge($response1->toArray(), ['access_token' => $access_token]);

            return sendSuccess("User Registered Successfully!", $response);

        } else {

            return sendError("Something went wrong, Please try again", null);


        }
    }

    public function verify_account(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users,email',
            'code' => 'required|string|exists:users,verification_code'
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }


        $user = User::where('email', $request->email)->first();

        $user->is_active = 1;
        $user->verification_code = null;
        $user->email_verified_at = Carbon::now();


        if ($user->save()) {

            $response['result'] = User::where('id', $user->id)->first();

            return sendSuccess("Your account activate successfully!", null);

        } else {

            return sendError("Something went wrong, Please try again.", null);


        }
    }

    public function user_documents(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'federal_id' => 'required',
            'driving_license' => 'required',
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }
        if ($request->hasfile('federal_id')) {

            $postData = $request->only('federal_id');

            $file = $postData['federal_id'];

            $fileArray = array('federal_id' => $file);

            // Tell the validator that this file should be an image
            $rules = array(
                'federal_id' => 'mimes:jpeg,jpg,png,gif|required|max:10000' // max 10000kb
            );

            // Now pass the input and rules into the validator
            $validator = Validator::make($fileArray, $rules);


            // Check to see if validation fails or passes
            if ($validator->fails()) {
                return sendError('upload image only (jpeg,jpg,png,gif)(10MB)', null);
            }

            $destinationpath = public_path("users/documents/" . $request->federal_id);
            File::delete($destinationpath);
            $file = $request->file('federal_id');
            $filename = str_replace(' ', '', $file->getClientOriginalName());
            $ext = $file->getClientOriginalExtension();
            $imgname = uniqid() . $filename;
            $destinationpath = public_path('users/documents');
            $file->move($destinationpath, $imgname);
        }
        if ($request->hasfile('driving_license')) {

            $postData = $request->only('driving_license');

            $file = $postData['driving_license'];

            $fileArray = array('driving_license' => $file);

            // Tell the validator that this file should be an image
            $rules = array(
                'driving_license' => 'mimes:jpeg,jpg,png,gif|required|max:10000' // max 10000kb
            );

            // Now pass the input and rules into the validator
            $validator = Validator::make($fileArray, $rules);


            // Check to see if validation fails or passes
            if ($validator->fails()) {
                return sendError('upload image only1 (jpeg,jpg,png,gif)(10MB)', null);
            }

            $destinationpath = public_path("users/documents/" . $request->driving_license);
            File::delete($destinationpath);
            $file = $request->file('driving_license');
            $filename = str_replace(' ', '', $file->getClientOriginalName());
            $ext = $file->getClientOriginalExtension();
            $imgname1 = uniqid() . $filename;
            $destinationpath = public_path('users/documents');
            $file->move($destinationpath, $imgname1);
        }

        $media = new UserImage();
        $media->user_id = Auth::id();
        $media->federal_id = asset('users/documents') . '/' . $imgname;
        $media->driving_license = asset('users/documents') . '/' . $imgname1;
        if ($media->save()) {
            $data['result'] = UserImage::find($media->id);
            return sendSuccess('Success', $data);
        }
        return sendError('There is some problem.', null);


    }
    public function user_official_certificates(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'certificate' => 'required',
            'sub_tag_id' => 'required',
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check = UserTags::where(['sub_tag_id'=>$request->sub_tag_id,'user_id'=>Auth::id()])->first();
        if (!$check){
            return sendError("No sub tag found", null);

        }
        if ($request->hasfile('certificate')) {

            $postData = $request->only('certificate');

            $file = $postData['certificate'];

            $fileArray = array('certificate' => $file);

            // Tell the validator that this file should be an image
            $rules = array(
                'certificate' => 'mimes:jpeg,jpg,png,gif|required|max:10000' // max 10000kb
            );

            // Now pass the input and rules into the validator
            $validator = Validator::make($fileArray, $rules);


            // Check to see if validation fails or passes
            if ($validator->fails()) {
                return sendError('upload image only (jpeg,jpg,png,gif)(10MB)', null);
            }

            $destinationpath = public_path("users/documents/" . $request->federal_id);
            File::delete($destinationpath);
            $file = $request->file('certificate');
            $filename = str_replace(' ', '', $file->getClientOriginalName());
            $ext = $file->getClientOriginalExtension();
            $imgname = uniqid() . $filename;
            $destinationpath = public_path('users/documents');
            $file->move($destinationpath, $imgname);
        }

        $media = new UserOfficialDocuments();
        $media->user_id = Auth::id();
        $media->sub_tag_id = $request->sub_tag_id;
        $media->certificate = asset('users/documents') . '/' . $imgname;
        if ($media->save()) {
            $data['result'] = UserOfficialDocuments::with('sub_tag_id')->where('user_id',Auth::id())->get();
            return sendSuccess('Success', $data);
        }
        return sendError('There is some problem.', null);


    }
    public function getSubTags(Request $request)
    {
        $tags = UserTags::where('user_id',Auth::id())->whereNotNull('sub_tag_id')->pluck('sub_tag_id');
        $data['result'] = SubTag::whereIn('id',$tags)->get();
        return sendSuccess('User Sub Tags', $data);

    }
    public function all_user_official_certificates(Request $request)
    {
        $data['result'] = UserOfficialDocuments::with('sub_tag_id')->where('user_id',Auth::id())->get();
        return sendSuccess('User Official Certificates', $data);

    }
    public function delete_user_official_certificates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }
        $check = UserOfficialDocuments::find($request->id);
        if (!$check){
            return sendError('No Certificate Found',null);
        }
        $data['result'] = UserOfficialDocuments::where('id',$request->id)->delete();
        return sendSuccess('Success', null);

    }

    public function getStripeLink(Request $request)
    {
        $data['redirect_url'] = route('stripe.url');
        $link = "https://connect.stripe.com/express/oauth/authorize?redirect_uri=".route('stripe.url')."&client_id=".config('app.stripe_client_id')."&scope=read_write&state=".bcrypt('123456');

        $data['connect_url'] = $link;
        return sendSuccess('Stripe Connect Link.',$data);
    }

    public function stripeRedirectUriMobile(Request $request)
    {
//        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET','sk_test_51HMSlnB1RGqZiqJsX0oFR17piiD8BmBSQHGu9QIgmDvKhVesZma1QnDA0UjNFEZeLH8CeDUdVm1U1pjC9DelwkDZ00lHSC9FmJ'));
//        $code = $request->code;
//        $response = \Stripe\OAuth::token([
//            'grant_type' => 'authorization_code',
//            'code' => $code,
//        ]);
//
//        $account = Account::retrieve($response->stripe_user_id);
//        $url = $account->login_links->create();
//
//        $user = User::find($request->state);
//        $user->stripe_payout_account_id = $response->stripe_user_id;
//        $user->is_bank_account_verified = 1;
//        $user->save();


        $validator = Validator::make($request->all(), [
            'stripe_payout_account_id' => 'required',
            'stripe_express_dashboard_url' => 'required'
        ]);
        if ($validator->fails()) {
            return sendError('The given data is invalid', $validator->getMessageBag());
        }
        $user_id = ($request->user_id) ? $request->user_id : Auth::id();

        $user = User::find($user_id);
        if ($user) {
            $user->stripe_payout_account_id = $request->stripe_payout_account_id;
            $user->stripe_express_dashboard_url = $request->stripe_express_dashboard_url;
            $user->is_bank_account_verified = 1;
            $user->save();
                $response1 = User::withCount('followers','followings','group')->with('tags','tags.tag_id','tags.sub_tags.sub_tag','notification_setting','docs','stance_id:id,title')->with(['tags.sub_tags' => function($a) use($user) {
                    $a->where('user_id', (int)$user->id);
                }])->where('id',$user->id)->first();
                $user['result'] = $response1;
            return sendSuccess('Stripe Connected Successfully.',$user);

        }

    }


    public function login(Request $request)
    {

        $msg = '';



            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|exists:users,email',
                'password' => 'required|string|min:6',
            ]);
            $credentials = request(['email', 'password']);
            $msg = 'Invalid email or password';


        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);


        } else {

            if (!Auth::attempt($credentials)) {
                return sendError($msg, null);
            }


            $user = $request->user();
            $user->last_login_at = Carbon::now();
            $user->save();

//            if (!$user->is_active) {
//                return $this->error_response(['message' => "Please Verify Your Account first"]);
//            }
            // if(NotificationsStatus::where('user_id', $user->id)->doesntExist()){
            // 	$notifications_status = new NotificationsStatus();
            // 	$notifications_status->user_id = $user->id;
            // 	$notifications_status->save();
            // }






            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();
//            dd($user->with('tags'));
            $response1 = User::withCount('followers','followings','group')->with('subscription_plan','tags','tags.tag_id','tags.sub_tags.sub_tag','notification_setting','docs','stance_id:id,title')->with(['tags.sub_tags' => function($a) use($user) {
                        $a->where('user_id', (int)$user->id);
            }])->where('id',$user->id)->first();
//            $response1 = new User();
//            $response1->tags($user->id);
            $access_token = $tokenResult->accessToken;
            $response['token_type'] = 'Bearer';
            $response['result'] = array_merge($response1->toArray(), ['access_token' => $access_token]);

            return sendSuccess("User Successfully Logged in!", $response);
        }
    }
    public function getUser(Request $request)
    {

        $msg = '';



            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,id',
            ]);


        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);


        } else {

            $response1 = User::withCount('followers','followings','group')->with('subscription_plan','tags','tags.tag_id','tags.sub_tags.sub_tag','notification_setting','docs','stance_id:id,title')->with(['tags.sub_tags' => function($a) use($request) {
                        $a->where('user_id', (int)$request->id);
            }])->where('id',$request->id)->first();
            $response['result'] = $response1;

            return sendSuccess("User Data!", $response);
        }
    }

    public function socialLogin(Request $request){
        $validator = Validator::make($request->all(), [

            'social_id' => 'required',
            'social_type' => 'required'
        ]);

        if($validator->fails()){
            return sendError( $validator->getMessageBag(),null);
        }

        if($request->email){
            $user = User::where('email',  $request->email)->where('social_id', $request->social_id)->first();
        } else {
            $user = User::where('social_id', $request->social_id)->first();
        }

        if($request->email){
            $check1 = User::where('email',  $request->email)->first();
            if(!$user && $check1){
                return sendError('Email has been registered already with another account.', null);
            }
        }

        $check2 = User::where('social_id', $request->social_id)->first();
        if(!$user && $check2){
            return sendError('Account has been registered with another email.', null);
        }

        if(!$user){
            $user = new User;
            $user->email = $request->email;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->password = bcrypt(mt_rand(100000, 999999));
            $user->social_id = $request->social_id;
            $user->social_type = $request->social_type;
            $user->createAsStripeCustomer();


            if(!$user->save()){
                return sendError('There is some problem.', null);
            }
        }

        if($user){
            Auth::login($user);
            $user = $request->user();
            $user->last_login_at = Carbon::now();
            $user->save();
            $setting = NotificationSetting::create(['user_id'=>$user->id]);

            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            $response['token_type'] = 'Bearer';
            $response1 = User::withCount('followers','followings','group')->with('subscription_plan','tags','tags.tag_id','tags.sub_tags.sub_tag','notification_setting','docs','stance_id:id,title')->with(['tags.sub_tags' => function($a) use($user) {
                $a->where('user_id', $user->id);
            }])->where('id',$user->id)->first()->toArray();
            $access_token = $tokenResult->accessToken;
            $response['result'] = array_merge($response1, ['access_token' => $access_token]);

            return sendSuccess("User Successfully Logged in!", $response);
        }

        return sendError('There is some problem.', null);
    }

    public function logout(Request $request)
    {

        $request->user()->token()->revoke();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out'
        ]);
    }

    public function forgot_password(Request $request)
    {


            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|exists:users,email',
            ]);


        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check = User::where(['email'=>$request->email,'social_id'=>null])->first();

        if (!$check){

            return sendError('You can not reset password against socail login!',null);
        }

        $code = $this->get_random_code();



            $user = User::where('email',$request->email)->first();
            // PasswordReset::request_password_reset($user->email, $code);
        if (!$user){
            return sendError("No user with this email found!", null);

        }


            if ($user->save()) {

                $email_data = [
                    'to_email' => $user->email,
                    'name' => $user->name,
                    'subject' => "Password Reset Code",
                    'body' => "Password Reset Code: " . $code
                ];

                $this->send_email($email_data);
                $data['result'] = $code;
                return sendSuccess('Verification code has been sent to your email!',$data);
            } else {

                return sendError("Something went wrong, Please try again!", null);

            }

    }

    public function forget_reset_password(Request $request)
    {



            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|exists:users,email',
                'password' => 'required|string|min:6',
                'confirm_password' => 'required|string|same:password',
//                'code' => 'required|string|exists:users,forgot_password_code'
            ]);


        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0],null);
        }


        $user = User::where('email',$request->email)->first();


        $user->password = bcrypt($request->password);

        if ($user->update()) {
            $response['result'] = $user;
            return sendSuccess("Password changed successfully! ",$response);

        } else {

            return sendError("Password can not be change! ",null);

        }
    }

    public function change_password(Request $request)
    {



            $validator = Validator::make($request->all(), [
                'old_password'=>'required|min:6',
                'password' => 'required|string|min:6',
                'confirm_password' => 'required|string|same:password',
//                'code' => 'required|string|exists:users,forgot_password_code'
            ]);


        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0],null);
        }


        $pass = Hash::check($request->old_password, Auth::user()->password);
        if (!$pass){
            return sendError('Old password in not valid!',null);
        }

        $user = User::find(Auth::id());
        $user->password = bcrypt($request->password);

        if ($user->update()) {
            $response['result'] = $user;
            return sendSuccess("Password changed successfully! ",null);

        } else {

            return sendError("Password can not be change! ",null);

        }
    }

    public function edit(Request $request)
    {
        $first_name = isset($request->first_name)?$request->first_name:Auth::user()->first_name;
        $last_name = isset($request->last_name)?$request->last_name:Auth::user()->last_name;
        $country = isset($request->country)?$request->country:Auth::user()->country;
        $state = isset($request->state)?$request->state:Auth::user()->state;
        $city = isset($request->city)?$request->city:Auth::user()->city;
        $bio = isset($request->bio)?$request->bio:Auth::user()->bio;
        $fight_weight = isset($request->weight)?$request->weight:Auth::user()->weight;
        $stance = isset($request->stance_id)?$request->stance_id:Auth::user()->stance_id;
        $height = isset($request->height)?$request->height:Auth::user()->height;
        $record = isset($request->record)?$request->record:Auth::user()->record;

        if ($request->hasfile('federal_id')) {

            $postData = $request->only('federal_id');

            $file = $postData['federal_id'];

            $fileArray = array('federal_id' => $file);

            // Tell the validator that this file should be an image
            $rules = array(
                'federal_id' => 'mimes:jpeg,jpg,png,gif|required|max:10000' // max 10000kb
            );

            // Now pass the input and rules into the validator
            $validator = Validator::make($fileArray, $rules);


            // Check to see if validation fails or passes
            if ($validator->fails()) {
                return sendError('upload image only (jpeg,jpg,png,gif)(10MB)', null);
            }

            $destinationpath = public_path("users/documents/" . $request->federal_id);
            File::delete($destinationpath);
            $file = $request->file('federal_id');
            $filename = str_replace(' ', '', $file->getClientOriginalName());
            $ext = $file->getClientOriginalExtension();
            $imgname = uniqid() . $filename;
            $destinationpath = public_path('users/documents');
            $file->move($destinationpath, $imgname);


            $federal_id = asset('users/documents') . '/' . $imgname;

        }else {
            if (Auth::user()->docs) {

                $federal_id = Auth::user()->docs->federal_id;
            }else{
                $federal_id = null;
            }

        }
        if ($request->hasfile('driving_license')) {

            $postData = $request->only('driving_license');

            $file = $postData['driving_license'];

            $fileArray = array('driving_license' => $file);

            // Tell the validator that this file should be an image
            $rules = array(
                'driving_license' => 'mimes:jpeg,jpg,png,gif|required|max:10000' // max 10000kb
            );

            // Now pass the input and rules into the validator
            $validator = Validator::make($fileArray, $rules);


            // Check to see if validation fails or passes
            if ($validator->fails()) {
                return sendError('upload image only1 (jpeg,jpg,png,gif)(10MB)', null);
            }

            $destinationpath = public_path("users/documents/" . $request->driving_license);
            File::delete($destinationpath);
            $file = $request->file('driving_license');
            $filename = str_replace(' ', '', $file->getClientOriginalName());
            $ext = $file->getClientOriginalExtension();
            $imgname1 = uniqid() . $filename;
            $destinationpath = public_path('users/documents');
            $file->move($destinationpath, $imgname1);

            $driving_license = asset('users/documents') . '/' . $imgname1;

        }else {
            if (Auth::user()->docs) {
                $driving_license = Auth::user()->docs->driving_license;
            }else{
                $driving_license = null;
            }

        }

        if ($request->hasfile('profile_image')) {

            $postData = $request->only('profile_image');

            $file = $postData['profile_image'];

            $fileArray = array('profile_image' => $file);

            // Tell the validator that this file should be an image
            $rules = array(
                'profile_image' => 'mimes:jpeg,jpg,png,gif|required|max:10000' // max 10000kb
            );

            // Now pass the input and rules into the validator
            $validator = Validator::make($fileArray, $rules);


            // Check to see if validation fails or passes
            if ($validator->fails()) {
                return sendError('upload image only1 (jpeg,jpg,png,gif)(10MB)', null);
            }

            $destinationpath = public_path("users/" . $request->profile_image);
            File::delete($destinationpath);
            $file = $request->file('profile_image');
            $filename = str_replace(' ', '', $file->getClientOriginalName());
            $ext = $file->getClientOriginalExtension();
            $imgname1 = uniqid() . $filename;
            $destinationpath = public_path('users/');
            $file->move($destinationpath, $imgname1);

            $profile_image = asset('users/') . '/' . $imgname1;

        }else {
            $profile_image = Auth::user()->profile_image;

        }

        $user = Auth::user();
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->country = $country;
        $user->state = $state;
        $user->city = $city;
        $user->bio = $bio;
        $user->weight = $fight_weight;
        $user->stance_id = $stance;
        $user->height = $height;
        $user->record = $record;
        $user->profile_image = $profile_image;
        $user->save();

        if ($request->has('tags') and !empty($request->tags)) {

            $tags = UserTags::where('user_id', $user->id)->whereNull('sub_tag_id')->delete();
            foreach ($request->tags as $t) {
                $tags = new UserTags();
                $tags->tag_id = $t;
                $tags->user_id = $user->id;
                $tags->save();
            }

        }
        if ($request->has('sub_tags') and !empty($request->sub_tags)) {
            $tags = UserTags::where('user_id', $user->id)->whereNotNull('sub_tag_id')->delete();
            foreach ($request->sub_tags as $t) {
                $parent = SubTag::where('id',$t)->first();
                $tags = new UserTags();
                    $tags->tag_id = $parent->tag_id;
                $tags->sub_tag_id = $t;
                $tags->user_id = $user->id;
                $tags->save();
            }
        }

        $docs = UserImage::where('user_id',Auth::id())->first();

        if ($docs) {
            $docs->federal_id = $federal_id;
            $docs->driving_license = $driving_license;
            $docs->save();
        }else{

            if ($request->hasFile('federal_id') or $request->hasFile('driving_license')){
                $docs = new UserImage();
                $docs->user_id = Auth::id();
                $docs->federal_id = $federal_id;
                $docs->driving_license = $driving_license;
                $docs->save();
            }
        }



            $response1 = User::withCount('followers','followings')->with('tags','tags.tag_id','tags.sub_tags.sub_tag','notification_setting','docs','stance_id:id,title')->with(['tags.sub_tags' => function($a) use($user) {
                $a->where('user_id', $user->id);
            }])->where('id',$user->id)->first();
            $response['result'] = $response1;

            return sendSuccess("User Updated Successfully!", $response);

    }

    function stripeRedirect(Request $request) {
        return sendSuccess('Success', null);

    }
    public function faqs(Request $request) {

        $data['result'] =  Faqs::get()->groupBy('category');
        return sendSuccess('Success', $data);

    }
    public function facilities(Request $request) {

        $data['result'] =  Facility::all();
        return sendSuccess('Success', $data);

    }
    public function disputes_list(Request $request) {

        $data['result'] =  DisputeList::all();
        return sendSuccess('Success', $data);

    }
    public function contact_e_p(Request $request) {

        $data['contact'] =  Admin::where('id',1)->first(['contact_email','contact_phone']);
        return sendSuccess('Success', $data);

    }





}
