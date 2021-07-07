<?php

namespace App\Http\Controllers;

use App\Models\Fight;
use App\Models\Sparring;
use App\Models\SparringOffer;
use App\Models\SubTag;
use App\Models\User;
use App\Models\UserTags;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $artists = User::withTrashed()->with('stance_id','docs')->get();
        $data['title'] = 'User List';
        return view('admin.Users.index', $data)->with('users', $artists);
    }
    public function matchmakers()
    {
        $tags = UserTags::where('tag_id', 1)->where('sub_tag_id', '!=', null)->distinct('user_id')->pluck('user_id');
        $users = array();

        foreach ($tags as $tag){
            $user = User::withTrashed()->with('stance','docs')->where('id',$tag)->first();

                $users[] = $user;

        }

        $data['title'] = 'Matchmakers List';
        $data['tags'] = SubTag::where('tag_id',1)->get();
        return view('admin.Users.index', $data)->with('users', $users);
    }public function athletes()
    {
        $tags = UserTags::where('tag_id', 4)->where('sub_tag_id', '!=', null)->distinct('user_id')->pluck('user_id');
        $users = array();
        foreach ($tags as $tag){
            $user = User::withTrashed()->with('stance','docs')->where('id',$tag)->first();
            $users[] = $user;
        }
        $data['title'] = 'Athletes List';
        $data['tags'] = SubTag::where('tag_id',4)->get();

        return view('admin.Users.index', $data)->with('users', $users);
    }
    public function user_tags(Request $request)
    {
//        dd($request->id);
        $tags = UserTags::with('tag','sub_tag')->whereNotNull('sub_tag_id')->where('user_id',(int)$request->id)->get();
//        dd($tags);
        $data['title'] = 'User Tags';
        return view('admin.Users.user_tags', $data)->with('tags', $tags);
    }
    public function add_record(Request $request)
    {
        $tags = UserTags::where('id',(int)$request->id)->first();
        $data['title'] = 'User Tags';
        $data['type'] = $request->type;
        return view('admin.Users.add_record', $data)->with('sub', $tags);
    }
    public function save_record(Request $request)
    {
            UserTags::where('id',(int)$request->id)->update(['best_record' => $request->record ]);
            return \redirect()->back()->with('success', 'Record Updated Successfully');

    }
    public function user_fights(Request $request)
    {
        $tags = Fight::where('challenger',(int)$request->id)->orWhere('defender',(int)$request->id)->get();
        $data['title'] = 'User Fights';
        return view('admin.fights.fights', $data)->with('fights', $tags);
    }
    public function user_fights_arranged(Request $request)
    {
        $tags = Fight::where('posted_by',(int)$request->id)->get();
        $data['title'] = 'User Arranged Fights';
        return view('admin.fights.fights', $data)->with('fights', $tags);
    }
    public function get_result(Request $request)
    {
        $tags = UserTags::where('sub_tag_id',(int)$request->id)->distinct('user_id')->pluck('user_id');
        $tag = UserTags::where('sub_tag_id',(int)$request->id)->first();
//        dd($tag);
        $users = array();
        foreach ($tags as $tag){
            $user = User::withTrashed()->with('stance','docs')->where('id',$tag)->first();
            $users[] = $user;
        }

            $data['title'] = $request->title;
        if ($data['title'] == 'Matchmakers List'){
            $data['tags'] = SubTag::where('tag_id', 1)->get();

        }else {
            $data['tags'] = SubTag::where('tag_id', 4)->get();
        }



        return view('admin.Users.index', $data)->with('users', $users);
    }
    public function user_jobs(Request $request)
    {
//        dd($request->id);
        if ($request->type == 'myJobs') {
            $artists = Sparring::where('user_id', (int)$request->id)->get();
//        dd($tags);
            $data['title'] = 'User Sparrings';
            return view('admin.fights.sparrings', $data)->with('sparrings', $artists);
        }else{
            $ids = SparringOffer::where('offer_by',(int)$request->id)->distinct('sparring_id')->pluck('sparring_id');
            $artists = Sparring::whereIn('user_id', $ids)->get();

            $data['title'] = 'User Applied Sparrings';
            return view('admin.fights.sparrings', $data)->with('sparrings', $artists);
        }
    }

    public function destroy($id)
    {
        User::where('id',$id)->forceDelete();
        return \redirect()->route('admin.users')->with('success', 'User Deleted Successfully');
    }

    public function accept_reject(Request $request)
    {
//        dd($request->all());
        $id = (int)$request->id;
        if ($request->type == 'accepted'){
           $user = User::where('id',$id)->update(['is_verified'=>1,'is_rejected'=>0]);
            return \redirect()->route('admin.users')->with('success', 'User Accepted Successfully');

        }
        if($request->type == 'rejected'){
            User::where('id',(int)$request->id)->update(['is_rejected'=>1,'is_verified'=>0]);
            return \redirect()->route('admin.users')->with('success', 'User Rejected Successfully');
        }
    }
    public function ban_unban(Request $request)
    {
//        dd($request->all());
        $id = (int)$request->id;
        if ($request->type == 'block'){
           $user = User::where('id',$id)->delete();
            return \redirect()->route('admin.Users')->with('success', 'User Blocked Successfully');

        }
        if($request->type == 'unblock'){
            User::where('id',(int)$request->id)->restore();
            return \redirect()->route('admin.Users')->with('success', 'User Unblocked Successfully');
        }
    }
}
