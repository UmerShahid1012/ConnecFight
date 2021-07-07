<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
|
*/


// Tags Routes
Route::prefix('v1')->group(function () {
    //tag routes
    Route::get('tags-list', 'Api\TagsController@index');

    //auth routes
    Route::post('signup', 'Api\AuthController@signup');
    Route::post('login', 'Api\AuthController@login');
    Route::post('social-login', 'Api\AuthController@socialLogin');
    Route::post('forgot-password', 'Api\AuthController@forgot_password');
    Route::post('reset-password', 'Api\AuthController@forget_reset_password');
    Route::post('verify-account', 'Api\AuthController@verify_account');

    //StripeConnect apis
//    Route::get('/get-stripe-link','Api\AuthController@getStripeLink');
//    Route::get('/get-stripe-code','Api\AuthController@stripeRedirectUriMobile');
    Route::get('save_stripe_connect', 'Api\AuthController@stripeRedirectUriMobile');
    Route::get('stripe_redirect', 'Api\AuthController@stripeRedirect')->name('stripe.url');
    Route::get('get_stripe_connect_url', 'Api\AuthController@getStripeLink');
    Route::get('faqs', 'Api\AuthController@faqs');
    Route::get('contact_info', 'Api\AuthController@contact_e_p');


    Route::group(['middleware' => 'auth:api'], function () {

        Route::get('logout', 'Api\AuthController@logout');
        Route::post('user-documents', 'Api\AuthController@user_documents');
        Route::post('save_user_official_certificates', 'Api\AuthController@user_official_certificates');
        Route::post('user_official_certificates', 'Api\AuthController@all_user_official_certificates');
        Route::post('delete_user_official_certificates', 'Api\AuthController@delete_user_official_certificates');
        Route::post('user_sub_tags', 'Api\AuthController@getSubTags');
        Route::post('change-password', 'Api\AuthController@change_password');
        Route::post('get_user', 'Api\AuthController@getUser');
        Route::post('facilities', 'Api\AuthController@facilities');
        Route::post('disputes_list', 'Api\AuthController@disputes_list');


        //fighter apis
        Route::post('all-fighters', 'Api\FightController@all_fighters');
        Route::post('search-fighters', 'Api\FightController@search_fighters');
        Route::post('all-events', 'Api\FightController@all_events');
        Route::post('all-stances', 'Api\FightController@all_stances');
        Route::post('accept/reject-fight', 'Api\FightController@accept_reject');

        //dashboard apis
        Route::post('dashboard', 'Api\DashboardController@dashboard');
        Route::post('edit-profile', 'Api\AuthController@edit');
        Route::post('search', 'Api\DashboardController@search');
        Route::post('add-post-media', 'Api\DashboardController@addPostMedia');
        Route::post('add-post', 'Api\DashboardController@addPost');

        //profile apis
        Route::post('profile-detail', 'Api\DashboardController@profile_detail');
        Route::post('profile-data', 'Api\DashboardController@profile_data');

        //charge apis
        Route::post('charge', 'Api\ChargeController@charge');

        //Membership apis
        Route::post('all_plans', 'Api\PlanController@index');
        Route::post('purchase_new_plan', 'Api\PlanController@newPlan');
        Route::post('cancel_plan', 'Api\PlanController@cancel');
        Route::post('my_plan', 'Api\PlanController@myPlan');

        //like/dislike apis
        Route::post('like_dislike', 'Api\NotificationSettingController@like_unlike');



        //following apis
        Route::post('my_highlights', 'Api\DashboardController@my_highlights');
        Route::post('following', 'Api\DashboardController@following');
        Route::post('follow', 'Api\DashboardController@follow');

        //offer apis
        Route::post('checkin', 'Api\FightController@checkin');

        //sparring apis

        Route::post('offers', 'Api\SparringController@offers');
        Route::post('apply-sparring', 'Api\SparringController@send_offer');
        Route::post('accept-sparring', 'Api\SparringController@accept_offer');
        Route::post('cancel-sparring', 'Api\SparringController@cancel_offer');//new
//        Route::post('dispute', 'Api\SparringController@disputeFight');//new
        Route::post('edit_sparring', 'Api\SparringController@edit_sparring'); //new
        Route::post('no_response_checkin', 'Api\SparringController@no_response_checkin'); //new



        //dispute
        Route::post('report', 'Api\FightController@reportIssue');//new
        Route::post('dispute', 'Api\FightController@disputeFight');//new
        Route::post('withdraw_dispute', 'Api\FightController@withdraw_dispute');//new

        //wallet
        Route::post('user_wallet', 'Api\ChargeController@userWallet');//new


        //card apis
        Route::post('add-card', 'Api\CardController@add_card');
        Route::post('update-card', 'Api\CardController@update_card');
        Route::post('delete-card', 'Api\CardController@delete_card');
        Route::post('card-listing', 'Api\CardController@card_listing');

        //challenge apis
        Route::post('challenges', 'Api\FightController@challenges');
        Route::post('send-challenge', 'Api\FightController@send_challenge');

        //filter apis
        Route::post('filter_according_to_status', 'Api\FightController@filter_according_to_status');

        //block apis
        Route::post('block-unblock-user', 'Api\BlockController@BlockUnblockUser');
        Route::get('All-block-users', 'Api\BlockController@AllBlockUsers');

        //review apis
        Route::post('post-review', 'Api\ReviewController@store');
        Route::post('edit-review', 'Api\ReviewController@edit');
        Route::post('delete-review', 'Api\ReviewController@destroy');
        Route::get('fetch-user-reviews', 'Api\ReviewController@index');
        Route::get('given-reviews', 'Api\ReviewController@given');

        //group apis
        Route::post('create-group', 'Api\GroupController@create');
        Route::post('edit-group', 'Api\GroupController@edit');
        Route::post('delete-group', 'Api\GroupController@destroy');
        Route::post('join-group', 'Api\GroupController@join');
        Route::post('leave-group', 'Api\GroupController@leave');
        Route::get('get-group', 'Api\GroupController@get');

        //notification setting apis
        Route::post('update-notification-settings', 'Api\NotificationSettingController@index');

        //chat apis
        Route::post('create_chat', 'ChatController@create');
        Route::post('add_message', 'ChatController@addMessage');
        Route::post('get_chat', 'ChatController@getChat');
        Route::get('get_current_chats', 'ChatController@getCurrentChats');
        Route::post('seen_messages', 'ChatController@seenMessages');
        Route::post('delete_msg', 'ChatController@deleteMsg');
        Route::post('delete_chat', 'ChatController@deleteChat');
        Route::post('chat_gallery', 'ChatController@chatGallery');
        Route::post('chat_search', 'ChatController@chatSearch');
        Route::post('share', 'ChatController@share');

        //highlight apis
        Route::post('edit_highlight', 'Api\DashboardController@edit_highlight'); //new
        Route::post('delete_highlight', 'Api\DashboardController@delete_highlight'); //new




    });
});
