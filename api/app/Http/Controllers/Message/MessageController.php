<?php namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\StylistMsgConf;
use DB;
use Excel;
use App\Hairstylist;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;
use Illuminate\Support\Facades\Redis as Redis;
class MessageController extends Controller{
	
	
	/**
	 * @api {post}  /message/index 1.消息列表
	 * @apiName index
	 * @apiGroup Message
	 *
	 * @apiParam {String} title 可选,标题.
	 * @apiParam {String} status 可选,状态 0未上线 1 上线 2删除  查询全部时 不要传该参数.
	 * @apiParam {String} starttime 可选,开始时间.
	 * @apiParam {String} endtime 可选,结束日期.
	 * @apiParam {Number} page 可选,页数.
	 * @apiParam {Number} page_size 可选,分页大小.
	 * @apiParam {String} sort_key 排序的键,比如:onlinetime,addtime,id;
	 * @apiParam {String} sort_type 排序方式,DESC或者ASC;默认DESC
	 *
	 *
	 * @apiSuccess {Number} total 总数据量.
	 * @apiSuccess {Number} per_page 分页大小.
	 * @apiSuccess {Number} current_page 当前页面.
	 * @apiSuccess {Number} last_page 当前页面.
	 * @apiSuccess {Number} from 起始数据.
	 * @apiSuccess {Number} to 结束数据.
	 * @apiSuccess {Number} id ID.
	 * @apiSuccess {String} title 标题.
	 * @apiSuccess {Number} status 状态 0未上线 1 上线 2删除.
	 * @apiSuccess {Number} onlinetime 上线时间 时间戳.
	 *
	 *
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "data": {
	 *	        "total": 1,
	 *	        "per_page": 20,
	 *	        "current_page": 1,
	 *	        "last_page": 1,
	 *	        "from": 1,
	 *	        "to": 1,
	 *	        "data": [
	 *	            {
	 *	                "id": 1,
	 *	                "title": "嘉美专业烫染",
	 *	                "status": "0",
	 *	                "onlinetime": "1441606960",
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
		$where = [];
		$param = $this->param;
		$where['title'] = isset($param['title'])?$param['title']:'';
		$where['status'] = isset($param['status'])?$param['status']:'';
		$starttime = isset($param['starttime'])?$param['starttime']:'';
		$endtime = isset($param['endtime'])?$param['endtime']:'';
		if($starttime && $endtime)
		{
			$where['starttime'] = strtotime($starttime);
			$where['endtime'] = strtotime($endtime.' 23:59:59');
		}
	
		$sort_key = isset($param['sort_key'])?$param['sort_key']:'id';
		$sort_type = isset($param['sort_type'])?$param['sort_type']:'desc';
	
		$page = isset($param['page'])?max($param['page'],1):1;
		$page_size = isset($param['page_size'])?$param['page_size']:20;
		$list = StylistMsgConf::getList($where,$page,$page_size,$sort_key,$sort_type);
		unset($list['next_page_url']);
		unset($list['prev_page_url']);
		return $this->success($list);
	}
	
	/**
	 * @api {post} /message/create 2.新增消息
	 * @apiName create
	 * @apiGroup  Message
	 *
	 * @apiParam {Number} receive_type 必填,接收类型 1所有造型师 2指定造型师'.
	 * @apiParam {Number} receivers 选填,指定接收人 手机号码（数组）.
	 * @apiParam {String} title 必填,标题.
	 * @apiParam {String} description 必填,摘要.
	 * @apiParam {String} img 选填,列表展示图片.
	 * @apiParam {String} description 必填,摘要.
	 * @apiParam {String} url 必填,消息内容url.
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
	 *		    "msg": "参数错误"
	 *		}
	 */
	public function store()
	{
		return $this->dosave($this->param);
	}
	
