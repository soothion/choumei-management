<?php namespace App\Http\Controllers;

use App\Manager;
use App\RoleUser;
use Illuminate\Pagination\AbstractPaginator;
use DB;
use Kodeine\Acl\Models\Eloquent\Role;
use Kodeine\Acl\Models\Eloquent\Permission;
use Event;
use Excel;
use Auth;
use App\User;
use App\Order;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;

class UserController extends Controller{
	/**
	 * @api {post} /user/survey 1.用户根况
	 * @apiName survey
	 * @apiGroup User
	 *
	 *
	 * @apiSuccess {Number} total 用户总数.
	 * @apiSuccess {Number} day 本日新增用户数.
	 * @apiSuccess {Number} week 本周新增用户数.
	 * @apiSuccess {Number} month 本月新增用户数.
	 * @apiSuccess {Number} register 注册用户数.
	 * @apiSuccess {Number} first 首单消费数.
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "token": "",
	 *	    "data": {
	 *	    	"total": 1999,
	 *	        "day": 0,
	 *	        "week": 0,
	 *	        "month": 0,
	 *	        "register": {
	 *	            "2015-08-24": 6,
	 *	            "2015-08-25": 0,
	 *	            "2015-08-26": 1,
	 *	            "2015-08-27": 4,
	 *	            "2015-08-28": 0,
	 *	            "2015-08-29": 0,
	 *	            "2015-08-30": 0,
	 *	            "2015-08-31": 0,
	 *	            "2015-09-01": 0,
	 *	            "2015-09-02": 0,
	 *	            "2015-09-03": 0,
	 *	            "2015-09-04": 0,
	 *	            "2015-09-05": 0,
	 *	            "2015-09-06": 0,
	 *	            "2015-09-07": 0
	 *	        },
	 *	        "first": {
	 *	            "2015-08-24": 0,
	 *	            "2015-08-25": 0,
	 *	            "2015-08-26": 0,
	 *	            "2015-08-27": 0,
	 *	            "2015-08-28": 0,
	 *	            "2015-08-29": 0,
	 *	            "2015-08-30": 0,
	 *	            "2015-08-31": 0,
	 *	            "2015-09-01": 1,
	 *	            "2015-09-02": 0,
	 *	            "2015-09-03": 0,
	 *	            "2015-09-04": 0,
	 *	            "2015-09-05": 0,
	 *	            "2015-09-06": 0,
	 *	            "2015-09-07": 0
	 *	        }
	 *	    }
	 *	}
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 */
	public function survey()
	{
		$day = strtotime('today');
		$timestamp = time();  
		$week = date('Y-m-d', $timestamp-86400*date('w',$timestamp)+(date('w',$timestamp)>0?86400:-/*6*86400*/518400));  

		$month = strtotime(date('Y-m'));

		$data['total'] = User::count();
		$data['day'] = User::where('add_time','>=',$day)->count();
		$data['week'] = User::where('add_time','>=',$week)->count();
		$data['month'] = User::where('add_time','>=',$month)->count();

		$data['register'] = User::getRecentRegister();
		$data['first'] = User::getRecentFirst();

		return $this->success($data);
	}


