<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fight;
use App\Models\Review;
use App\Models\Sparring;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'user_id' => 'required',

            ]);
        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }
        $check = User::find((int)$request->user_id);

        if(!$check){

            return sendError("User not found!",null);

        }
        $per_page = isset($request->per_page)?$request->per_page:20;
        $data['reviews'] = Review::where('given_to',$check->id)->orderBy('created_at','desc')->paginate($per_page);

        return sendSuccess('Reviews list!',$data);

    }
    public function given(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'user_id' => 'required',

            ]);
        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }
        $check = Auth::user();

        if(!$check){

            return sendError("User not found!",null);

        }
        $per_page = isset($request->per_page)?$request->per_page:20;
        $data['reviews'] = Review::with('given_by','given_by.stance_id','given_to','given_to.stance_id')->where('given_by',$check->id)->orderBy('created_at','desc')->paginate($per_page);

        return sendSuccess('Reviews list!',$data);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required',
            'given_to' => 'required',
            'comment' => 'required|string',
            'event_type' => 'required|string',
            'event_id' => 'required',
        ]);
        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }
        $check = User::find($request->given_to);

        if (!$check){

            return sendError('No user found!',null);

        }
        if ($request->event_type == "fight"){

            $check2 = Fight::find($request->event_id);


            if (!$check2){

                return sendError('No fight found!',null);

            }
            $fight_id = $check2->id;
            $sparring_id = null;

        }elseif ($request->event_type == "sparring"){

            $check2 = Sparring::find($request->event_id);

            if (!$check2){

                return sendError('No Sparring found!',null);

            }
            $sparring_id = $check2->id;
            $fight_id = null;

        }else{

            return sendError('Undefined event type!',null);

        }

        $check3 = Review::where([
            'given_by'=>Auth::id(),
            'given_to'=>(int)$request->given_to,
            'fight_id'=>$fight_id,
            'sparring_id'=>$sparring_id,
            ])->first();

        if ($check3){
            return sendError('You have already given review on this event!',null);
        }

        $review = Review::create([
            'given_by'=>Auth::id(),
            'given_to'=>(int)$request->given_to,
            'fight_id'=>$fight_id,
            'sparring_id'=>$sparring_id,
            'comment'=>$request->comment,
            'rating'=>$request->rating,
        ]);

        $data['review'] = $review;
        return sendSuccess('Review posted successfully!',$data);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'review_id' => 'required',

        ]);
        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check = Review::find((int)$request->review_id);
        if (!$check){
            return sendError('No review found!',null);
        }
        $rating = isset($request->rating)?$request->rating:$check->rating;
        $comment = isset($request->comment)?$request->comment:$check->comment;
        $review = Review::where('id',$check->id)->update(['rating'=>$rating,'comment'=>$comment]);

        $data['review'] = Review::find($check->id);

        return sendSuccess('Successfully Updated!',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'review_id' => 'required',

        ]);
        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check = Review::find((int)$request->review_id);
        if (!$check){
            return sendError('No review found!',null);
        }

        $review = Review::where('id',$check->id)->forceDelete();

        return sendSuccess('Review deleted successfully!',null);
    }
}
