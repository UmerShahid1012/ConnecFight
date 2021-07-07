<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Plan;
use App\Models\Stance;
use App\Models\Status;
use App\Models\SubTag;
use App\Models\Tag;
use Illuminate\Http\Request;
use Stripe\Price;
use Stripe\Stripe;

class AdminController extends Controller
{
    function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }
    public function tags(){
        $tags = Tag::all();
        $data['title'] = 'Tags List';
        return view('admin.lists.tags', $data)->with('tags', $tags);
    }
    public function edit_tags($id){
        $tags = Tag::find($id);
        $data['title'] = 'Edit Tag';
        return view('admin.lists.add.tag', $data)->with('sub', $tags);
    }
    public function tagSave(Request $request)
    {
            Tag::where('id',(int)$request->id)->update(['name' => $request->name]);
            return \redirect()->back()->with('success', 'Tag Edited Successfully');


    }
    public function sub_tags($id){
        $tags = SubTag::where('tag_id',$id)->get();
        $data['title'] = 'Sub Tags List';

        $data['id'] = $id;

        return view('admin.lists.sub_tags', $data)->with('tags', $tags);
    }
    public function add_sub_tags($id){
        $data['title'] = 'Add Sub Tags ';
        $data['type'] = "add";

        $data['id'] = $id;
        return view('admin.lists.add.sub_tag', $data)->with('tag_id', $id);
    }
    public function edit_sub_tags($id){
        $data['title'] = 'Edit Sub Tags ';
        $data['sub'] = SubTag::find((int)$id);
        $data['type'] = "edit";
        return view('admin.lists.add.sub_tag', $data);
    }
    public function subSave(Request $request)
    {
        if ($request->type == "edit") {
            SubTag::where('id',(int)$request->id)->update(['name' => $request->name]);
            return \redirect()->back()->with('success', 'Sub-Tag Edited Successfully');
        }else{
            SubTag::create(['tag_id' => (int)$request->id, 'name' => $request->name]);
            return \redirect()->back()->with('success', 'Sub-Tag Added Successfully');
        }

    }
    public function subDestroy($id)
    {
        SubTag::where('id',$id)->forceDelete();
        return \redirect()->back()->with('success', 'Sub-Tag Deleted Successfully');
    }
    public function stances(){
        $tags = Stance::all();
        $data['title'] = 'Stance List';
        return view('admin.lists.stance', $data)->with('tags', $tags);
    }
    public function add_stance(){
        $data['title'] = 'Add Events ';
        $data['type'] = "add";

        return view('admin.lists.add.stance', $data);
    }
    public function edit_stance($id){
        $data['title'] = 'Edit Events ';
        $data['sub'] = Stance::find((int)$id);
        $data['type'] = "edit";
        return view('admin.lists.add.stance', $data);
    }
    public function stanceSave(Request $request)
    {
        if ($request->type == "edit") {
            Stance::where('id',(int)$request->id)->update(['title' => $request->name]);
            return \redirect()->back()->with('success', 'Stance Edited Successfully');
        }else{
            Stance::create(['title' => $request->name]);
            return \redirect()->back()->with('success', 'Stance Added Successfully');
        }

    }
    public function events(){
        $tags = Event::all();
        $data['title'] = 'Events List';
        return view('admin.lists.events', $data)->with('tags', $tags);
    }
    public function add_event(){
        $data['title'] = 'Add Events ';
        $data['sub_tags'] = SubTag::where('tag_id',4)->get();
        $data['type'] = "add";

        return view('admin.lists.add.event', $data);
    }
    public function edit_event($id){
        $data['title'] = 'Edit Events ';
        $data['sub'] = Event::find((int)$id);
        $data['sub_tags'] = SubTag::where('tag_id',4)->get();
        $data['type'] = "edit";
        return view('admin.lists.add.event', $data);
    }
    public function eventSave(Request $request)
    {
        if ($request->type == "edit") {
            Event::where('id',(int)$request->id)->update(['title' => $request->name , 'sub_tag_id'=>$request->sub_tag]);
            return \redirect()->back()->with('success', 'Event Edited Successfully');
        }else{
            Event::create(['tag_id' => (int)$request->id, 'title' => $request->name, 'sub_tag_id'=>$request->sub_tag]);
            return \redirect()->back()->with('success', 'Event Added Successfully');
        }

    }
    public function eventDestroy($id)
    {
        Event::where('id',$id)->forceDelete();
        return \redirect()->back()->with('success', 'Event Deleted Successfully');
    }


    public function plans(){
        $tags = Plan::all();
        $data['title'] = 'Plan List';
        return view('admin.membership.plan', $data)->with('plans', $tags);
    }
    public function addPlan(){
        $data['title'] = 'Add Plans ';
        $data['type'] = "add";

        return view('admin.membership.add', $data);
    }
    public function edit_plan($id){
        $data['title'] = 'Edit Plans ';
        $data['sub'] = Plan::find((int)$id);
        $data['type'] = "edit";
        return view('admin.membership.add', $data);
    }
    public function savePlan(Request $request)
    {
        if ($request->type == "edit") {
           $plan = Plan::find((int)$request->id);
           if ($plan->price != $request->price || $plan->title != $request->title) {
               $price = Price::create([
                   'product' => env('STRIPE_PRODUCT_KEY'),
                   'unit_amount' => (100 * $request->price),
                   'nickname' => $request->title,
                   'currency' => 'usd',
                   'recurring' => [
                       'interval' => 'month',
                   ],

               ]);
           }
           if ($plan->tax != $request->tax){
               $tax_rate = \Stripe\TaxRate::create([
                   'display_name' => 'Sales Tax',
                   'description' => 'SF Sales Tax',
//                'jurisdiction' => 'CA - SF',
                   'percentage' => $request->tax,
                   'inclusive' => false,
               ]);
           }
           $tax_id = isset($tax_rate->id)?$tax_rate->id:$plan->tax_id;
           $price_id = isset($price->id)?$price->id:$plan->stripe_plan_id;
            Plan::where('id',(int)$request->id)->update(['tax_id'=>$tax_id,'price' => $request->price, 'title' => $request->title,'no_of_sparrings'=>$request->sparring,'no_of_applications'=>$request->applying,'no_of_challenges'=>$request->challenges,'stripe_price_id'=>$price_id]);
            return \redirect()->back()->with('success', 'Plan Edited Successfully');
        }else{
            $tax_rate = \Stripe\TaxRate::create([
                'display_name' => 'Cnf tax',
                'description' => 'cnf percentage',
//                'jurisdiction' => 'CA - SF',
                'percentage' => $request->tax,
                'inclusive' => false,
            ]);
            $price = Price::create([
                'product' => env('STRIPE_PRODUCT_KEY'),
                'unit_amount' => (100 * $request->price),
                'nickname' => $request->title,
                'currency' => 'usd',

            ]);

            Plan::create(['tax_id'=>$tax_rate->id, 'stripe_plan_id'=>$price->id,'price' => $request->price, 'title' => $request->title,'no_of_sparrings'=>$request->sparring,'no_of_applications'=>$request->applying,'no_of_challenges'=>$request->challenges]);
            return \redirect()->back()->with('success', 'Plan Added Successfully');
        }

    }
    public function planDestroy($id)
    {
        Plan::where('id',$id)->forceDelete();
        return \redirect()->back()->with('success', 'Plan Deleted Successfully');
    }

    public function statuses(){
        $tags = Status::all();
        $data['title'] = 'Statuses List';
        return view('admin.lists.statuses', $data)->with('tags', $tags);
    }
    public function edit_status($id){
        $tags = Status::find($id);
        $data['title'] = 'Edit Status';
        $data['type'] = "edit";

        return view('admin.lists.add.status', $data)->with('sub', $tags);
    }
    public function statusSave(Request $request)
    {
        if ($request->type == "edit") {
            Status::where('id',(int)$request->id)->update(['name' => $request->name]);
            return \redirect()->back()->with('success', 'Status Edited Successfully');
        }else{
            Status::create(['name' => $request->name]);
            return \redirect()->back()->with('success', 'Status Added Successfully');
        }


    }
    public function add_status(){
        $data['title'] = 'Add Status ';
        $data['type'] = "add";

        return view('admin.lists.add.status', $data);
    }

    public function stanceDestroy($id)
    {
        Stance::where('id',$id)->forceDelete();
        return \redirect()->back()->with('success', 'Stance Deleted Successfully');
    }

}
