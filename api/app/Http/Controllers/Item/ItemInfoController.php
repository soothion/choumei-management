<?php namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use DB;
use App\SalonItemType;
use App\SalonItem;
use App\Hairstylist;
use App\Addedservice;
use App\AddedserviceItemtype;
use App\AddedserviceSalon;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;
use App\SalonItemBuylimit;
use App\SalonItemFormatPrice;
class ItemInfoController extends Controller{

	/**
	 * @api {post} /itemInfo/index 1.店铺项目资料列表
	 * @apiName index
	 * @apiGroup  itemInfo
	 *
	 * @apiParam {Number} page 可选,页数.
	 * @apiParam {Number} page_size 可选,分页大小.
	 * 
	 * @apiSuccess {Number} salonid 店铺id.
	 * @apiSuccess {String} salonname 店铺名称.
	 * @apiSuccess {Number} generalNums 普通项目数量.
	 * @apiSuccess {Number} specialNums 限时特价数量.
	 * @apiSuccess {Number} wareroomNums 项目仓库数量.
	 * @apiSuccess {Number} hairstyNums 造型师数量.
	 *
	 * @apiSuccessExample Success-Response:
	 *{
	 *	    "result": 1,
	 *	    "token": "",
	 *	    "data": [
	 *	    {
     *           "salonid": 691,
     *           "salonname": "choumeitest_salon",
     *           "generalNums": 12,
     *           "specialNums": 43,
     *           "wareroomNums": 42,
     *           "hairstyNums": 36
     *       },
	 *			......
	 *	    ]
	 *	}
	 *
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
		$key = isset($param['key'])?intval($param['key']):0;
		$keyword = isset($param['keyword'])?trim($param['keyword']):'';
		$page = isset($param['page'])?max($param['page'],1):1;
		$page_size = isset($param['page_size'])?$param['page_size']:20;

		$result = SalonItem::getSalonItem($key,$keyword,$page,$page_size);
		unset($result['next_page_url']);
		unset($result['prev_page_url']);
		return $this->success($result);
	}	
	
	
	/**
	 * @api {post} /itemInfo/getItems 2.获取分类下项目名称
	 * @apiName getItems
	 * @apiGroup  itemInfo
	 *
	 * @apiParam {Number} typeid 必填,项目分类id.
	 * @apiParam {Number} salonid 必填,店铺id.
	 * 
	 * @apiSuccess {Number} itemid 项目id.
	 * @apiSuccess {String} itemname 项目名称.
	 *
	 * @apiSuccessExample Success-Response:
	 *{
	 *	    "result": 1,
	 *	    "token": "",
	 *	    "data": [
	 *	        {
	 *	            "itemid": 29888,
	 *	            "itemname": "总监快剪-洗剪吹造型"
	 *	        },
	 *	        {
	 *	            "itemid": 29890,
	 *	            "itemname": "普通快剪--洗剪吹造型"
	 *	        },
	 *	        {
	 *	            "itemid": 29892,
	 *	            "itemname": "洗剪吹造型"
	 *	        },
	 *			......
	 *	    ]
	 *	}
	 *
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 */
	public function getItemByTypeid()
	{
		$param = $this->param;
		$data['typeid'] = isset($param['typeid'])?intval($param['typeid']):0;
		$data['item_type'] = 1;//商品类型，1 默认在售，2 限时特价
		$data['salonid'] = isset($param['salonid'])?intval($param['salonid']):0;
		
		if(!$data['typeid'] || !$data['salonid'])
			 throw new ApiException('参数错误', ERROR::ITEM_ERROR);
		
		$items = SalonItem::select(['itemid','itemname'])->where($data)->get();
		return $this->success($items);
	}
	
