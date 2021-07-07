<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;

// Resource
use App\Http\Resources\TagsResource;

// Modesl
use App\Models\Tag;

class TagsController extends BaseApiController {


	public function index(Request $request) {

		$tags 	= Tag::with('sub_tags')->get();

		if(!isset($tags) && empty($tags)) {
            return sendError('Tags not found!',null);

		} else {
		    $data['result'] = $tags;
            return sendSuccess('Tags Info',$data);

		}
	}
}
