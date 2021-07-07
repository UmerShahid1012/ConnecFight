<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Fight;
use App\Models\Sparring;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Stripe\Charge;

class ChargeController extends Controller
{
    public function charge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_id' => 'required',
            'type' => 'required',
            'id' => 'required',
        ]);

        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check = Card::find($request->card_id);

        if (!$check) {
            return sendError('No card found!', null);
        }
        if ($request->type == "fight") {

            $check2 = Fight::with('challenger', 'defender')->find($request->id);
            if (!$check2) {
                return sendError('No fight found!', null);
            }
            $amount = $check2->fund / 2;

            if ($check2->defender != $check2->posted_by and $check2->defender->stripe_payout_account_id != null and Auth::user()->stripe_payout_account_id != null) {
                $stripeCharge = Charge::create(array(
                    'customer' => Auth::user()->stripe_payout_account_id,
                    'source' => $check->stripe_id,
                    'amount' => (100 * $amount),
                    'description' => "Fund transfered to defender",
                    'currency' => 'usd',
                    'transfer_data' => [
                        'destination' => $check2->defender->stripe_payout_account_id,
                    ],
                ));
//        dd($stripeCharge->status);
                if ($stripeCharge->status != "succeeded") {
                    return sendError("Payment was not successful!.", null);
                }
                Transaction::create([
                    'from' => Auth::id(),
                    'to' => $check2->defender,
                    'fight_id' => $check2->id,
                    'amount' => $amount,
                    'charge_id' => $stripeCharge->id
                ]);
            } else {
                return sendError("Payment was not successful!.", null);
            }

            if ($check2->challenger != $check2->posted_by and $check2->challenger->stripe_payout_account_id != null and Auth::user()->stripe_payout_account_id != null) {
                $stripeCharge = Charge::create(array(
                    'customer' => Auth::user()->stripe_payout_account_id,
                    'source' => $check->stripe_id,
                    'amount' => (100 * $amount),
                    'description' => "Fund transfered to challenger",
                    'currency' => 'usd',
                    'transfer_data' => [
                        'destination' => $check2->challenger->stripe_payout_account_id,
                    ],
                ));
//        dd($stripeCharge->status);
                if ($stripeCharge->status != "succeeded") {
                    return sendError("Payment was not successful!.", null);
                }

                Transaction::create([
                    'from' => Auth::id(),
                    'to' => $check2->challenger,
                    'fight_id' => $check2->id,
                    'amount' => $amount,
                    'charge_id' => $stripeCharge->id
                ]);
            }
            {
                return sendError("Payment was not successful!.", null);
            }

            return sendSuccess('Payment was successful!', null);

        } elseif ($request->type == "sparring") {
            $check2 = Sparring::find($request->id);
            if (!$check2) {
                return sendError('No sparring found!', null);
            }
            $amount = $check2->no_of_weeks * $check2->budget_per_week;

            if ($check2->assigned_to->stripe_payout_account_id != null and Auth::user()->stripe_payout_account_id != null) {
                $stripeCharge = Charge::create(array(
                    'customer' => Auth::user()->stripe_payout_account_id,
                    'source' => $check->stripe_id,
                    'amount' => (100 * $amount),
                    'description' => "Fund transfered to defender",
                    'currency' => 'usd',
                    'transfer_data' => [
                        'destination' => $check2->assigned_to->stripe_payout_account_id,
                    ],
                ));
//        dd($stripeCharge->status);
                if ($stripeCharge->status != "succeeded") {
                    return sendError("Payment was not successful!.", null);
                }
                Transaction::create([
                    'from' => Auth::id(),
                    'to' => $check2->assigned_to,
                    'fight_id' => $check2->id,
                    'amount' => $amount,
                    'charge_id' => $stripeCharge->id
                ]);
            } else {
                return sendError("Payment was not successful!.", null);

            }

            return sendSuccess('Payment was successful!', null);

        } else {
            return sendError('Undefined type!', null);
        }
    }

    public function userWallet(){
        $transactions = Transaction::where()->get();
    }
}
