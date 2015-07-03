<?php namespace App\Http\Controllers;

use App\Log;
use App\RoleUser;
use Illuminate\Pagination\AbstractPaginator;
use DB;
use Kodeine\Acl\Models\Eloquent\Role;
use Kodeine\Acl\Models\Eloquent\Permission;
use Event;
use Excel;

class LogController extends Controller{
	/**
	 * @api {post} /log/index 1.日志列表
	 * @apiName list
	 * @apiGroup Log
	 *
	 * @apiParam {String} username 可选,登录用户名.
	 * @apiParam {String} object 可选,操作对象.
	 * @apiParam {String} start 可选,起始时间.
	 * @apiParam {String} end 可选,结束时间.
	 * @apiParam {Number} page 可选,页数.
	 * @apiParam {Number} page_size 可选,分页大小.
	 *
	 * 
	 * @apiSuccess {Number} total 总数据量.
	 * @apiSuccess {Number} per_page 分页大小.
	 * @apiSuccess {Number} current_page 当前页面.
	 * @apiSuccess {Number} last_page 当前页面.
	 * @apiSuccess {Number} from 起始数据.
	 * @apiSuccess {Number} to 结束数据.
	 * @apiSuccess {String} username 登录用户名.
	 * @apiSuccess {String} roles 用户角色.
	 * @apiSuccess {String} operation 操作类型.
	 * @apiSuccess {String} Slug 操作路径.
	 * @apiSuccess {String} object 操作对象.
	 * @apiSuccess {String} ip 操作IP.
	 * @apiSuccess {String} created_at 操作时间.
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 * 
	 *	{
	 *	    "result": 1,
	 *	    "msg": "success",
	 *	    "data": {
	 *	        "total": 5,
	 *	        "per_page": 20,
	 *	        "current_page": 1,
	 *	        "last_page": 1,
	 *	        "from": 1,
	 *	        "to": 5,
	 *	        "data": [
	 *	            {
	 *	                "id": 5,
	 *	                "username": "soothion",
	 *	                "roles": "User",
	 *	                "operation": "更新用户信息",
	 *	                "slug": "user.update",
	 *	                "object": "soothion",
	 *	                "ip": "::1",
	 *	                "created_at": "2015-05-11 07:29:48"
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
		$query = Log::getQuery();

		//操作对象
		if(isset($param['object'])&&$param['object']){
			$query = $query->where('object','=',$param['object']);
		}

		//起始时间
		if(isset($param['start'])&&$param['start']){
			$query = $query->where('created_at','>=',$param['start']);
		}

		//结束时间
		if(isset($param['end'])&&$param['end']){
			$query = $query->where('created_at','<=',$param['end']);
		}

		if(isset($param['username'])&&$param['username']){
			$keyword = '%'.$param['username'].'%';
			$query = $query->where('username','like',$keyword);
		}
		$page = isset($param['page'])?max($param['page'],1):1;
		$page_size = isset($param['page_size'])?$param['page_size']:20;

		//手动设置页数
		AbstractPaginator::currentPageResolver(function() use ($page) {
		    return $page;
		});

		$fields = ['id','username','roles','operation','slug','object','ip','created_at'];

		//分页
	    $result = $query->select($fields)->paginate($page_size)->toArray();
	    unset($result['next_page_url']);
	    unset($result['prev_page_url']);
	    return $this->success($result);

	}



	/**
	 * @api {post} /log/export 2.导出日志
	 * @apiName export
	 * @apiGroup Log
	 *
	 * @apiParam {String} username 可选,登录用户名.
	 * @apiParam {String} object 可选,操作对象.
	 * @apiParam {String} start 可选,起始时间.
	 * @apiParam {String} end 可选,结束时间.
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
		$query = Log::getQuery();

		//操作对象
		if(isset($param['object'])&&$param['object']){
			$query = $query->where('object','=',$param['object']);
		}

		//起始时间
		if(isset($param['start'])&&$param['start']){
			$query = $query->where('created_at','>=',$param['start']);
		}

		//结束时间
		if(isset($param['end'])&&$param['end']){
			$query = $query->where('created_at','<=',$param['end']);
		}

		if(isset($param['username'])&&$param['username']){
			$keyword = '%'.$param['username'].'%';
			$query = $query->where('username','like',$keyword);
		}

		$result = $query->get();
	    foreach ($result as $key => $value) {
	    	$result[$key] = (array)$value;
	    }
		//触发事件，写入日志
	    Event::fire('log.export');
		
		//导出excel	   
		$title = 'logs-'.date('Y-m-d');
	    Excel::create($title, function($excel) use($result){
		    $excel->sheet('Sheet1', function($sheet) use($result){
			        $sheet->fromArray($result);
			    });
		})->export('xls');

	}


}