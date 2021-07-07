<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;
use App\EmailSubscriber;
use App\Contact;

class HomeController extends Controller {

	public function index() {

		echo 'this is test API';
	}
}

