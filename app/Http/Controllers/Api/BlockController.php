<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlockUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BlockController extends Controller
{
    public function BlockUnblockUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'type'=> 'required',
        ]);
        if ($validator->fails()) {
            return sendError($validator->getMessageBag(), null);
        }
        if ($request->user_id == Auth::id()){
            return sendError('You can not block/unblock yourself!',null);
        }

      if ($request->type == "block") {


          $block = BlockUser::create([
              'block_by' => Auth::id(),
              'blocked_user' => $request->user_id,
          ]);

          return sendSuccess('User Blocked successfully.!', null);
      }elseif ($request->type == "unblock"){

          $block =  BlockUser::where([
              'block_by'=>Auth::id(),
              'blocked_user'=>$request->user_id,
          ])->delete();

          return sendSuccess('User Un-Blocked successfully.!', null);
      }else{
          return sendError('Undefined type',null);
      }

    }

    public function AllBlockUsers(Request $request)
    {
        $per_page = isset($request->per_page)?$request->per_page:20;
        $ids = BlockUser::where('block_by',Auth::id())->pluck('blocked_user');
        $data['Blocked Users'] = User::whereIn('id',$ids)->orderBy('created_at','desc')->paginate($per_page);
        return sendSuccess('Blocked User List',$data);



    }
}