	/**
	 * @api {post} /user/index 2.用户列表
	 * @apiName list
	 * @apiGroup User
	 *
	 *
	 * @apiParam {String} username 可选,臭美号;
	 * @apiParam {String} mobilephone 可选,手机号;
	 * @apiParam {String} companyCode 可选,集团邀请码;
	 * @apiParam {String} recommendCode 可选,商家推荐码或活动邀请码;
	 * @apiParam {Number} sex 可选,性别,0未知、1男、2女;
	 * @apiParam {String} start_at 可选,起始注册时间;
	 * @apiParam {String} end_at 可选,截止注册时间;
	 * @apiParam {String} area 可选,区域,省市区用英文逗号,分隔;
	 * @apiParam {Number} hair_type 可选,区域,省市区用英文逗号,分隔;
	 * @apiParam {Number} page 可选,页数.
	 * @apiParam {Number} page_size 可选,分页大小.
	 * @apiParam {String} sort_key 排序的键,比如:start_at,end_at;
	 * @apiParam {String} sort_type 排序方式,DESC或者ASC;默认DESC
	 *
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "token": "",
	 *	    "data": {
	 *	        "total": 761506,
	 *	        "per_page": 20,
	 *	        "current_page": 1,
	 *	        "last_page": 38076,
	 *	        "from": 1,
	 *	        "to": 20,
	 *	        "data": [
	 *	            {
	 *	                "user_id": 8635360,
	 *	                "username": "10800054",
	 *	                "nickname": "光芒万丈的火腿肠",
	 *	                "sex": "男",
	 *	                "growth": null,
	 *	                "mobilephone": 18500001111,
	 *	                "area": "",
	 *	                "hair_type": 2,
	 *	                "companyCode": null,
	 *	                "recommendCode": null,
	 *	                "add_time": "2015-08-27 21:32:36",
	 *	                "level": 2
	 *	            }
	 *	        ]
	 *	    }
	 *	}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 */
	public function index()
	{
		$param = $this->param;
		$query = User::getQueryByParam($param);

		$page = isset($param['page'])?max($param['page'],1):1;
		$page_size = isset($param['page_size'])?$param['page_size']:20;

		//手动设置页数
		AbstractPaginator::currentPageResolver(function() use ($page) {
		    return $page;
		});

		$fields = array(
			'user.user_id',
		    'user.username',
		    'user.nickname',
		    'user.sex',
		    'user.growth',
		    'user.mobilephone',
		    'user.area',
		    'company_code.code as companyCode',
		    'recommend_code_user.recommend_code as recommendCode',
		    'activity',
		    'user.add_time'
		);

		//分页
	    $result = $query->select($fields)->paginate($page_size)->toArray();
	    foreach ($result['data'] as $key=>$user) {
	    	$user['level'] = User::getLevel($user['growth']);
	    	$user['sex'] = User::getSex($user['sex']);
	    	$user['add_time'] = date('Y-m-d H:i:s',intval($user['add_time']));
	    	$result['data'][$key] = $user;
	    }
	    unset($result['next_page_url']);
	    unset($result['prev_page_url']);
	    $queries = DB::getQueryLog();
	    return $this->success($result);

	}