	/**
	 * @api {post} /itemInfo/getAddedService 3.获取店铺项目分类下增值服务
	 * @apiName getAddedService
	 * @apiGroup  itemInfo
	 *
	 * @apiParam {Number} typeid 必填,项目分类id.
	 * @apiParam {Number} salonid 必填,店铺id.
	 *
	 * @apiSuccess {Number} sId 增值服务id.
	 * @apiSuccess {String} sName 增值服名称.
	 *
	 * @apiSuccessExample Success-Response:
	 *{
	 *	    "result": 1,
	 *	    "token": "",
	 *	    "data": [
	 *	        {
	 *	            "sId": 1,
	 *	            "sName": "进口面贴"
	 *	        },
	 *	        {
	 *	            "sId": 2,
	 *	            "sName": "进口烫染隔离霜"
	 *	        },
	 *			......
	 *	    ]
	 *	}
	 *
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 */
	public function getAddedService()
	{
		$param = $this->param;
		$itemTypeId = isset($param['typeid'])?intval($param['typeid']):0;
		$salonid = isset($param['salonid'])?intval($param['salonid']):0;
		
		if(!$salonid || !$itemTypeId)
			throw new ApiException('参数错误', ERROR::ITEM_ERROR);

		$where['salonId'] = $salonid;
		$where['itemTypeId'] = $itemTypeId;
		$sInfo = AddedserviceSalon::where($where)->first();
		if(!$sInfo)
			return $this->success();
		
		$iwhere['itemType']=$itemTypeId;
		$iInfo = AddedserviceItemtype::select(['service_detail'])->where($iwhere)->first();
		if(!$iInfo['service_detail'])
			return $this->success();
		
		$detail = $iInfo['service_detail'];
		$list = Addedservice::select(['sId','sName'])->whereRaw("sId in ({$detail})")->get();
		return $this->success($list);
	}
	
