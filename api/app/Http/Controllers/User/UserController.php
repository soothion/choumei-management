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

class UserController extends Controller{
	/**
	 * @api {post} /user/index 1.用户列表
	 * @apiName list
	 * @apiGroup User
	 *
	 * @apiParam {String} role 可选,角色名关键字.
	 * @apiParam {String} name 可选,姓名关键字.
	 * @apiParam {String} username 可选,登录帐号关键字.
	 * @apiParam {Number} department_id 可选,部门ID.
	 * @apiParam {Number} status 可选,用户状态.1正常、2停用、3注销.
	 * @apiParam {Number} city_id 可选,城市ID.
	 * @apiParam {String} start 可选,起始时间.
	 * @apiParam {String} end 可选,结束时间.
	 * @apiParam {Number} page 可选,页数.
	 * @apiParam {Number} page_size 可选,分页大小.
	 * @apiParam {String} sort_key 排序的键,比如:created_at,update_at;
	 * @apiParam {String} sort_type 排序方式,DESC或者ASC;默认DESC
	 *
	 *
	 * @apiSuccess {Number} total 总数据量.
	 * @apiSuccess {Number} per_page 分页大小.
	 * @apiSuccess {Number} current_page 当前页面.
	 * @apiSuccess {Number} last_page 当前页面.
	 * @apiSuccess {Number} from 起始数据.
	 * @apiSuccess {Number} to 结束数据.
	 * @apiSuccess {String} name 用户姓名.
	 * @apiSuccess {String} username 用户名.
	 * @apiSuccess {Number} status 用户状态.1正常、2停用、3注销.
	 * @apiSuccess {Array} roles 用户角色.
	 * @apiSuccess {Object} department 用户部门.
	 * @apiSuccess {Object} city 用户部门.
	 * @apiSuccess {Object} position 用户部门.
	 * @apiSuccess {String} created_at 创建时间.
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "data": {
	 *	        "total": 3,
	 *	        "per_page": 20,
	 *	        "current_page": 1,
	 *	        "last_page": 1,
	 *	        "from": 1,
	 *	        "to": 3,
	 *	        "data": [
	 *	            {
	 *	                "id": 1,
	 *	                "username": "soothion",
	 *	                "name": "老王",
	 *	                "tel": "18617185201",
	 *	                "email": "soothion@sina.com",
	 *	                "result": "1",
	 *	                "created_at": "2015-05-07 14:15:00",
	 *	                "updated_at": "2015-05-11 07:18:23",
	 *	                "roles": [
	 *	                    {
	 *	                        "role_id": 2
	 *	                    },
	 *	                    {
	 *	                        "role_id": 1
	 *	                    },
	 *	                "department": {
	 *		                    "id": 1,
	 *		                    "title": "产品部"
	 *		                },
	 *	                "city": {
	 *		                    "id": 1,
	 *		                    "title": "深圳"
	 *		                },
	 *	                "position": {
	 *		                    "id": 1,
	 *		                    "title": "PHP"
	 *		                }
	 *	                ]
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
			$query = $query->where('created_at','<=',$param['end']);
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
		$page = isset($param['page'])?max($param['page'],1):1;
		$page_size = isset($param['page_size'])?$param['page_size']:20;

		//手动设置页数
		AbstractPaginator::currentPageResolver(function() use ($page) {
		    return $page;
		});

		$fields = array(
		    'id',
			'name',
			'username',
			'status',
			'created_at',
			'department_id',
			'city_id',
			'position_id'
		);

		//分页
	    $result = $query->select($fields)->paginate($page_size)->toArray();
	    unset($result['next_page_url']);
	    unset($result['prev_page_url']);
	    return $this->success($result);

	}


	/**
	 * @api {post} /user/export 5.导出用户
	 * @apiName export
	 * @apiGroup User
	 *
	 * @apiParam {Number} role_id 可选,角色ID.
	 * @apiParam {Number} department_id 可选,部门ID.
	 * @apiParam {Number} status 可选,用户状态.1正常、2停用、3注销.
	 * @apiParam {Number} city_id 可选,城市ID.
	 * @apiParam {String} start 可选,起始时间.
	 * @apiParam {String} end 可选,结束时间.
	 * @apiParam {String} keyword 可选,搜索关键字,匹配帐号或者姓名.
	 *
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "data": null
	 *	}
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
		$query = Manager::with(['roles'=>function($q){
			$q->lists('role_id');
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
			$query = $query->where('created_at','<=',$param['end']);
		}

		if(isset($param['keyword'])&&$param['keyword']){
			$keyword = '%'.$param['keyword'].'%';
			$query = $query->where('name','like',$keyword);
			$query = $query->orWhere('username','like',$keyword);
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
		
		$result = $query->get()->toArray();

		//触发事件，写入日志
	    Event::fire('user.export');
		
		//导出excel	 
		$title = 'users-'.date('Y-m-d');  
	    Excel::create($title, function($excel) use($result){
		    $excel->sheet('Sheet1', function($sheet) use($result){
			        $sheet->fromArray($result);
			    });
		})->export('xls');

	}

	 /**
	 * @api {post} /user/create 4.新增用户
	 * @apiName create
	 * @apiGroup User
	 *
	 * @apiParam {String} username 登录帐号.
	 * @apiParam {String} name 用户姓名.
	 * @apiParam {String} tel 用户电话.
	 * @apiParam {String} department_id 所属部门.
	 * @apiParam {String} position_id 职位.
	 * @apiParam {String} city_id 所属城市.
	 * @apiParam {String} email email.
	 * @apiParam {Number} status 用户状态.1正常、2停用、3注销.
	 * @apiParam {Array} roles 用户角色.
	 *
	 * @apiSuccessExample Success-Response:
	 *	    {
	 *	        "result": 1,
	 *	        "data": null
	 *	    }
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "用户创建失败"
	 *		}
	 */
	public function create()
	{
		$param = $this->param;
		DB::beginTransaction();
		$param['password'] = bcrypt($param['password']);
		$user = Manager::create($param);
		$role = 1;
		if(isset($param['roles'])){
			$roles = $param['roles'];
			unset($param['roles']);
			$role = $user->roles()->sync($roles);
		}
		
		if($user&&$role){
			DB::commit();
			//触发事件，写入日志
		    Event::fire('user.create',array($user));
			return $this->success();
		}
		else
		{
			DB::rollBack();
			return $this->error('用户创建失败');
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{

	}

	/**
	 * @api {post} /user/show/:id 2.查看用户信息
	 * @apiName show
	 * @apiGroup User
	 *
	 * @apiParam {Number} id 必填,用户ID.
	 *
	 * @apiSuccess {Number} id 用户ID.
	 * @apiSuccess {String} username 用户名.
	 * @apiSuccess {String} name 用户姓名.
	 * @apiSuccess {String} tel 用户电话.
	 * @apiSuccess {String} department_id 所属部门.
	 * @apiSuccess {String} position_id 职位.
	 * @apiSuccess {String} city_id 所属城市.
	 * @apiSuccess {String} email email.
	 * @apiSuccess {Number} status 用户状态.1正常、2停用、3注销.
	 * @apiSuccess {String} created_at 创建时间.
	 * @apiSuccess {String} update_at 更新时间.
	 * @apiSuccess {Array} roles 用户角色.
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "data": {
	 *	        "id": 1,
	 *	        "username": "soothion",
	 *	        "name": "老王",
	 *	        "tel": "18617185201",
	 * 	        "department_id": 1,
	 *	        "position_id": 1,
	 *	        "city_id": 1,
	 *	        "email": "soothion@sina.com",
	 *	        "result": "1",
	 *	        "created_at": "2015-05-07 14:15:00",
	 *	        "updated_at": "2015-05-11 07:18:23",
	 *	        "roles": [
	 *	            {
	 *	                "id": 1,
	 *	                "name": "test1sssssssssss",
	 *	                "slug": "administrator",
	 *	                "description": "manage administration privileges",
	 *	                "department_id": 1,
	 *	                "city_id": 1,
	 *	                "result": "1",
	 *	                "note": null,
	 *	                "created_at": "2015-05-05 06:23:43",
	 *	                "updated_at": "2015-05-11 07:15:28"
	 *	            }
	 *	        ]
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
		$user = Manager::with('roles')->find($id);
		return $this->success($user);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * @api {post} /user/update/:id 3.更新用户信息
	 * @apiName update
	 * @apiGroup User
	 *
	 * @apiParam {String} name 用户姓名.
	 * @apiParam {String} old_password 用户原密码.
	 * @apiParam {String} password 用户新密码.
	 * @apiParam {String} tel 用户电话.
	 * @apiParam {String} department_id 所属部门.
	 * @apiParam {String} position_id 职位.
	 * @apiParam {String} city_id 所属城市.
	 * @apiParam {String} email email.
	 * @apiParam {Number} status 用户状态.1正常、2停用、3注销.
	 * @apiParam {Array} roles 用户角色.
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
		DB::beginTransaction();
		$user = Manager::find($id);
		$update_role = 1;
		if(isset($param['password'])&&$param['password']){
			$param['password'] = bcrypt($param['password']);
		}
		if(isset($param['roles'])){
			$roles = $param['roles'];
			unset($param['roles']);
			$update_role = $user->roles()->sync($roles);
		}
		$update_user = $user->update($param);
		if($update_role&&$update_user){
			DB::commit();
			//触发事件，写入日志
			$response = Event::fire('user.update',array($user));
			return $this->success();
		}
		else
		{
			DB::rollBack();
			return $this->error('用户更新失败');
		}

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$account = Account::find($id);
		$account->delete();
	}

}