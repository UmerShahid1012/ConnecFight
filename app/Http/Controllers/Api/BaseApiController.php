<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Auth;

define('PER_PAGE', 10);

class BaseApiController extends Controller {

	public $user_info		= '';
	public $country_code	= '+1';

	public function __construct() {

		$this->user_info	= Auth::guard('api')->user();
	}

	public function success_response($resp_data) {

		$response_array['status'] = 'success';
		$response_array['message'] = $resp_data['message'];

		if((isset($resp_data['result']) && !empty($resp_data['result']))) {
			$response_array['result'] = $resp_data['result'];
		}
		return response()->json($response_array);
	}

	public function error_response($resp_data) {

		return response()->json(
			[
				'status'	=> 'error',
				'message'	=> $resp_data['message']
			]
		);
	}

	public function get_random_code() {

		return mt_rand(1000, 9999);
	}
}