	/**
	 * @api {post} /user/export 3.导出用户
	 * @apiName export
	 * @apiGroup User
	 *
	 * @apiParam {String} username 可选,臭美号;
	 * @apiParam {String} mobilephone 可选,手机号;
	 * @apiParam {String} companyCode 可选,集团邀请码;
	 * @apiParam {String} recommendCode 可选,商家推荐码或活动邀请码;
	 * @apiParam {Number} sex 可选,性别,0未知、1男、2女;
	 * @apiParam {String} start_at 可选,起始注册时间;
	 * @apiParam {String} end_at 可选,截止注册时间;
	 * @apiParam {String} area 可选,区域,省市区用英文逗号,分隔;
	 * @apiParam {Number} page 可选,页数.
	 * @apiParam {Number} page_size 可选,分页大小.
	 * @apiParam {String} sort_key 排序的键,比如:start_at,end_at;
	 * @apiParam {String} sort_type 排序方式,DESC或者ASC;默认DESC
	 *
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 */
	public function export()
	{
		$param = $this->param;
		$query = User::getQueryByParam($param);

		$fields = array(
			'user.user_id',
		    'user.username',
		    'user.nickname',
		    'user.sex',
		    'user.growth',
		    'user.mobilephone',
		    'user.area',
		    'company_code.code as companyCode',
		    'recommend_code_user.recommend_code as recommendCode',
		    'activity',
		    'user.add_time'
		);

		//分页
	    $array = $query->select($fields)->take(5000)->get();
	    $result = [];
	    foreach ($array as $key=>$value) {
	    	$result[$key]['id'] = $key+1;
	    	$result[$key]['username'] = $value->username;
	    	$result[$key]['nickname'] = $value->nickname;
	    	$result[$key]['sex'] = User::getSex($value->sex);
	    	$result[$key]['growth'] = User::getLevel($value->growth);
	    	$result[$key]['mobilephone'] = $value->mobilephone;
	    	$result[$key]['area'] = $value->area;
	    	$result[$key]['companyCode'] = $value->companyCode;
	    	$result[$key]['salonCode'] = $value->activity==2?$value->recommendCode:'';
	    	$result[$key]['activityCode'] = $value->activity==1?$value->recommendCode:'';
	    	$result[$key]['add_time'] = date('Y-m-d H:i:s',intval($value->add_time));
	    }

		// 触发事件，写入日志
	    // Event::fire('user.export');
		
		//导出excel	   
		$title = '用户列表'.date('Ymd');
		$header = ['序号','臭美号','昵称','性别','会员等级','手机号','地区','集团邀请码','商家邀请码','活动邀请码','注册时间'];
		Excel::create($title, function($excel) use($result,$header){
		    $excel->sheet('Sheet1', function($sheet) use($result,$header){
			        $sheet->fromArray($result, null, 'A1', false, false);//第五个参数为是否自动生成header,这里设置为false
	        		$sheet->prependRow(1, $header);//添加表头

			    });
		})->export('xls');

	}

	
	/**
	 * @api {post} /user/show/:id 4.查看用户信息
	 * @apiName show
	 * @apiGroup User
	 *
	 * @apiParam {Number} id 必填,用户ID.
	 *
	 * @apiSuccess {String} username 用户名.
	 * @apiSuccess {String} img 头像地址.
	 * @apiSuccess {String} nickname 昵称.
	 * @apiSuccess {String} sex 性别.
	 * @apiSuccess {Number} hair_type 发长,1为长发,2为中发,3为短发.
	 * @apiSuccess {String} area 地区.
	 * @apiSuccess {String} birthday 生日.
	 * @apiSuccess {String} add_time 注册时间.
	 * @apiSuccess {String} mobilephone 手机号.
	 * @apiSuccess {String} grade 积分.
	 * @apiSuccess {String} growth 成长值.
	 * @apiSuccess {String} password 密码.
	 * @apiSuccess {String} costpwd 支付密码.
	 * @apiSuccess {String} companyId 集团ID.
	 * @apiSuccess {String} companyCode 集团码.
	 * @apiSuccess {String} companyName 集团名.
	 * @apiSuccess {String} recommendCode 推荐码.
	 * @apiSuccess {String} salonname 商家名,如果此项为空,那么
	 * @apiSuccess {String} level 等级.
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "token": "",
	 *	    "data": {
	 *	        "username": "10000000",
	 *	        "img": "http://img01.choumei.cn/1/1/2015082811011440730900326169088.jpg?imageView2/0/w/100/h/100",
	 *	        "nickname": "test",
	 *	        "sex": "女",
	 *	        "hair_type": 2,
	 *	        "area": "广东,深圳,南山区",
	 *	        "birthday": "2008-08-20",
	 *	        "add_time": "2014-06-03",
	 *	        "mobilephone": 15102011866,
	 *	        "grade": 93,
	 *	        "growth": 4050,
	 *	        "password": "已设置",
	 *	        "costpwd": "已设置",
	 *	        "companyId": 0,
	 *	        "companyCode": null,
	 *	        "companyName": null,
	 *	        "recommendCode": "8280",
	 *	        "salonname": "嘉美专业烫染",
	 *	        "level": 6
	 *	    }
	 *	}
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 */
	public function show($id)
	{
		$fields = [
			'username',
			'img',
			'nickname',
			'sex',
			'hair_type',
			'area',
			'birthday',
			'user.add_time',
			'mobilephone',
			'grade',
			'growth',
			'password',
			'costpwd',
			'user.companyId',
			'company_code.code as companyCode',
			'company_code.companyName',
			'recommend_code_user.recommend_code as recommendCode',
			'salon.salonname'
		];
		$user = User::leftJoin('recommend_code_user','user.user_id','=','recommend_code_user.user_id')
			->leftJoin('salon','salon.salonid','=','recommend_code_user.salon_id')
			->leftJoin('company_code','company_code.companyId','=','user.companyId')
			->select($fields)
			->find($id);

		if(!$user)
			throw new ApiException('用户不存在', ERROR::USER_NOT_FOUND);

		$user->add_time = date('Y-m-d',$user->add_time);
		$user->sex = User::getSex($user->sex);
		$user->level = User::getLevel($user->growth);
		if($user->password)
			$user->password = '已设置';
		else
			$user->password = '未设置';
		if(!$user->costpwd)
			$user->costpwd = '已设置';
		else
			$user->costpwd = '未设置';
		return $this->success($user);
	}


