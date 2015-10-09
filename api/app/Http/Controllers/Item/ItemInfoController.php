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
use App\SalonItemFormats;
use App\SalonItemFormat;
use App\SalonNormsCat;
use App\SalonNorms;
use App\Salon;
class ItemInfoController extends Controller{

	//规格菜单英文标识 保持和前端一致
	private  $_typeArr = ['sex'=>'性别','hairstylist'=>'造型师','solution'=>'药水','longhair'=>'发长'];
	
	/**
	 * @api {post} /itemInfo/index 1.店铺项目资料列表
	 * @apiName index
	 * @apiGroup  itemInfo
	 * 
	 * @apiParam {Number} key 可选,1店铺 2商户.
	 * @apiParam {String} keyword 可选,关键字.
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
     *	    	    "salonid": 691,
     *	    	    "salonname": "choumeitest_salon",
     *	    	    "generalNums": 12,
     *	    	    "specialNums": 43,
     *	    	    "wareroomNums": 42,
     *	    	    "hairstyNums": 36
     *	    },
	 *			......
	 *	    ]
	 *}
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
	 * @apiParam {array}  addedService 可选,增值服务(数组).
	 * @apiParam {string} timingAdded 可选,定时上架（Y-m-d H:i:s）.
	 * @apiParam {string} timingShelves 可选,定时下架（Y-m-d H:i:s）.
	 * @apiParam {string} timeLimitInput 可选,单人限制购买数 无限制 不传或0.
	 * @apiParam {string} inviteLimit 可选,1 限推荐用户购买 无限制 不传或0.
	 * @apiParam {string} firstLimit 可选,1 限首次下单可购买  无限制 不传或0.
	 * 
	 * @apiParam {string} priceStyle 必选,1无规格2有规格.
	 * @apiParam {string} price 无规格必填,原价.
	 * @apiParam {string} priceDis 无规格必填,臭美价.
	 * @apiParam {string} priceGroup 可选,集团价.
	 * 
	 * @apiParam {string} normMenu 有规格必填（json字符串）,规格说明  ["sex","hairstylist","longhair","solution"]（sex 性别 hairstylist造型师 longhair发长 solution药水 ）.
	 * @apiParam {string} normarr 有规格必填（json字符串）,规格模板价格price 价格 priceDis 臭美价 priceGroup集团价
	 * [
	 *	    {
	 *	        "type": {
	 *	            "sex": "男",
	 *	            "hairstylist": "高级造型师",
	 *	            "longhair": "长发",
	 *	            "solution": "普通药水"
	 *	        },
	 *	        "price": "50",
	 *	        "priceDis": "40",
	 *	        "priceGroup": "38"
	 *	    },
	 *	    {
	 *	        "type": {
	 *	            "sex": "男",
	 *	            "hairstylist": "普通造型师",
	 *	            "longhair": "中发",
	 *	            "solution": "臭美药水"
	 *	        },
	 *	        "price": "80",
	 *	        "priceDis": "48",
	 *	        "priceGroup": "30"
	 *	    },
	 *		.........	
	 *	]
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
		$itemid = isset($param['itemid'])?intval($param['itemid']):null;
		$err_msg = [];
		$ret = self::filter($param, $err_msg);
		if(!$ret)
		{
		    throw new ApiException($err_msg['msg'],$err_msg['no']);
		}
		$data = self::compositeData($param);
		$res = self::upsert($param,$itemid);
		return $this->success($res);

	}
	
	/**
	 * @api {post} /itemInfo/update 5.修改项目
	 * @apiName update
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
	 * @apiParam {array}  addedService 可选,增值服务(数组).
	 * @apiParam {string} timingAdded 可选,定时上架（Y-m-d H:i:s）.
	 * @apiParam {string} timingShelves 可选,定时下架（Y-m-d H:i:s）.
	 * @apiParam {string} timeLimitInput 可选,单人限制购买数 无限制 不传或0.
	 * @apiParam {string} inviteLimit 可选,1 限推荐用户购买 无限制 不传或0.
	 * @apiParam {string} firstLimit 可选,1 限首次下单可购买  无限制 不传或0.
	 *
	 * @apiParam {string} priceStyle 必选,1无规格2有规格.
	 * @apiParam {string} price 无规格必填,原价.
	 * @apiParam {string} priceDis 无规格必填,臭美价.
	 * @apiParam {string} priceGroup 可选,集团价.
	 *
	 * @apiParam {string} normMenu 有规格必填（json字符串）,规格说明  ["sex","hairstylist","longhair","solution"]（sex 性别 hairstylist造型师 longhair发长 solution药水 ）.
	 * @apiParam {string} normarr 有规格必填（json字符串）,规格模板价格price 价格 priceDis 臭美价 priceGroup集团价
	 * [
	 *	    {
	 *	        "type": {
	 *	            "sex": "男",
	 *	            "hairstylist": "高级造型师",
	 *	            "longhair": "长发",
	 *	            "solution": "普通药水"
	 *	        },
	 *	        "price": "50",
	 *	        "priceDis": "40",
	 *	        "priceGroup": "38"
	 *	    },
	 *	    {
	 *	        "type": {
	 *	            "sex": "男",
	 *	            "hairstylist": "普通造型师",
	 *	            "longhair": "中发",
	 *	            "solution": "臭美药水"
	 *	        },
	 *	        "price": "80",
	 *	        "priceDis": "48",
	 *	        "priceGroup": "30"
	 *	    },
	 *		.........
	 *	]
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
	public function update()
	{
		$param = $this->param;
		return $this->save($param);
	}
	
	/**
	 * 添加修改控制器集中处理
	 * */
	public function save($param)
	{
		$data['salonid'] = $salonid = isset($param['salonid'])?intval($param['salonid']):0;
		$salonItemId	= isset($param['itemid'])?intval($param['itemid']):0;
		$data['typeid'] = isset($param['typeid'])?intval($param['typeid']):0;//分类id
		$data['useLimit'] = isset($param['useLimit'])?trim($param['useLimit']):'';//消费限制（特价项目）
		$data['logo'] = isset($param['logo'])?trim($param['logo']):'';
		$data['desc'] = isset($param['desc'])?trim($param['desc']):'';
		$data['item_type']      = isset($param['itemType'])?intval($param['itemType']):0;;//'商品类型，1 默认在售，2 限时特价'
		$data['itemname'] = isset($param['itemname'])?trim($param['itemname']):'';
		$data['itemname'] = preg_replace('/\s+/', '', $data['itemname']);
		$data['fastGrade']  = isset($param['fastGrade'])?intval($param['fastGrade']):0;//快剪等级
		$timingAdded  = isset($param['timingAdded'])?trim($param['timingAdded']):0;//定时上架
		$timingShelves  = isset($param['timingShelves'])?trim($param['timingShelves']):0;//定时下架
		
		$priceStyle = isset($param['priceStyle'])?intval($param['priceStyle']):0;//项目规格 选项
		if(!$data['salonid'] || !$data['typeid'] || !$data['logo'] || !$data['itemname'] || !$data['desc'] || !in_array($priceStyle, [1,2]))
			throw new ApiException('参数错误', ERROR::ITEM_ERROR);
		
		if($timingAdded && $timingShelves)
		{
			$data['timingAdded'] = strtotime($timingAdded);
			$data['timingShelves'] = strtotime($timingShelves);
			if($data['timingAdded'] < time() || $data['timingShelves'] < time())
				throw new ApiException('日期或时间设置错误，必须大于当前时间', ERROR::ITEM_ERROR);
		}
		elseif($salonItemId && $timingShelves)
		{
			$data['timingShelves'] = strtotime($timingShelves);
			if($data['timingShelves'] < time())
				throw new ApiException('日期或时间设置错误，必须大于当前时间', ERROR::ITEM_ERROR);
		}
		
		$itemInfo = [];
		if($salonItemId)
		{
			$itemInfo = SalonItem::where(['itemid'=>$salonItemId])->select(['itemid','salonid','sold','timingAdded','typeid','norms_cat_id','status'])->first();
			if(!$itemInfo)
				throw new ApiException('数据错误,项目id不存在！', ERROR::ITEM_DATA_ERROR);
			if($itemInfo->salonid != $salonid)
				throw new ApiException('salinid参数错误', ERROR::ITEM_ERROR);	
		}
		
		if($data['typeid'] == 8)//男士快剪检测 是否有对应的造型师 快剪等级
		{
			$flags = Hairstylist::checkHairerGrade($data['fastGrade'],$data['salonid']);
			if(!$flags)
				throw new ApiException('当前快剪等级下面无对应等级的造型师，请修改造型师界面中的快剪等级后再添加快剪项目！', ERROR::ITEM_GRADE_ERROR);
		}
		
		
		if($data['typeid'] == 6 || $data['item_type'] != 1 )//兑换专用  --日库存
			$data['repertory']  = isset($param['repertory'])?intval($param['repertory']):0;
		else
			$data['repertory']  = 0;
		
		$expTimeInput = isset($param['expTimeInput'])?trim($param['expTimeInput']):0;//项目使用有效期
		if ($expTimeInput)
		{
			$expTime = $expTimeInput.' 23:59:00';
			$expTimeStamp = strtotime($expTime);
			if ($expTimeStamp > time())
				$data['exp_time'] = $expTimeStamp;
			else
				throw new ApiException('项目有效期时间不正确！', ERROR::ITEM_EXPTIME_ERROR);
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
		$addedService = isset($param['addedService'])?$param['addedService']:'';
		if($addedService)
			$data['addserviceStr']=implode(',',$addedService);
		
		//购买资格
		$timeLimitInput = isset($param['timeLimitInput'])?intval($param['timeLimitInput']):0;
		$inviteLimit = isset($param['inviteLimit'])?intval($param['inviteLimit']):0;
		$firstLimit = isset($param['firstLimit'])?intval($param['firstLimit']):0;
		if ($timeLimitInput >= 0)
			$buyLimit['limit_time'] = $timeLimitInput;
		else
			throw new ApiException('限制次数不正确！', ERROR::ITEM_RESTRICT_ERROR);
		
		//处理购买限制表 开始
		$buyLimit['limit_invite'] = $inviteLimit?1:0;
		$buyLimit['limit_first'] = $firstLimit?1:0;
		$buyLimit['update_time'] = time();
		//处理购买限制表 结束
		
		DB::beginTransaction();
		if($salonItemId)
		{
			//判断库存设置
			if($data['total_rep'] > 0 && intval($itemInfo->sold) >= $data['total_rep'])
				throw new ApiException('项目总库存不正确！', ERROR::ITEM_TOTALREP_ERROR);
		
			$data['up_time'] 	= time();
			 
			//处理项目基础信息
			$row = SalonItem::where(['itemid'=>$salonItemId])->update($data);
			if(!$row)
			{
				DB::rollBack();
				throw new ApiException('项目更新失败！', ERROR::ITEM_TOTALREP_ERROR);
			}
		
			//处理购买限制表
			$existBuyLimitData =  SalonItemBuylimit::where(['salon_item_id'=>$salonItemId])->first();
			if($existBuyLimitData)
			{
				SalonItemBuylimit::where(['salon_item_id'=>$salonItemId])->update($buyLimit);
			}
			else
			{
				$buyLimit['create_time'] = time();
				$buyLimit['salon_item_id'] = $salonItemId;
				SalonItemBuylimit::insertGetId($buyLimit);
			}
		}
		else
		{
			//$data['status'] 	= 1;	//1 上架   2下架   3删除
			if(strtotime($timingAdded) > time())//上线时间 》 当前时间   下架状态
				$data['status'] 	= 2;
			else 
				$data['status'] 	= 1;
			$data['uid'] 	= 1;//注意：以前是店铺账号id  现新管理后台默认 管理账号id
			$data['add_time'] 	= time();
			$salonItemId = SalonItem::insertGetId($data);
			if(!$salonItemId)
			{
				DB::rollBack();
				throw new ApiException('项目更新失败！', ERROR::ITEM_TOTALREP_ERROR);
			}
			//处理购买限制表 开始
			$buyLimit['create_time'] = time();
			$buyLimit['salon_item_id'] = $salonItemId;
			SalonItemBuylimit::insertGetId($buyLimit);
			//处理购买限制表 结束
		}
		$priceNorm = SalonItemFormatPrice::where(['itemid'=>$salonItemId])->first();
		if($priceNorm)
			SalonItemFormatPrice::where(['itemid'=>$salonItemId])->delete();
		//无规格--价格处理
		if($priceStyle == 1)
		{
			$price = isset($param['price'])?intval($param['price']):0;
			$priceGroup = isset($param['priceGroup'])?intval($param['priceGroup']):0;
			$priceDis = isset($param['priceDis'])?intval($param['priceDis']):0;
			$priceData['dis_id'] = 0;
			$priceData['discount'] = 0;
			$priceData['price_dis'] = $priceDisArr[] = $priceDis;//臭美价
			$priceData['price_group'] = $priceGroupArr[] = $priceGroup;//集团价
			$priceData['itemid'] = $salonItemId;
			$priceData['salon_norms_id'] = 0;
			$priceData['price'] = $priceOriArr[] = $price;//原价
			$priceData['add_time']=time();
			$row = SalonItemFormatPrice::insertGetId($priceData);
			if(!$row)
			{
				DB::rollBack();
				throw new ApiException('项目更新失败！', ERROR::ITEM_TOTALREP_ERROR);
			}
		}
		else
		{
			$normMenu = isset($param['normMenu'])?json_decode($param['normMenu'],true):'';
			$normarr = isset($param['normarr'])?json_decode($param['normarr'],true):'';
			if(!$normarr || !$normMenu)
			{
				DB::rollBack();
				throw new ApiException('参数错误', ERROR::ITEM_ERROR);
			}
			foreach($normarr as $val)
			{
				if($val['price'] < $val['priceDis'] || $val['price'] < $val['priceGroup'] || $val['priceDis'] < $val['priceGroup'])
				{
					DB::rollBack();
					throw new ApiException('价格参数错误', ERROR::ITEM_ERROR);
				}
				 
			}
		
			foreach($normarr as $v)
			{
				foreach($v['type'] as $key=>$val)
				{
					$itemType[$key][]=$val;
				}
			}
			 
			$typeArr = $this->_typeArr;
			$ge = [];
			foreach($normMenu as  $key=>$val)
			{
				$gekey = SalonItemFormats::where(['salonid'=>0,'formats_name'=>$typeArr[$val]])->first();
				$formatsIdArr[$val] = $gekey->salon_item_formats_id;
			}
			$clearVal =[];
			foreach($itemType as $key=>$value)
			{
				foreach ($value as $kt=>$vt)
				{
					if(!in_array($vt, $clearVal))
					{
						$st = SalonItemFormat::where(['salonid'=>0,'format_name'=>$vt,'salon_item_formats_id'=>$formatsIdArr[$key]])->first();
						if($st)
							$norId = $st->salon_item_format_id;
						else
							$norId = SalonItemFormat::insertGetId(['salonid'=>0,'format_name'=>$vt,'salon_item_formats_id'=>$formatsIdArr[$key]]);
						$attribute[$vt] = $norId;//属性数组
						$clearVal[] = $vt;
					}
				}
			}
			$data = [
					'salonid' => $salonid,
					'norms_cat_name' => date('YmdHis'),//模板名称
					'add_time' => time()
			];
			 
			$nowid = SalonNormsCat::insertGetId($data);
			if(!$nowid)
			{
				DB::rollBack();
				throw new ApiException('项目更新失败！', ERROR::ITEM_TOTALREP_ERROR);
			}
			foreach($normarr as $ks=>$vs)
			{
				foreach($vs['type'] as $vtype)
				{
					$formatsId[$ks][] = $attribute[$vtype];
				}
				$data = [
						'salon_norms_cat_id' => $nowid,
						'salonid' => $salonid,
						'salon_item_format_id' =>implode(',',$formatsId[$ks]),
						'add_time' => time(),
				];
				$normarr[$ks]['salonNormsId'] = SalonNorms::insertGetId($data);
			}
		
			//规则价格入库
			foreach($normarr as $value)
			{
				$priceData['dis_id'] = 0;
				$priceData['discount'] = 0;
				$priceData['price_dis'] = $value['priceDis'];
				$priceData['price_group'] = $value['priceGroup'];
				$priceData['itemid'] = $salonItemId;
				$priceData['salon_norms_id'] = $value['salonNormsId'];
				$priceData['price'] = $value['price'];
				$priceData['add_time'] = time();
				$priceDisArr[] = $value['priceDis'];
				$priceGroupArr[] = $value['priceGroup'];
				$priceOriArr[] = $value['price'];
				$row = SalonItemFormatPrice::insertGetId($priceData);
				if(!$row)
				{
					DB::rollBack();
					break;
					throw new ApiException('项目更新失败！', ERROR::ITEM_TOTALREP_ERROR);
				}
			}
			$upData['norms_cat_id'] = $nowid;
		
		}
		//更新下项目信息
		$upData['minPrice'] = min($priceDisArr);
		$upData['maxPrice'] = max($priceDisArr);
		$upData['minPriceGroup'] = min($priceGroupArr);
		$upData['maxPriceGroup'] = max($priceGroupArr);
		$upData['minPriceOri'] = min($priceOriArr);
		$upData['maxPriceOri'] = max($priceOriArr);
		
		if( $upData['maxPrice'] >= 1000 )   //项目价格大于1000  调整店铺类型
			Salon::where(['salonid'=>$salonid])->update(['bountyType'=>4]);
		
		$row = SalonItem::where(['itemid'=>$salonItemId])->update($upData);
		if(!$row)
		{
			DB::rollBack();
			throw new ApiException('项目更新失败！', ERROR::ITEM_TOTALREP_ERROR);
		}
		else
		{
			if($itemInfo)
			{
				if($itemInfo->norms_cat_id)
					SalonNormsCat::delNorms($itemInfo->norms_cat_id);
			}
			
			DB::commit();
			return $this->success();
		}
		
	}
	
	public static function filter($param,&$err_msg)
	{
	    $itemid = isset($param['itemid'])?intval($param['itemid']):null;
	    $is_new = empty($itemid)?true:false;
	    if(!$param['salonid'] || !$$param['typeid'] || !$param['logo'] || !$param['itemname'] || !$param['desc'] || !in_array($param['priceStyle'], [1,2]))
	    {
	        $err_msg = ['msg'=>'参数错误','no'=>ERROR::ITEM_ERROR];
	        return false;
	    }
	        
	    
	    return true;
	}
	
	
	public static function compositeData($param)
	{
	    $res =  [
	      'salon_item'=>[],
	      'salon_item_buylimit'=>[],
	      'salon_norms_cat'=>[],
	      'salon_item_format'=>[],
	      'salon_item_format_price'=>[],
	      'salon_item_formats'=>[],
	      'salon_norms'=>[],
	    ];
	    
	    $now_time = time();
	    
	    $res['salon_item']['salonid'] = isset($param['salonid'])?intval($param['salonid']):0;
	    $res['salon_item']['typeid'] = isset($param['typeid'])?intval($param['typeid']):0;//分类id
	    $res['salon_item']['useLimit'] = isset($param['useLimit'])?trim($param['useLimit']):'';//消费限制（特价项目）
	    $res['salon_item']['logo'] = isset($param['logo'])?trim($param['logo']):'';
	    $res['salon_item']['desc'] = isset($param['desc'])?trim($param['desc']):'';
	    $res['salon_item']['item_type']      = isset($param['itemType'])?intval($param['itemType']):0;;//'商品类型，1 默认在售，2 限时特价'
	    $res['salon_item']['itemname'] = isset($param['itemname'])?trim($param['itemname']):'';
	    $res['salon_item']['itemname'] = str_replace(' ', '', $res['salon_item']['itemname']);
	    $res['salon_item']['fastGrade']  = isset($param['fastGrade'])?intval($param['fastGrade']):0;//快剪等级
	    //#@todo
	    
	
	    return $res;
	}
	
	public static function upsert($datas,$itemid=null)
	{
	    $datas= [
	      'salon_item'=>[],
	      'salon_item_buylimit'=>[],
	      'salon_norms_cat'=>[],
	      'salon_item_format'=>[],
	      'salon_item_format_price'=>[],
	      'salon_item_formats'=>[],
	      'salon_norms'=>[],
	    ];
	    
	    $salon_item_id = null;
	    $salon_buylimit_id = null;
	    $salon_norms_cat_id = null;
	    if(empty($itemid))
	    {
	        DB::beginTransaction();
	        $salon_item_id = SalonItem::insertGetId($datas['salon_item']);
	        $salon_buylimit_id = SalonItem::insertGetId($datas['salon_item_buylimit']);
	        DB::rollBack();
	    }
	    else 
	    {
	        $salon_item_id = $itemid;
	        DB::beginTransaction();
	        $salon_item_id = SalonItem::where('itemid',$salon_item_id)->update($datas['salon_item']);
	        $salon_buylimit_id = SalonItemBuylimit::where('salon_item_id',$salon_item_id)->update($datas['salon_item_buylimit']);
	        DB::rollBack();
	    }
	    
	    DB::beginTransaction();
	    $salon_norms_cat_id = SalonNormsCat::insertGetId($datas['salon_norms_cat']);
	    foreach($datas['salon_item_format'] as $format)
	    {
	        $tmp_salon_item_format_id = SalonItemFormat::insertGetId($format);
	    }
	    foreach($datas['salon_item_format_price'] as $price)
	    {
	        $tmp_salon_item_format_price_id = SalonItemFormatPrice::insertGetId($price);
	    }
	    foreach($datas['salon_item_formats'] as $formats)
	    {
	        $tmp_salon_item_formats_id = SalonItemFormats::insertGetId($formats);
	    }
	    foreach($datas['salon_norms'] as $norms)
	    {
	        $tmp_norms_id = SalonNorms::insertGetId($norms);
	    }
	    
	    //update relation id
	    //#@todo
	    
	    
	    DB::rollBack();
	}
	
	public static function add($salon_item,$salon_buylimit)
	{
	    
	}
	
}
?>