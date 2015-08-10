<?php namespace App\Http\Controllers;

use App\Manager;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Captcha;
use Validator;
use Input;
use Auth;
use Response;
use Event;
use Illuminate\Support\Facades\Redis as Redis;
use App\Permission;
use DB;
use App\Role;
use Excel;

class IndexController extends Controller{


	public function test(){
				$param = $this->param;
		$query = Manager::with(['roles'=>function($q){
			$q->lists('role_id','name');
		}]);

		$query = $query->with(['department'=>function($q){
			$q->lists('id','title');
		}]);

		$query = $query->with(['city'=>function($q){
			$q->lists('id','title');
		}]);		

		$query = $query->with(['position'=>function($q){
			$q->lists('id','title');
		}]);

		//角色筛选
		if(isset($param['role_id'])&&$param['role_id']){
			$ids = RoleManager::where('role_id','=',$param['role_id'])->get(['user_id'])->toArray();
			$ids = array_values($ids);
			$query =Manager::whereHas('roles',function($q) use($param){
				$q->where('role_id','=',$param['role_id']);
			});
		}

		//所属部门筛选
		if(isset($param['department_id'])&&$param['department_id']){
			$query = $query->where('department_id','=',$param['department_id']);
		}

		//状态筛选
		if(isset($param['status'])&&$param['status']){
			$query = $query->where('status','=',$param['status']);
		}

		//所属城市
		if(isset($param['city_id'])&&$param['city_id']){
			$query = $query->where('city_id','=',$param['city_id']);
		}

		//起始时间
		if(isset($param['start'])&&$param['start']){
			$query = $query->where('created_at','>=',$param['start']);
		}

		//结束时间
		if(isset($param['end'])&&$param['end']){
			$query = $query->where('created_at','<',date('Y-m-d',strtotime('+1 day',strtotime($param['end']))));
		}
		//登录帐号筛选
		if(isset($param['username'])&&$param['username']){
			$keyword = '%'.$param['username'].'%';
			$query = $query->where('username','like',$keyword);
		}		
		//姓名筛选
		if(isset($param['name'])&&$param['name']){
			$keyword = '%'.$param['name'].'%';
			$query = $query->where('name','like',$keyword);
		}
		//角色名筛选
		if(isset($param['role'])&&$param['role']){
			$keyword = '%'.$param['role'].'%';
			$query = $query->whereHas('roles',function($q) use($keyword){
				$q->where('name','like',$keyword);
			});
		}

		//排序
		if(isset($param['sort_key'])&&$param['sort_key']){
			$param['sort_type'] = empty($param['sort_type'])?'DESC':$param['sort_type'];
			$query = $query->orderBy($param['sort_key'],$param['sort_type']);
		}

		$result = $query->get()->toArray();

		$header = ['ID','用户名','名字','电话','职位','城市','部门','邮箱','状态','添加时间','更新时间'];
		//导出excel	 
		$title = 'users-'.date('Y-m-d');  
	    Excel::create($title, function($excel) use($result,$header){
		    $excel->sheet('Sheet1', function($sheet) use($result,$header){
			        $sheet->fromArray($result, null, 'A1', false, false);//第五个参数为是否自动生成header,这里设置为false
	        		$sheet->prependRow(1, $header);//添加表头

			    });
		})->export('xls');
	}


	/**
	 * @api {get} /captcha 1.获取验证码
	 * @apiDescription 此接口用于生成登录验证码,直接将img的src属于指向这里即可。
	 * @apiName captcha
	 * @apiGroup Login
	 *
	 * @apiParam {String} uniqid 必填,前端生成一个32位唯一标识,用于记录验证码.
	 * 
	 * @apiSuccess {Image} image 返回验证码图片.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *     验证码
	 *
	 */
	public function captcha(){
		$captcha = new Captcha;
		$captcha = $captcha::create();
		return $captcha;
	}

	/**
	 * @api {post} /login 2.提交登录
	 * @apiName login
	 * @apiGroup Login
	 *
	 * @apiParam {String} username 必填,用户名.
	 * @apiParam {String} password 必填,密码.
	 * @apiParam {String} captcha 必填,验证码.
	 * @apiParam {String} uniqid 必填,唯一标识,用于验证验证码,与上面保持一致.
	 *
	 * @apiSuccess {String} token  加密过的token字符串.以后每次请求都需要将token加入到header中.格式如下：Authorization: Bearer {yourtokenhere},或者将token=xxx置于url中传递,否则将没有权限访问.错误码401表示token过期或者被加入黑名单(退出登录),400表示无效token.
	 * @apiSuccess {Int} uid 用户ID
	 * 
	 * @apiSuccessExample Success-Response:
	 *		{
	 *		    "result": 1,
	 *		    "data": {
	 *		        "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvbGFyYXZlbFwvcHVibGljXC9pbmRleC5waHBcL2xvZ2luIiwiaWF0IjoiMTQzMDkwNTQ4NyIsImV4cCI6IjE0MzA5MDkwODciLCJuYmYiOiIxNDMwOTA1NDg3IiwianRpIjoiYTQ4OWI3N2NmOWY4NmUxMWZjMWY1NTE3ZTQ4NjViZjYifQ.Njg2ZWQ3ZDNjZjFjMGY3ZGVmMDhmYjdkZjI0MDI2NTY4YjFjOTBmNzM4MzFhYzgzZjNkZTZmNTc3NGRhODI4Ng",
	 *		        "uid": 1
	 *		    }
	 *		}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "验证码错误"
	 *		}
	 */
	public function login(){
		$captcha = new Captcha;
		$validator = $captcha::check(strtolower($this->param['captcha']));
        if (!$validator)
	       return $this->error('验证码错误');
		if (Auth::attempt(array('username' => $this->param['username'], 'password' => $this->param['password'])))
		{       
    		$user = Manager::where('username',$this->param['username'])->firstOrFail();
    		if($user->status==2)
    			return $this->error('当前帐户已停用'); 
    		if($user->status==3)
    			return $this->error('当前帐户已注销'); 
    		$this->user = $user;
    		$token = JWTAuth::fromUser($user);
    		Event::fire('login',$user);
    		return $this->success(['token'=>$token,'uid'=>$user->id,'name'=>$user->name,'username'=>$user->username]);
    	}
        else
        {
        	return $this->error('用户名或密码错误');
        }
	}


	/**
	 * @api {post} /logout 3.退出登录
	 * @apiName logout
	 * @apiGroup Login
	 * 
	 * @apiSuccessExample Success-Response:
	 *		{
	 *		    "result": 1,
	 *		    "data": null
	 *		}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "token无效"
	 *		}
	 */
	public function logout()
	{
		$token = JWTAuth::getToken();
		$user = JWTAuth::parseToken()->authenticate();
		if(JWTAuth::invalidate($token))
		{
			Event::fire('logout',$user);
			$redis = Redis::connection();
			$redis->del('permissions:'.$token);
			return $this->success();
		}
		return $this->error('退出失败');
	}


}
?>