	/**
	 * @api {post} /itemInfo/create 4.新增项目
	 * @apiName create
	 * @apiGroup  itemInfo
	 *
	 * @apiParam {Number} salonid  必填,店铺id.
	 * @apiParam {Number} typeid 必填,项目分类id.
	 * @apiParam {string} useLimit 可选,消费限制（特价项目）.
	 * @apiParam {string} logo 必填,logo.
	 * @apiParam {string} desc 必填,服务详情.
	 * @apiParam {Number} itemType 必填,商品类型，1 默认在售，2 限时特价.
	 * @apiParam {string} itemname 必填,项目名称
	 * @apiParam {Number} fastGrade 可选,快剪等级 1普通快剪 2总监快剪.
	 * @apiParam {string} repertory 可选,日库存（兑换专用+特价项目）.
	 * @apiParam {string} expTimeInput 可选,项目有效期（Y-m-d） 无限制 不传或0.
	 * @apiParam {Number} totalRepInput 可选,项目总库存 无限制 不传或0.
	 * @apiParam {string} addedService[] 可选,增值服务(数组).
	 * 
	 * @apiParam {string} timeLimitInput 可选,单人限制购买数 无限制 不传或0.
	 * @apiParam {string} inviteLimit 可选,1 限推荐用户购买 无限制 不传或0.
	 * @apiParam {string} firstLimit 可选,1 限首次下单可购买  无限制 不传或0.
	 * 
	 * @apiParam {string} priceStyle 必选,1无规格2有规格.
	 * @apiParam {string} price 可选,原价.
	 * @apiParam {string} priceDis 可选,臭美价.
	 * @apiParam {string} priceGroup 可选,集团价.
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
	 *		    "msg": "参数错误"
	 *		}
	 */
	public function store()
	{
		$param = $this->param;
        $data['salonid'] = isset($param['salonid'])?intval($param['salonid']):0;
        $itemId	= isset($param['itemid'])?intval($param['itemid']):0;
        $data['typeid'] = isset($param['typeid'])?intval($param['typeid']):0;//分类id
		
        $data['useLimit'] = isset($param['useLimit'])?trim($param['useLimit']):'';//消费限制（特价项目）
        $data['logo'] = isset($param['logo'])?trim($param['logo']):'';
        $data['desc'] = isset($param['desc'])?trim($param['desc']):'';

        $data['item_type']      = isset($param['itemType'])?intval($param['itemType']):0;;//'商品类型，1 默认在售，2 限时特价'
//        if($data['item_type']==2 && empty($data['useLimit'])) $this->alert ('请完善 消费限制 信息');

        $data['itemname'] = isset($param['itemname'])?trim($param['itemname']):'';
        $data['itemname'] = preg_replace('/\s+/', '', $data['itemname']);
		$data['fastGrade']  = isset($param['fastGrade'])?intval($param['fastGrade']):0;//快剪等级
		$priceStyle = isset($param['priceStyle'])?intval($param['priceStyle']):0;//项目规格 选项
		if(!$data['salonid'] || !$data['typeid'] || !$data['logo'] || !$data['itemname'] || !$data['desc'] || !in_array($priceStyle, [1,2]))
			throw new ApiException('参数错误', ERROR::ITEM_ERROR);
		
		if($data['typeid'] == 8)//男士快剪检测 是否有对应的造型师 快剪等级
		{
			$flags = Hairstylist::checkHairerGrade($data['fastGrade'],$data['salonid']);
			if(!$flags)
				throw new ApiException('当前快剪等级下面无对应等级的造型师，请修改造型师界面中的快剪等级后再添加快剪项目！', ERROR::UNKNOWN_ERROR);
		}

		if($data['typeid'] == 6 || $data['item_type'] != 1 )//兑换专用  --日库存
			$data['repertory']  = isset($param['repertory'])?intval($param['repertory']):0;
		else 
			$data['repertory']  = 0;
		
        $data['add_time'] 	= time();
       // $data['norms_cat_id'] = intval($_POST['nomsCatId']);   模板id

        $expTimeInput = isset($param['expTimeInput'])?trim($param['expTimeInput']):0;//项目使用有效期
        if ($expTimeInput)
        {
        	$expTime = $expTimeInput.' 23:59:00';
        	$expTimeStamp = strtotime($expTime);
        	if ($expTimeStamp > time())
        		$data['exp_time'] = $expTimeStamp;
        	else 
        		throw new ApiException('项目有效期时间不正确！', ERROR::UNKNOWN_ERROR);
        } 
        else
        {
        	$data['exp_time'] = 0;
        }

        $totalRepInput = isset($param['totalRepInput'])?trim($param['totalRepInput']):0;//项目总库存
        if($totalRepInput > 0) 
        	$data['total_rep'] = $totalRepInput;
        else
        	$data['total_rep'] = 0;
        
        //增值服务
        $data['addserviceStr'] = '';
        $addedService = isset($param['addedService'])?trim($param['addedService']):'';
        if($addedService)
        	$data['addserviceStr']=implode(',',$addedService);
        
        if($itemId)
        {
        	
        }
        else 
        {
        	$timeLimitInput = isset($param['timeLimitInput'])?intval($param['timeLimitInput']):0;
        	//购买资格
        	$inviteLimit = isset($param['inviteLimit'])?intval($param['inviteLimit']):0;
        	$firstLimit = isset($param['firstLimit'])?intval($param['firstLimit']):0;
        	if ($timeLimitInput >= 0)
        		$buyLimit['limit_time'] = $timeLimitInput;
        	else
        		throw new ApiException('限制次数不正确！', ERROR::UNKNOWN_ERROR);
        	
        	$data['status'] 	= 1;	//1 上架   2下架   3删除
        	$data['uid'] 	= 1;//注意：以前是店铺账号id  现新管理后台默认 管理账号id
        	$data['up_time'] 	= time();
        	$data['add_time'] 	= time();
        	$salonItemId = SalonItem::insertGetId($data);
        	
        	//处理购买限制表 开始
        	$buyLimit['limit_invite'] = $inviteLimit?1:0;
        	$buyLimit['limit_first'] = $firstLimit?1:0;
        	$buyLimit['salon_item_id'] = $salonItemId;
        	$buyLimit['create_time'] = time();
        	$buyLimit['update_time'] = time();
        	SalonItemBuylimit::insertGetId($buyLimit);
        	//处理购买限制表 结束
        	
        	//无规格--价格处理
        	if($priceStyle == 1)
        	{
        		$price = isset($param['price'])?intval($param['price']):0;
        		$priceGroup = isset($param['priceGroup'])?intval($param['priceGroup']):0;
        		$priceDis = isset($param['priceDis'])?intval($param['priceDis']):0;
        		$priceData['dis_id'] = 0;
        		$priceData['discount'] = 0;
        		$priceData['price_dis'] = $priceDis;//臭美价
        		$priceData['price_group'] = $priceGroup;//集团价
        		$priceData['itemid'] = $salonItemId;
        		$priceData['salon_norms_id'] = 0;
        		$priceData['price'] = $price;//原价
        		$priceData['add_time']=time();
        		$row = SalonItemFormatPrice::insertGetId($priceData);
        	}
        	else 
        	{
        		
        	}      	
        	
        }
       return $this->success($row);
        
	}
	


}
?>