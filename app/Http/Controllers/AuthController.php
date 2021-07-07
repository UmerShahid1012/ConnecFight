<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        return sendError('login to continue..!', null);
    }

    public function loginView()
    {
        if (Auth::guard('admin')->check()) {
            return view('admin.dashboard_content');
        } else {
            $data['title'] = 'Admin Login';
            return view('admin.login', $data);
        }

    }


    public function loginSave(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);


        if (Auth::guard('admin')->attempt($request->only('email', 'password'), $request->filled('remember'))) {
            return redirect()->route('admin.dashboard');
        } else {
            Session::flash('error', 'Invalid email or password.');
            return Redirect::to(URL::previous());
        }
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login_form');
    }


    public function showDashboard()
    {
        $data['title'] = 'CNF';
        return view('admin.dashboard_content', $data);
    }
    public function profile()
    {
        $data['title'] = 'Admin Profile';
        $data['admin'] = Admin::find(1);
        return view('admin.profile', $data);
    }
    public function profile_save(Request $request)
    {
//        dd($request->all());
        $admin = Admin::find(1);
        if ($request->hasfile('profile')) {

            $postData = $request->only('profile');

            $file = $postData['profile'];

            $fileArray = array('profile' => $file);

            // Tell the validator that this file should be an image
            $rules = array(
                'profile' => 'mimes:jpeg,jpg,png,gif|required|max:10000' // max 10000kb
            );

            // Now pass the input and rules into the validator
            $validator = Validator::make($fileArray, $rules);


            // Check to see if validation fails or passes
            if ($validator->fails()) {
                return sendError('upload image only (jpeg,jpg,png,gif)(10MB)', null);
            }

            $destinationpath = public_path("post/" . $request->media);
            File::delete($destinationpath);
            $file = $request->file('profile');
            $filename = str_replace(' ', '', $file->getClientOriginalName());
            $ext = $file->getClientOriginalExtension();
            $imgname = uniqid() . $filename;
            $destinationpath = public_path('post');
            $file->move($destinationpath, $imgname);
            $admin->name = isset($request->name)?$request->name:"";
            $admin->email = isset($request->email)?$request->email:"";
            $admin->profile_image = asset('post') . '/' . $imgname;
            $admin->save();
            return redirect()->back()->with('success','Updated Successfully');


        }
        $admin->name = isset($request->name)?$request->name:"";
        $admin->email = isset($request->email)?$request->email:"";
        $admin->save();
        return redirect()->back()->with('success','Updated Successfully');
    }

}
