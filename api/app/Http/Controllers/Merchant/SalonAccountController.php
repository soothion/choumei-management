<?php namespace App\Http\Controllers\Merchant;


use App\Http\Controllers\Controller;
use App\SalonUser;
use Illuminate\Pagination\AbstractPaginator;
use DB;
use App\Salon;
use App\SalonAccount;


class SalonAccountController extends Controller {
	
	private $pwd = "choumei";
	
	private $addMsg = array(
				1=>"当前店铺已存在普通用户，请查询",
				2=>"当前商户已存在超级管理员，请查询",
		);

	private $sequenceType = array(
						1=>"status",
						2=>"roleType"
			);
			
	private $orderType = array(
						1=>"desc",
						2=>"asc"
			);
			
	
	/**
	 * @api {post} /salonAccount/index 1.店铺账号列表
	 * @apiName index
	 * @apiGroup salonAccount
	 *
	 * @apiParam {String} salonname 可选,店铺名称
	 * @apiParam {String} name 可选,商户名
	 * @apiParam {String} username 可选,账号名称
	 * @apiParam {Number} page 可选,页数.
	 * @apiParam {Number} page_size 可选,分页大小.
	 * @apiParam {Number} sequence 可选,排序字段 1状态 2角色.
	 * @apiParam {Number} order 可选,排序 1倒序 2升序.
	 *
	 * @apiSuccess {Number} total 总数据量.
	 * @apiSuccess {Number} per_page 分页大小.
	 * @apiSuccess {Number} current_page 当前页面.
	 * @apiSuccess {Number} last_page 当前页面.
	 * @apiSuccess {Number} from 起始数据.
	 * @apiSuccess {Number} to 结束数据.
	 * @apiSuccess {Number} salonUserId 账号Id.
	 * @apiSuccess {String} username 账号名称.
	 * @apiSuccess {Number} salonid 店铺Id.
	 * @apiSuccess {Number} roleType 账号类型 1.普通用户2.超级管理员.
	 * @apiSuccess {Number} addTime 创建时间 （时间戳 1436242693）.
	 * @apiSuccess {String} status 状态  1.正常使用2.已停用3.已删除.
	 * @apiSuccess {String} merchantId 商户Id.
	 * @apiSuccess {String} name 商户名称.
	 * @apiSuccess {String} salonname 店铺名称.
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *  {
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
	 *	                "salonUserId": 1155,
	 *	                "username": "臭美商盟美发店",
	 *	                "salonid": 2,
	 *	                "roleType": 1,
	 *	                "addTime": 1436236918,
	 *	                "status": 3,
	 *	                "merchantId": 1,
	 *	                "name": "15854856985",
	 *	                "salonname": "名流造型SPA（皇岗店）"
	 *	            },
	 *	            ......
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
		$page = isset($param['page'])?max($param['page'],1):1;
		$page_size = isset($param['page_size'])?$param['page_size']:20;
		$sequence = isset($param["sequence"])?$param["sequence"]:1;//排序字段
    	$orders = isset($param["order"])?$param["order"]:1;//1倒序 2升序
		if(in_array($sequence, array(1,2)) && in_array($orders, array(1,2)))
    	{
    		$orderName = $this->sequenceType[$sequence];
    		$orderLr = $this->orderType[$orders];
    	}
		$result = SalonAccount::getList($param,$page,$page_size,$orderName,$orderLr);
		unset($result['next_page_url']);
	    unset($result['prev_page_url']);
	    return $this->success($result);
	}

	/**
	 * @api {post} /salonAccount/save 2.添加账号
	 * @apiName save
	 * @apiGroup salonAccount
	 *
	 * @apiParam {String} username 必填,用户名.
	 * @apiParam {Number} salonid 必填,店铺Id.
	 * @apiParam {Number} merchantId 必填,商户Id.
	 * @apiParam {String} roleType 必填,账号类型 1.普通用户2.超级管理员.
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "msg": "",
	 *	    "data": {
	 *	    }
	 *	}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "当前店铺已存在普通用户，请查询"
	 *		}
	 */
	
