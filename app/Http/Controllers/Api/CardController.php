<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CardController extends Controller
{
    public function add_card(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_holder_name' => 'required',
            'stripe_id' => 'required',
            'brand' => 'required',
            'last4' => 'required',
            'exp_month' => 'required',
            'exp_year' => 'required',
            'country' => 'required',
            'is_default' => 'required',
        ]);
        if ($validator->fails()) {
            return sendError($validator->getMessageBag(), null);
        }
        $user_id = ($request->user_id) ? $request->user_id : Auth::id();
        $is_default = $request->is_default;

        if ($is_default) {
            Card::where('user_id', $user_id)->update(['is_default' => false]);
        } else {
            $check = Card::where('user_id', $user_id)->where('is_default', true)->first();
            if (!$check) {
                $is_default = true;
            }
        }

        $card = Card::where('stripe_id', $request->stripe_id)->first();
        if (!$card) {
            $card = new Card();
            $card->user_id = $user_id;
            $card->stripe_id = $request->stripe_id;
        }
        $card->card_holder_name = $request->card_holder_name;
        $card->brand = $request->brand;
        $card->last4 = $request->last4;
        $card->exp_month = $request->exp_month;
        $card->exp_year = $request->exp_year;
        $card->country = $request->country;
        $card->is_default = $is_default;
        if ($card->save()) {
            $data['Card'] = Card::where('id', $card->id)->first();
            return sendSuccess('Card added successfully.', $data);
        }
        return sendError('There is some problem. Please try again.', null);
    }
    public function delete_card(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_id' => 'required',

        ]);
        if ($validator->fails()) {
            return sendError($validator->getMessageBag(), null);
        }
        $card = Card::where('id', $request->card_id)->first();
        if ($card) {
            $delete = Card::where('id', $request->card_id)->delete();

            $default = Card::where('user_id', Auth::id())->where('is_default', 1)->first();
            if (!$default) {
                $set_default = Card::where('user_id', Auth::id())->first();
                if ($set_default) {
                    $set_default->is_default = 1;
                    if ($set_default->save()) {
                        $per_page = isset($request->per_page)?$request->per_page:20;
                        $Card = Card::where(['user_id' => Auth::id()])->orderby('id', 'desc')->paginate($per_page);
                    }


                    return sendSuccess('Card deleted Successfully!', $Card);


                }
            }
            return sendSuccess('Card deleted Successfully!', null);
        }
        return sendError('Card not found!.', null);


    }

    public function update_card(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_id' => 'required',
            'is_default' => 'required',
        ]);
        if ($validator->fails()) {
            return sendError($validator->getMessageBag(), null);
        }
        $user_id = Auth::id();
        $is_default = $request->is_default;
        if ($is_default == true) {

            $card = Card::where('user_id', $user_id)->update(['is_default' => false]);
            $check = Card::where('id', $request->card_id)->update(['is_default' => $is_default]);
        } else {
            $check = Card::where('id', $request->card_id)->update(['is_default' => $is_default]);
            $check2 = Card::where(['user_id' => $user_id, 'is_default' => true])->first();
            if (!$check2) {
                $check = Card::where('id', $request->card_id)->update(['is_default' => true]);
                return sendError('One default card is mandatory!.', null);
            }

        }
        if ($check == 1) {
            $data['card'] = Card::where('id', $request->card_id)->first();

            return sendSuccess('Card Updated Successfully!.', $data);
        }


        return sendError('There is some problem. Please try again.', null);
    }
    public function card_listing(Request $request)
    {
        $per_page = isset($request->per_page)?$request->per_page:20;

        $Card = Card::where(['user_id' => Auth::id()])->orderby('id', 'desc')->paginate($per_page);
        if ($Card) {
            return sendSuccess('Card listing.', $Card);
        } else {
            return sendSuccess('Card listing.', null);

        }

    }
}