	/**
	 * @api {post} /user/update/:id 5.更新用户信息
	 * @apiName update
	 * @apiGroup User
	 *
 	 * @apiParam {Number} id 必填,用户ID.
	 *
	 * @apiParam {String} username 用户名.
	 * @apiParam {String} img 头像地址.
	 * @apiParam {String} nickname 昵称.
	 * @apiParam {String} sex 性别.
	 * @apiParam {String} area 地区.
	 * @apiParam {String} birthday 生日.
	 * @apiParam {String} add_time 注册时间.
	 * @apiParam {String} mobilephone 手机号.
	 * @apiParam {String} grade 积分.
	 * @apiParam {String} growth 成长值.
	 * @apiParam {String} password 密码.
	 * @apiParam {String} costpwd 支付密码.
	 * @apiParam {String} companyId 集团ID.
	 * @apiParam {String} companyCode 集团码.
	 * @apiParam {String} companyName 集团名.
	 * @apiParam {String} recommendCode 推荐码.
	 * @apiParam {String} salonname 商家名,如果此项为空,那么
	 * @apiParam {String} level 等级.
	 *
	 * 
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
	 *		    "msg": "没有符合条件数据"
	 *		}
	 */
	public function update($id)
	{
		$param = $this->param;
		$user = User::find($id);
		if(!empty($param['password'])){
			$param['password'] = md5($param['password']);
		}
		else
			unset($param['password']);
		if(!empty($param['costpwd'])){
			$param['costpwd'] = md5($param['costpwd']);
		}
		else
			unset($param['costpwd']);
		$result = $user->update($param);

		if($result){
			//触发事件，写入日志
			Event::fire('user.update',array($user));
			return $this->success();
		}
		throw new ApiException('用户更新失败', ERROR::USER_UPDATE_FAILED);
	}

	/**
	 * @api {post} /user/destroy/:id 6.删除用户信息
	 * @apiName destroy
	 * @apiGroup User
	 *
	 * @apiParam {String} id 用户ID.
	 */
	public function destroy($id)
	{
		$user = User::find($id);
		if(!$user)
			throw new ApiException('用户不存在', ERROR::USER_NOT_FOUND);
		$result = $user->delete();
		if($result){
			//触发事件，写入日志
			Event::fire('user.delete',array($user));
			return $this->success();
		}
		throw new ApiException('用户删除失败', ERROR::USER_UPDATE_FAILED);
	}

	/**
	 * @api {post} /user/company 7.集团用户数
	 * @apiName company
	 * @apiGroup User
	 *
	 * @apiParam {String} keyword 集团码或者集团名.
	 *
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "token": "",
	 *	    "data": {
	 *	        "total": 52,
	 *	        "per_page": 20,
	 *	        "current_page": 1,
	 *	        "last_page": 3,
	 *	        "from": 1,
	 *	        "to": 20,
	 *	        "data": [
	 *	            {
	 *	                "code": "0001",
	 *	                "name": "华为集团",
	 *	                "total": 3
	 *	            }
	 *	        ]
	 *	    }
	 *	}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "没有符合条件数据"
	 *		}
	 */
	public function company(){
		$param = $this->param;

		$page = isset($param['page'])?max($param['page'],1):1;
		$page_size = isset($param['page_size'])?$param['page_size']:20;

		//手动设置页数
		AbstractPaginator::currentPageResolver(function() use ($page) {
		    return $page;
		});

		$query = DB::table('company_code')
			->leftJoin('user','user.companyId','=','company_code.companyId')
			->select('company_code.code','company_code.companyName as name',DB::raw('count(user_id) as total'))
			->groupBy('company_code.code');

		if(!empty($param['keyword'])){
			$keyword = '%'.$param['keyword'].'%';
			$query = $query->where('company_code.code','like',$keyword)
				->orWhere('company_code.companyName','like',$keyword);
		}	
		$result = $query->paginate($page_size)->toArray();
		unset($result['next_page_url']);
	    unset($result['prev_page_url']);
		return $this->success($result);
	}

}