	/**
	 * @api {post} /message/update 3.修改消息
	 * @apiName update
	 * @apiGroup  Message
	 *
	 * @apiParam {Number} receive_type 必填,接收类型 1所有造型师 2指定造型师'.
	 * @apiParam {Number} receivers 选填,指定接收人 手机号码（数组）.
	 * @apiParam {String} title 必填,标题.
	 * @apiParam {String} description 必填,摘要.
	 * @apiParam {String} img 选填,列表展示图片.
	 * @apiParam {String} description 必填,摘要.
	 * @apiParam {String} url 必填,消息内容url.
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
	 *		    "msg": "参数错误"
	 *		}
	 */
	public function update()
	{
		return $this->dosave($this->param);
	}
	
	
	private function dosave($param)
	{
		$save = [];
		$id = isset($param['id'])?$param['id']:0;
		$save['receive_type'] = isset($param['receive_type'])?intval($param['receive_type']):'';
		$receivers = isset($param['receivers'])?$param['receivers']:'';
		if($receivers)
		{
			$save['receivers'] = join(',',$receivers);
		}
		$save['title'] = isset($param['title'])?trim($param['title']):'';
		$save['description'] = isset($param['description'])?trim($param['description']):'';
		$save['img'] = isset($param['img'])?trim($param['img']):'';
		$save['url'] = isset($param['url'])?trim($param['url']):'';
		if(!$save['title'] || !$save['description'])
			throw new ApiException('参数错误', ERROR::PARAMETER_ERROR);
		if(($save['receive_type'] == 1 && $receivers) || ($save['receive_type'] == 2 && !$receivers))
			throw new ApiException('参数错误', ERROR::PARAMETER_ERROR);
		if(StylistMsgConf::dosave($save,$id))
		{
			return $this->success();
		}
		else
		{
			throw new ApiException('更新失败', ERROR::UPDATE_FAILED);
		}

	}
	
	
	/**
	 * @api {post} /message/checkPhone 4.检测手机号码
	 * @apiName checkPhone
	 * @apiGroup  Message
	 *
	 * @apiParam {Number} phone 必填,数组  手机号码.
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
	 *  @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "msg": "",
	 *	    "data": {//data 返回错误的手机号
	 *			"13526598665",
	 *			"13826598465",	
	 *	    }
	 *	}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "参数错误"
	 *		}
	 */
	public function checkPhone()
	{
		$param = $this->param;
		$phone = $param['phone'];
		
		$errorPhone = [];
		if($phone)
		{
			foreach($phone as $val)
			{
				$count = hairstylist::where('mobilephone', '=', $val)->count();
				if($count < 1)
				{
					$errorPhone[] = $val;
				}
			}
		}
		return $this->success($errorPhone);
	}
	
	
	/**
	 * @api {post} /message/destroy/ 5.删除消息
	 * @apiName destroy
	 * @apiGroup Message
	 *
	 * @apiParam {Number} id ID.
	 *
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
	 *		    "msg": "操作失败"
	 *		}
	 */
	public function destroy()
	{
		$param = $this->param;
		$id = $param['id'];
		$result = StylistMsgConf::select(['status'])->where("id","=",$id)->first();;
		if(!$result)
			throw new ApiException('未知Id', ERROR::MESSAGE_ID_IS_ERROR);
		$row = StylistMsgConf::doOperating($id,2);
		if($row)
			return $this->success();
		throw new ApiException('更新失败', ERROR::UPDATE_FAILED);
	}
	
	/**
	 * @api {post} /message/online/ 6.上线
	 * @apiName online
	 * @apiGroup Message
	 *
	 * @apiParam {Number} id ID.
	 *
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
	 *		    "msg": "操作失败"
	 *		}
	 */
	public function online()
	{
		$param = $this->param;
		$id = $param['id'];
		$result = StylistMsgConf::select(['status'])->where("id","=",$id)->where("status","=",0)->first();;
		if(!$result)
			throw new ApiException('未知Id', ERROR::MESSAGE_ID_IS_ERROR);
		$row = StylistMsgConf::doOperating($id,1);//1 上线
		if($row)
			return $this->success();
		throw new ApiException('上线失败', ERROR::UPDATE_FAILED);
	}
	
	/**
	 * @api {post} /message/getOne/ 7.查询单条信息
	 * @apiName getOne
	 * @apiGroup Message
	 *
	 * @apiParam {Number} id ID.
	 *
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
	 *		    "msg": "操作失败"
	 *		}
	 */
	public function getOne()
	{
		$param = $this->param;
		$id = $param['id'];
		$result = StylistMsgConf::getOnebyId($id);
		if(!$result)
			throw new ApiException('未知Id', ERROR::MESSAGE_ID_IS_ERROR);
		return $this->success($result);
	}
	
	/**
	 * @api {post} /message/addingPreview/ 8.添加预览信息
	 * @apiName addingPreview
	 * @apiGroup Message
	 *
	 * @apiParam {Number} receive_type 选填,接收类型 1所有造型师 2指定造型师'.
	 * @apiParam {Number} receivers 选填,指定接收人 手机号码（数组）.
	 * @apiParam {String} title 选填,标题.
	 * @apiParam {String} description 选填,摘要.
	 * @apiParam {String} img 选填,列表展示图片.
	 * @apiParam {String} description 选填,摘要.
	 * @apiParam {String} url 选填,消息内容url.
	 * @apiParam {String} content 选填,消息内容.
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
	 *		    "msg": "操作失败"
	 *		}
	 */
	public function addingPreview()
	{
		$param = $this->param;
		$key = time();
		Redis::hset('messageAddingPreview',$key,json_encode($param));
		Redis::EXPIREAT('receivables',strtotime(date('Y-m-d').'23:59:59'));//当天过期
		return $this->success($key);
	}
	
	/**
	 * @api {post} /message/getPreview/ 9.预览信息
	 * @apiName getPreview
	 * @apiGroup Message
	 *
	 * @apiParam {Number} key 必填,信息key.
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
	 *		    "msg": "操作失败"
	 *		}
	 */
	public function getPreview()
	{
		$param = $this->param;
		$key = $param['key'];
		if(!$key)
		{
			throw new ApiException('参数错误', ERROR::PARAMETER_ERROR);
		}
		$value = Redis::hget('messageAddingPreview',$key);
		return $this->success(json_decode($value,true));
	}
	
	
}
?>