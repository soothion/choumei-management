<?php

namespace App\Http\Controllers\Merchant;


use App\Http\Controllers\Controller;
use App\Merchant;
use Illuminate\Pagination\AbstractPaginator;
use DB;

class MerchantController extends Controller {
	/**
	 * @api {post} /merchant/index 1.商户列表
	 * @apiName list
	 * @apiGroup 
	 *
	 * @apiParam {String} phone 可选,电话号码
	 * @apiParam {String} name 可选,商户名
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
	 * @apiSuccess {Number} id id.
	 * @apiSuccess {String} sn 商户编号.
	 * @apiSuccess {String} name 用户姓名.
	 * @apiSuccess {String} contact 联系人.
	 * @apiSuccess {String} mobile 用户姓名.
	 * @apiSuccess {String} phone 电话.
	 * @apiSuccess {String} email 邮箱.
	 * @apiSuccess {String} addr 地址.
	 * @apiSuccess {String} foundingDate 商户成立时间.
	 * @apiSuccess {String} salonNum 拥有店铺数量.
	 * @apiSuccess {String} addTime 添加时间戳.
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "data": {
	 *	        "total": 51,
	 *	        "per_page": "1",
	 *	        "current_page": 1,
	 *	        "last_page": 51,
	 *	        "from": 1,
	 *	        "to": 1,
	 *	        "data": [
	 *	            {
	 *	                "id": 53,
	 *	                "sn": "0000900",
	 *	                "name": "s卡段商户",
	 *	                "contact": "汪先生",
	 *	                "mobile": "13458745236",
	 *	                "phone": "0755236566",
	 *	                "email": "",
	 *	                "addr": "",
	 *	                "foundingDate": 1432202590,
	 *	                "salonNum": 0,
	 *	                "addTime": 1432202951
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
		$query = Merchant::getQuery();

		//状态筛选
		if(isset($param['name'])&&$param['name'])
		{
			$keyword = '%'.$param['name'].'%';
			$query = $query->where('name','like',$keyword);
		}
		
		if(isset($param['phone'])&&$param['phone'])
		{
			$query = $query->where('phone','>=',$param['phone']);
		}

		$page = isset($param['page'])?max($param['page'],1):1;
		$page_size = isset($param['page_size'])?$param['page_size']:20;

		//手动设置页数
		AbstractPaginator::currentPageResolver(function() use ($page) {
		    return $page;
		});
		
		$fields = array('id', 'sn','name','contact','mobile','phone','email','addr','foundingDate','salonNum','addTime' );
		//分页
	    $result = $query->select($fields)->orderBy('addTime', 'desc')->paginate($page_size)->toArray();
	    unset($result['next_page_url']);
	    unset($result['prev_page_url']);
	    return $this->success($result);
	}
	
	/**
	 * 检测商户编号是否存在
	 * 
	 * */
	public function getCheckSn($sn,$id=0)
	{
		$query = Merchant::getQuery();
		$query->where('sn',$sn);
		if($id)
		{
			$query->where('id',$id);
		}
		return  $query->count();
	}
	
	/**
	 * @api {post} /merchant/save/ 2.添加或者修改商户
	 * @apiName 
	 * @apiGroup 
	 *
	 *@apiParam {Number} id 修改必填,商家ID（添加不填）.
	 *
	 * @apiParam {String} sn 商户编号.
	 * @apiParam {String} name 用户姓名.
	 * @apiParam {String} contact 联系人.
	 * @apiParam {String} mobile 用户姓名.
	 * @apiParam {String} phone 电话.
	 * @apiParam {String} email 邮箱.
	 * @apiParam {String} addr 地址.
	 * @apiParam {String} foundingDate 商户成立时间.
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
	 *		    "msg": "未授权访问"
	 *		}
	 */	
	public function save()
	{
		$param = $this->param;
		$param["id"] = isset($param["id"])?$param["id"]:0;
		
		$query = Merchant::getQuery();
		if(!$param["name"] || !$param["contact"] || !$param["mobile"]  || !$param["sn"])
		{
			return $this->error("参数错误");
		}
		
		if($param["id"])
		{
			$save["upTime"] = time();
			$setTows = $this->getCheckSn($param['sn'],$param["id"]);//检测商户编号
			if($setTows)
			{
				$rows = 0;
			}
			else
			{
				$rows = $this->getCheckSn($param['sn']);//检测商户编号
			}
		}
		else 
		{
			$save["addTime"] = time();
			$rows = $this->getCheckSn($param['sn']);//检测商户编号
		}

		if($rows > 0)
		{
			return $this->error('商户编号重复');
		}

		$save["name"] = trim($param["name"])?trim($param["name"]):"";
		$save["contact"] = trim($param["contact"])?trim($param["contact"]):"";
		$save["mobile"] = trim($param["mobile"])?trim($param["mobile"]):"";
		$save["phone"] = trim($param["phone"])?trim($param["phone"]):"";
		$save["email"] = trim($param["email"])?trim($param["email"]):"";
		$save["addr"] = trim($param["addr"])?trim($param["addr"]):"";
		$save["foundingDate"] = trim($param["foundingDate"])?trim($param["foundingDate"]):"";
		$save["sn"] = trim($param["sn"])?trim($param["sn"]):"";
		
		if($param["id"])
		{
			$status = $query->where('id',$param['id'])->update($save);
		}
		else
		{
			$status = $query->insert($save);
		}
		
		if($status)
		{
			//Event::fire('Merchant.create',array($Merchant));
			return $this->success();
		}	 
		else
		{
			return $this->error('商户更新失败');
		} 
			
	}
	
	/**
	 * @api {post} /merchant/del/ 3.删除商户
	 * @apiName 
	 * @apiGroup 
	 *
	 *@apiParam {Number} id 删除必填,商家ID.
	 *
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
	 *		    "msg": "商户删除失败"
	 *		}
	 */	
	public function del()
	{
		$param = $this->param;
		$query = Merchant::getQuery();
		
		$param["id"] = isset($param["id"])?$param["id"]:0;
		if(!$param["id"])
		{
			return $this->error('参数错误');
		}
		$save["status"] = 2;//1正常 2删除

		$status = $query->where('id',$param['id'])->update($save);

		if($status)
		{
			return $this->success();
		}	 
		else
		{
			return $this->error('商户删除失败');
		} 
		
	}
	
	/**
	 * @api {post} /merchant/checkMerchantSn/ 4.检测商家编号是否重复
	 * @apiName 
	 * @apiGroup 
	 *
	 *@apiParam {String} sn 必填商家编号.
	 *
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
	 *		    "msg": "店铺编号重复已经存在"
	 *		}
	 */	
	public function checkMerchantSn()
	{
		$param = $this->param;
		$sn = isset($param["sn"])?trim($param["sn"]):"";	

		if(!$sn)
		{
			return $this->error('参数错误');
		}

		$snNo = $this->getCheckSn($sn);//检测商铺编号
		if($snNo)
		{
			return $this->error('商户编号重复已经存在');
		}
		else 
		{
			return $this->success();
		}
	}
	
}

?>