	public function save()
	{
		$param = $this->param;
		if(!isset($param["username"]) || !isset($param["salonid"]) || !isset($param["merchantId"]) || !isset($param["roleType"]))
		{
			return $this->error("参数错误");	
		}
		$save["username"] = $param["username"];
		$save["salonid"] = $param["salonid"];
		$save["merchantId"] = $param["merchantId"];
		$save["roleType"] = $param["roleType"];
		$save["password"] = md5($this->pwd);
		$save["addTime"] = time();
		$save["status"] = 1;
		
		$nums = SalonAccount::getAccountNums($save);//查看管理员个数是否符合要求 
		if($nums >= 1)
		{
			return $this->error($this->addMsg[$param["roleType"]]);
		}
		$id = SalonUser::insertGetId($save);//添加账号
		if($id)
		{
			return $this->success();
		}	
		else
		{
			return $this->error("更新失败");	
		}
	}
	
	/**
	 * @api {post} /salonAccount/resetPwd 3.重置密码
	 * @apiName resetPwd
	 * @apiGroup salonAccount
	 *
	 *@apiParam {Number} salonUserId 必填,账号ID.
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "msg": "",
	 *	    "data": {
	 *	    }
	 *	}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "操作失败"
	 *		}
	 */

	public function resetPwd()
	{
		$param = $this->param;
		if(!isset($param['salonUserId']))
		{
			return $this->error("参数错误");	
		}
		$status = SalonAccount::doUpdate($param['salonUserId'], array("password"=>md5($this->pwd)));
		if($status !== false)
		{
			return	$this->success();
		}
		else
		{
			return $this->error("操作失败");
		}
	}
	
	/**
	 * @api {post} /salonAccount/delAct 4.停用 删除账号
	 * @apiName delAct
	 * @apiGroup salonAccount
	 *
	 *@apiParam {Number} salonUserId 必填,账号ID.
	 *@apiParam {Number} type 必填,操作类型 1.停用 2.删除.
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "msg": "",
	 *	    "data": {
	 *	    }
	 *	}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "操作失败"
	 *		}
	 */
	public function delAct()
	{
		$param = $this->param;
		if(!isset($param['salonUserId']) || !isset($param['type']))
		{
			return $this->error("参数错误");	
		}
		if($param["type"] == 1)
		{
			$status = 2;//停用
		}
		elseif ($param["type"] == 2)
		{
			$status = 3;//删除
		}
		else 
		{
			return $this->error("参数异常");
		}
		$status = SalonAccount::doUpdate($param['salonUserId'], array("status"=>$status));
		if($status !== false)
		{
			return	$this->success();
		}
		else
		{
			return $this->error("操作失败");
		}
	}
	
	
	/**
	 * @api {post} /salonAccount/getSalonName 5.模糊查找店铺
	 * @apiName getSalonName
	 * @apiGroup salonAccount
	 *
	 * @apiParam {String} salonname 必填,店铺名称.
	 * 
	 * @apiSuccess {Number} merchantId 商户Id.
	 * @apiSuccess {String} name 商户名称.
	 * @apiSuccess {Number} salonid 店铺Id.
	 * @apiSuccess {String} salonname 店铺名称.
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "data": [
	 *	        {
	 *	            "merchantId": 1,
	 *	            "salonid": 1,
	 *	            "salonname": "嘉美专业烫染",
	 *				"name": "嘉烫染"
	 *	        },
	 *	        {
	 *	            "merchantId": 33,
	 *	            "salonid": 804,
	 *	            "salonname": "臭美腾讯专属高端店"
	 *              "name": "嘉美烫染"
	 *	        },
	 *	        ......
	 *	    ]
	 *	}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "操作失败"
	 *		}
	 */
	public function getSalonName()
	{
		$param = $this->param;
		if(!isset($param['salonname']))
		{
			return $this->error("参数错误");	
		}
		$data = SalonAccount::getSalonNamebyCon($param);
		return $this->success($data);
	}
	
}

?>