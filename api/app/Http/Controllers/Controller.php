<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Input;
use Response;
use Route;
use Request;
use Event;
use App\User;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

	public $param;
	public $user;

	public function __construct(){
		$this->param = Input::all();
		$this->user = User::first();		
	}

	public function error($msg,$code){
		return Response::json([
			'result'=>0,
			'msg'=>$msg,
			'code'=>$code
		]);
	}

	public function success($data=[]){
		return Response::json([
			'result'=>1,
			'data'=>$data
		]);
	}


}
