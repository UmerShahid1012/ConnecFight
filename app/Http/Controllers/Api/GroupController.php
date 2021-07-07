<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use App\Models\UserTags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'title' => 'required|string',
            'member_ids'=>'required|array'

        ]);
        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }
        $check = UserTags::where('user_id',Auth::id())->where('tag_id',1)->first();

        if (!$check){

            return sendError('You are not allowed to make group!',null);

        }

        $check1 = Group::where('made_by',Auth::id())->first();

        if ($check1){
            $data['group'] = $check1;
            return sendError('You already have one group!',$data);

        }

        $data['group'] = Group::create(['made_by'=>Auth::id(),'title'=>$request->title]);
        foreach ($request->member_ids as $id){

            $group = GroupMember::create([
                'member_id'=>$id,
                'group_id'=>$data['group']->id,
            ]);
        }

        return sendSuccess('Group created successfully!',$data);


    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'group_id' => 'required',

        ]);
        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check = Group::where('id',$request->group_id)->first();

        if (!$check){

            return sendError('No group found!!',null);

        }

        $title = isset($request->title)?$request->title:$check->title;

        $group = Group::where('id',$request->group_id)->update(['title'=>$title]);

        $data['group'] = Group::with('made_by','members','members.member_id')->find($check->id);

        return sendSuccess('Group edited successfully!',$data);


    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'group_id' => 'required',

        ]);
        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check = Group::where('made_by',Auth::id())->first();

        if (!$check){

            return sendError('No group found!!',null);

        }

        $data['group'] = Group::where('id',$request->group_id)->delete();

        return sendSuccess('Group deleted successfully!',null);


    }

    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'type' => 'required',

        ]);
        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }
        if ($request->type == 'fighter'){
            $ids = GroupMember::where('member_id', Auth::id())->pluck('group_id');
            $groups = Group::whereIn('id', $ids)->get();
            $all = array();
            foreach ($groups as $group){
                $group = Group::with('made_by')->where('id',$group->id)->first();
                $member_ids = GroupMember::where('group_id', $group->id)->pluck('member_id');
                $members = User::with('tags', 'tags.tag_id', 'notification_setting', 'docs', 'stance_id:id,title')->whereIn('id', $member_ids)->get();
                $g = array_merge($group->toArray(), ['members' => $members]);
                array_push($all,$g);
            }
            $data['group'] = $all;


        }else {
            $check = Group::where('made_by', Auth::id())->first();

            if (!$check) {

                return sendError('No group found!!', null);

            }

            $groups = Group::with('made_by')->where('made_by', Auth::id())->get();
            $all = array();
            foreach ($groups as $group){
                $group = Group::with('made_by')->where('id',$group->id)->first();
                $member_ids = GroupMember::where('group_id', $group->id)->pluck('member_id');
                $members = User::with('tags', 'tags.tag_id', 'notification_setting', 'docs', 'stance_id:id,title')->whereIn('id', $member_ids)->get();
                $g = array_merge($group->toArray(), ['members' => $members]);
                array_push($all,$g);
            }
            $data['group'] = $all;
        }

        return sendSuccess('Group!',$data);


    }

    public function join(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'user_id' => 'required',

        ]);
        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check = Group::where('made_by',Auth::id())->first();

        if (!$check){

            return sendError('No group found!!',null);

        }

        $check1 = GroupMember::where(['group_id'=>$check->id,'member_id'=>$request->user_id])->first();

        if ($check1){

            return sendError('You are already member of this group!',null);

        }

        $group = GroupMember::create([
            'member_id'=>$request->user_id,
            'group_id'=>$check->id,
        ]);

        $data['group'] = Group::with('made_by','members','members.member_id')->where('id',$check->id)->first();

        return sendSuccess('Added to group successfully!',$data);

    }

    public function leave(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'group_id' => 'required',

        ]);
        if ($validator->fails()) {

            return sendError($validator->errors()->all()[0], null);

        }

        $check = Group::where('id',$request->group_id)->first();

        if (!$check){

            return sendError('No group found!!',null);

        }

        $check1 = GroupMember::where(['group_id'=>$request->group_id,'member_id'=>Auth::id()])->first();

        if (!$check1){

            return sendError('You are not member of this group!',null);

        }

        $group = GroupMember::where([
            'member_id'=>Auth::id(),
            'group_id'=>$request->group_id,
        ])->forceDelete();

        $data['group'] = Group::with('made_by','members','members.member_id')->where('id',$request->group_id)->first();

        return sendSuccess('Group left successfully!',$data);

    }



}
