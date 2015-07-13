<?php namespace App\Http\Controllers\Merchant;


use App\Http\Controllers\Controller;
use App\Salon;
use Illuminate\Pagination\AbstractPaginator;
use DB;
use App\Merchant;
use App\SalonInfo;


class SalonController extends Controller {
		
    private $addFields = array(
			    		"merchantId",
			            "salonname",
						"addr",
						"addrlati",
						"addrlong",
						"zone",
   					    "district",
						"shopType",
						"contractTime",
						"contractPeriod",
			            "sn",
						"bcontacts",
						"tel",
						"phone",
						"corporateName",
						"corporateTel",
						"businessId",
						"bankName",
						"beneficiary",
						"bankCard",
						"branchName",
						"accountType",
    		);
	/**
	* @api {post} /salon/index 1.店铺列表
	* @apiName index
	* @apiGroup salon
	*
	* @apiParam {Number} shopType 可选,店铺类型 
	* @apiParam {Number} district 可选,区域
	* @apiParam {Number} zone 可选,所属商圈
	* @apiParam {String} salonname 可选,店名
	* @apiParam {String} businessName 可选,业务代表
	* @apiParam {Number} sort_key 可选,排序字段 shopType 店铺类型  salestatus 状态.
	* @apiParam {Number} sort_type 可选,排序 DESC倒序 ASC升序.
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
	* @apiSuccess {Number} salonid 店铺Id.
	* @apiSuccess {String} salonname 店铺名称.
	* @apiSuccess {String} shopType 店铺类型 店铺类型  1预付款店 2投资店 3金字塔店.
	* @apiSuccess {String} zone 商圈.
	* @apiSuccess {String} district 行政区域.
	* @apiSuccess {String} salestatus 状态 0终止合作 1正常合作.
	* @apiSuccess {String} businessId 业务ID.
	* @apiSuccess {String} sn 地址.
	* @apiSuccess {String} add_time 添加时间(10位时间戳).
	* @apiSuccess {String} name 商户名.
	* @apiSuccess {String} merchantId 商户ID.
	* @apiSuccess {String} businessName 业务代表名.
	* @apiSuccess {String} zoneName 商圈名.
	* @apiSuccess {String} districtName 区域名称.
	* @apiSuccess {String} citiesName 市名称.
	* @apiSuccess {String} citiesId 市Id.
	* @apiSuccess {String} provinceName 省名称.
	* @apiSuccess {String} provinceId 省Id
	* 
	* 
	* @apiSuccessExample Success-Response:
	*	{
	*	    "result": 1,
	*	    "data": {
	*	        "total": 782,
	*	        "per_page": "1",
	*	        "current_page": 1,
	*	        "last_page": 782,
	*	        "from": 1,
	*	        "to": 1,
	*	        "data": [
	*	            {
	*	                "salonid": 796,
	*	                "salonname": "亮丽人生",
	*	                "shopType": 3,
	*	                "zone": 0,
	*	                "district": 0,
	*	                "salestatus": 1,
	*	                "businessId": 4,
	*	                "sn": "0002701",
	*	                "add_time": 1432017651,
	*	                "name": "美好年代1",
	*	                "merchantId": 27,
	*	                "businessName": "",
	*	                "zoneName": "",
	*	                "districtName": "",
	*	                "citiesName": "",
	*	                "citiesId": "",
	*	                "provinceName": "",
	*	                "provinceId": ""
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
		$where = "";
		
		$param = $this->param;
		$shopType = isset($param["shopType"])?intval($param["shopType"]):0;//店铺类型
		$zone = isset($param["zone"])?$param["zone"]:0;//所属商圈
		$salonname = isset($param["salonname"])?urldecode($param["salonname"]):"";//店名
		$district = isset($param["district"])?$param["district"]:0;//区域
		$businessName = isset($param["businessName"])?urldecode($param["businessName"]):"";//业务代表
		$sort_key = isset($param["sort_key"])?$param["sort_key"]:"add_time";
    	$sort_type = isset($param["sort_type"])?$param["sort_type"]:"desc";
		
		if($shopType)
    	{
    		$where["shopType"] = $shopType;
    	}
		if($district)
    	{
    		$where["district"] = $district;
    	}
		if($zone)
    	{
    		$where["zone"] = $zone;
    	}
		if($salonname)
    	{
    		$where["salonname"] = $salonname;
    	}
    	if($businessName)
    	{
    		$userBus = DB::table('business_staff')->where('businessName',"like", "%".$businessName."%")->first();
    		if($userBus)
    		{
    			$where["businessId"] = $userBus->id;
    		}
    		else 
    		{
    			$where["businessId"] = 1000000000;//默认查找不到信息
    		}
    	}
		$page = isset($param['page'])?max($param['page'],1):1;
		$page_size = isset($param['page_size'])?$param['page_size']:20;
		$list = Salon::getSalonList($where,$page,$page_size,$sort_key,$sort_type);
	    return $this->success($list);
	}
	
	/**
	* @api {post} /salon/save 2.店铺添加
	* @apiName save
	* @apiGroup salon
	*
	* @apiParam {Number} merchantId 必填,商户Id.
	* @apiParam {Number} sn 必填,店铺编号.
	* @apiParam {String} salonname 必填,店名.
	* @apiParam {Number} district 必填,行政地区 . 
	* @apiParam {String} addr 必填,详细街道信息.
	* @apiParam {Number} addrlati 必填,地理坐标纬度.
	* @apiParam {Number} addrlong 必填,地理坐标经度.
	* @apiParam {Number} zone 必填,所属商圈 .
	* @apiParam {Number} shopType 必填,店铺类型  1预付款店 2投资店 3金字塔店.
	* @apiParam {String} contractTime 可选,合同日期  Y-m-d.
	* @apiParam {String} contractPeriod 可选,合同期限 y_m.
	* @apiParam {String} bargainno 可选,合同编号.
	* @apiParam {String} bcontacts 可选,联系人.
	* @apiParam {String} tel 必填,联系电话.
	* @apiParam {String} phone 必填,店铺座机.
	* @apiParam {String} corporateName 必填,法人代表.
	* @apiParam {String} corporateTel 必填,法人电话.
	* @apiParam {Number} businessId 必填,业务代表Id.
	* @apiParam {String} bankName 必填,银行名称.
	* @apiParam {String} beneficiary 必填,收款人.
	* @apiParam {String} bankCard 必填,银行卡号.
	* @apiParam {String} branchName 必填,支行名称.
	* @apiParam {Number} accountType 必填,帐户类型 1. 对公帐户 2.对私帐户.
	* @apiParam {String} salonArea 可选,店铺面积.
	* @apiParam {String} dressingNums 可选,镜台数量.
	* @apiParam {Number} staffNums 可选,员工总人数.
	* @apiParam {Number} stylistNums 可选,发型师人数.
	* @apiParam {String} monthlySales 可选,店铺平均月销售额.
	* @apiParam {String} totalSales 可选,年销售总额.
	* @apiParam {String} price 可选,本店客单价.
	* @apiParam {String} payScale 可选,充值卡占月销售额.
	* @apiParam {String} payMoney 可选,销售最多的充值卡金额.
	* @apiParam {String} payMoneyScale 可选,销售最多的充值卡折扣.
	* @apiParam {String} payCountScale 可选,占全部充值总额.
	* @apiParam {String} cashScale 可选,每月非充值卡现金占销售额.
	* @apiParam {String} blowScale 可选,洗剪吹占销售额.
	* @apiParam {String} hdScale 可选,烫染占销售额.
	* @apiParam {String} platformName 可选,O2O平台合作.
	* @apiParam {String} platformScale 可选,合作O2O销售额占比.
	* @apiParam {String} receptionNums 可选,本店正常工作时间每日最多可接待人次理论数.
	* @apiParam {String} receptionMons 可选,均实际每月接待.
	* @apiParam {String} setupTime 可选,店铺成立时间 Y-m-d.
	* @apiParam {String} hotdyeScale 可选,店铺租金.
	* @apiParam {String} lastValidity 可选,店铺租赁合同剩余有效期.
	* @apiParam {String} salonType 可选,店铺类型 1纯社区店 2社区商圈店 3商圈店 4商场店 5工作室（写字楼)）,多选  1_3  下划线拼接.
	* @apiParam {String} contractPicUrl 可选,合同图片 json数组.
	* @apiParam {String} licensePicUrl 可选,营业执照 json数组.
	* @apiParam {String} corporatePicUrl 可选,法人执照 json数组.
	* @apiDescription 合同图片 营业执照 法人执照 demo
	*	[
	*		{
	*			"img": "http://choumei2.test.com/merchant/index.jpg",    //大图
	*			"thumbimg": "http://choumei2.test.com/sindex.jpg"       //缩略图
	*		},
	*		{
	*			"img": "http://choumei2.test.com/merchant/index.jpg",
	*			"thumbimg": "http://choumei2.test.com/sindex.jpg"
	*		},
	*		{
	*			"img": "http://choumei2.test.com/merchant/index.jpg",
	*			"thumbimg": "http://choumei2.test.com/sindex.jpg"
	*		}
	*	]
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
	*		    "msg": "店铺更新失败"
	*		}
	*/
	public function save()
	{
		return $this->dosave($this->param);
	}
	
		/**
	* @api {post} /salon/update 3.店铺更新 
	* @apiName update
	* @apiGroup salon
	*
	* @apiParam {Number} merchantId 必填,商户Id
	* @apiParam {Number} salonid 必填,店铺id .
	* @apiParam {Number} sn 必填,店铺编号.
	* @apiParam {String} salonname 必填,店名.
	* @apiParam {Number} district 必填,行政地区 . 
	* @apiParam {String} addr 必填,详细街道信息.
	* @apiParam {Number} addrlati 必填,地理坐标纬度.
	* @apiParam {Number} addrlong 必填,地理坐标经度.
	* @apiParam {Number} zone 必填,所属商圈 .
	* @apiParam {Number} shopType 必填,店铺类型  1预付款店 2投资店 3金字塔店.
	* @apiParam {String} contractTime 可选,合同日期  Y-m-d.
	* @apiParam {String} contractPeriod 可选,合同期限 y_m.
	* @apiParam {String} bargainno 可选,合同编号.
	* @apiParam {String} bcontacts 可选,联系人.
	* @apiParam {String} tel 必填,联系电话.
	* @apiParam {String} phone 必填,店铺座机.
	* @apiParam {String} corporateName 必填,法人代表.
	* @apiParam {String} corporateTel 必填,法人电话.
	* @apiParam {Number} businessId 必填,业务代表Id.
	* @apiParam {String} bankName 必填,银行名称.
	* @apiParam {String} beneficiary 必填,收款人.
	* @apiParam {String} bankCard 必填,银行卡号.
	* @apiParam {String} branchName 必填,支行名称.
	* @apiParam {Number} accountType 必填,帐户类型 1. 对公帐户 2.对私帐户.
	* @apiParam {String} salonArea 可选,店铺面积.
	* @apiParam {String} dressingNums 可选,镜台数量.
	* @apiParam {Number} staffNums 可选,员工总人数.
	* @apiParam {Number} stylistNums 可选,发型师人数.
	* @apiParam {String} monthlySales 可选,店铺平均月销售额.
	* @apiParam {String} totalSales 可选,年销售总额.
	* @apiParam {String} price 可选,本店客单价.
	* @apiParam {String} payScale 可选,充值卡占月销售额.
	* @apiParam {String} payMoney 可选,销售最多的充值卡金额.
	* @apiParam {String} payMoneyScale 可选,销售最多的充值卡折扣.
	* @apiParam {String} payCountScale 可选,占全部充值总额.
	* @apiParam {String} cashScale 可选,每月非充值卡现金占销售额.
	* @apiParam {String} blowScale 可选,洗剪吹占销售额.
	* @apiParam {String} hdScale 可选,烫染占销售额.
	* @apiParam {String} platformName 可选,O2O平台合作.
	* @apiParam {String} platformScale 可选,合作O2O销售额占比.
	* @apiParam {String} receptionNums 可选,本店正常工作时间每日最多可接待人次理论数.
	* @apiParam {String} receptionMons 可选,均实际每月接待.
	* @apiParam {String} setupTime 可选,店铺成立时间 Y-m-d.
	* @apiParam {String} hotdyeScale 可选,店铺租金.
	* @apiParam {String} lastValidity 可选,店铺租赁合同剩余有效期.
	* @apiParam {String} salonType 可选,店铺类型 1纯社区店 2社区商圈店 3商圈店 4商场店 5工作室（写字楼)）,多选  1_3  下划线拼接.
	* @apiParam {String} contractPicUrl 可选,合同图片 json数组.
	* @apiParam {String} licensePicUrl 可选,营业执照 json数组.
	* @apiParam {String} corporatePicUrl 可选,法人执照 json数组.
	* @apiDescription 合同图片 营业执照 法人执照 demo
	*	[
	*		{
	*			"img": "http://choumei2.test.com/merchant/index.jpg",    //大图
	*			"thumbimg": "http://choumei2.test.com/sindex.jpg"       //缩略图
	*		},
	*		{
	*			"img": "http://choumei2.test.com/merchant/index.jpg",
	*			"thumbimg": "http://choumei2.test.com/sindex.jpg"
	*		},
	*		{
	*			"img": "http://choumei2.test.com/merchant/index.jpg",
	*			"thumbimg": "http://choumei2.test.com/sindex.jpg"
	*		}
	*	]
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
	*		    "msg": "店铺更新失败"
	*		}
	*/
	public function update()
	{
		return $this->dosave($this->param);
	}
	
	/**
	 * 店铺添加 修改操作方法
	 * 
	 * */
	private function dosave($param)
	{
		//$param = $this->param;
		$flag = 0;
		
		$data["merchantId"] = isset($param["merchantId"])?intval($param["merchantId"]):0;//商户id
		$data["salonid"] = isset($param["salonid"])?intval($param["salonid"]):0;//店铺id
		
		//商铺基本信息
		$data["sn"] = isset($param["sn"])?trim($param["sn"]):"";//店铺编号
		$data["salonname"] = isset($param["salonname"])?trim($param["salonname"]):"";//店铺名称
		$data["district"] = isset($param["district"])?trim($param["district"]):"";//行政地区  
		$data["addr"] = isset($param["addr"])?trim($param["addr"]):"";//详细街道信息
		
		$data["addrlati"] = isset($param["addrlati"])?trim($param["addrlati"]):"";//地理坐标纬度   
		$data["addrlong"] = isset($param["addrlong"])?trim($param["addrlong"]):"";//地理坐标经度
		
		$data["zone"] = isset($param["zone"])?trim($param["zone"]):"";//所属商圈 - 位置地区
		$data["shopType"] = isset($param["shopType"])?intval($param["shopType"]):0;//店铺类型
		$data["contractTime"] = isset($param["contractTime"])?strtotime($param["contractTime"]):"";//合同日期
		$data["contractPeriod"] = isset($param["contractPeriod"])?trim($param["contractPeriod"]):"";//合同期限
		
		$data["bargainno"] = isset($param["bargainno"])?trim($param["bargainno"]):"";//合同编号
		$data["bcontacts"] = isset($param["bcontacts"])?trim($param["bcontacts"]):"";//联系人
		$data["tel"] = isset($param["tel"])?trim($param["tel"]):"";//联系电话
		$data["phone"] = isset($param["phone"])?trim($param["phone"]):"";//店铺座机
		$data["corporateName"] = isset($param["corporateName"])?trim($param["corporateName"]):"";//法人代表
		$data["corporateTel"] = isset($param["corporateTel"])?trim($param["corporateTel"]):"";//法人电话
		$data["businessId"] = isset($param["businessId"])?trim($param["businessId"]):"";//业务代表ID
		
		//商铺其他信息
		$dataInfo["bankName"] = isset($param["bankName"])?trim($param["bankName"]):"";//银行名称
		$dataInfo["beneficiary"] = isset($param["beneficiary"])?trim($param["beneficiary"]):"";//收款人
		$dataInfo["bankCard"] = isset($param["bankCard"])?trim($param["bankCard"]):"";//银行卡号
		$dataInfo["branchName"] = isset($param["branchName"])?trim($param["branchName"]):"";//支行名称
		$dataInfo["accountType"] = isset($param["accountType"])?trim($param["accountType"]):"";//帐户类型
		
		$dataInfo["salonArea"] = isset($param["salonArea"])?trim($param["salonArea"]):"";//店铺面积
		$dataInfo["dressingNums"] = isset($param["dressingNums"])?trim($param["dressingNums"]):"";//镜台数量
		$dataInfo["staffNums"] = isset($param["staffNums"])?trim($param["staffNums"]):"";//员工总人数
		$dataInfo["stylistNums"] = isset($param["stylistNums"])?trim($param["stylistNums"]):"";//发型师人数
		$dataInfo["monthlySales"] = isset($param["monthlySales"])?trim($param["monthlySales"]):"";//店铺平均月销售额
		$dataInfo["totalSales"] = isset($param["totalSales"])?trim($param["totalSales"]):"";//年销售总额
		$dataInfo["price"] = isset($param["price"])?trim($param["price"]):"";//本店客单价
		$dataInfo["payScale"] = isset($param["payScale"])?trim($param["payScale"]):"";//充值卡占月销售额
		$dataInfo["payMoney"] = isset($param["payMoney"])?trim($param["payMoney"]):"";//销售最多的充值卡金额
		$dataInfo["payMoneyScale"] = isset($param["payMoneyScale"])?trim($param["payMoneyScale"]):"";//销售最多的充值卡折扣
		$dataInfo["payCountScale"] = isset($param["payCountScale"])?trim($param["payCountScale"]):"";//占全部充值总额
		$dataInfo["cashScale"] = isset($param["cashScale"])?trim($param["cashScale"]):"";//每月非充值卡现金占销售额
		$dataInfo["blowScale"] = isset($param["blowScale"])?trim($param["blowScale"]):"";//洗剪吹占销售额
		$dataInfo["hdScale"] = isset($param["hdScale"])?trim($param["hdScale"]):"";//烫染占销售额
		$dataInfo["platformName"] = isset($param["platformName"])?trim($param["platformName"]):"";//O2O平台合作
		$dataInfo["platformScale"] = isset($param["platformScale"])?trim($param["platformScale"]):"";//合作O2O销售额占比
		$dataInfo["receptionNums"] = isset($param["receptionNums"])?trim($param["receptionNums"]):"";//本店正常工作时间每日最多可接待人次理论数
		$dataInfo["receptionMons"] = isset($param["receptionMons"])?trim($param["receptionMons"]):"";//均实际每月接待
		$dataInfo["setupTime"] = isset($param["setupTime"])?strtotime($param["setupTime"]):"";//店铺成立时间
		$dataInfo["hotdyeScale"] = isset($param["hotdyeScale"])?trim($param["hotdyeScale"]):"";//店铺租金
		$dataInfo["lastValidity"] = isset($param["lastValidity"])?trim($param["lastValidity"]):"";//店铺租赁合同剩余有效期
		$dataInfo["salonType"] = isset($param["salonType"])?trim($param["salonType"]):"";//店铺类型
		
		$dataInfo["contractPicUrl"] = isset($param["contractPicUrl"])?trim($param["contractPicUrl"]):"";//合同图片
		$dataInfo["licensePicUrl"] = isset($param["licensePicUrl"])?trim($param["licensePicUrl"]):"";//营业执照
		$dataInfo["corporatePicUrl"] = isset($param["corporatePicUrl"])?trim($param["corporatePicUrl"]):"";//法人执照
		if($dataInfo["contractPicUrl"] == "[]"){$dataInfo["contractPicUrl"] = "";} //排除[]
		if($dataInfo["licensePicUrl"] == "[]"){$dataInfo["licensePicUrl"] = "";}
		if($dataInfo["corporatePicUrl"] == "[]"){$dataInfo["corporatePicUrl"] = "";}
		
		//参数检测
		$postData = array_merge($data,$dataInfo);
		$retMissing = "";
		foreach ($this->addFields as $val)
		{
			 if(!$postData[$val])
			 {
			 	 $retMissing .= $val."-";
			 }
			 
		}
		if($retMissing)
		{
			return $this->error("缺失参数".$retMissing);
		}
		$merchantQuery = Merchant::getQuery();
		$salonQuery = Salon::getQuery();
		
		
		$merchantData = $merchantQuery->where(array("id"=>$data["merchantId"],"status"=>1))->get();//商户id 检测
		if(!$merchantData)
		{
			return $this->error("商户id有误");
		}
		
		if($data["salonid"])
		{
			$whereInfo["salonid"] = $data["salonid"];
			$where["salonid"] = $data["salonid"];
			$dataInfo["upTime"] = time();
			
			$ordRs = $salonQuery->where($whereInfo)->select(array("sn"))->get();
			
			if(!$ordRs)
			{
				return $this->error("店铺数据不存在，id错误");
			}
			foreach ($ordRs as $v)
			{
				if($v->sn)
				{
					$oldSn = $v->sn;
				}
			}
			if($oldSn != $data["sn"])
			{
				$flag = 1;
			}
		}
		else 
		{
			$where = '';
			$whereInfo = '';
			$flag = 1;
			$data["add_time"] = time();
			$dataInfo["addTime"] = time();
		}
		
		
		
		if($flag == 1)
		{
			$snNo = $this->getCheckSn($data["sn"]);
			if($snNo)
			{
				return $this->error("店铺编号重复已经存在");
			}
		}
		
		$row = $this->doadd($data,$dataInfo,$where,$whereInfo);
		if($row)
		{
			return $this->success();
		}
		else
		{
			return $this->error("店铺更新失败");
		}
	
		
	}
	
	
	/**
	* @api {post} /salon/getSalon 4.获取店铺详情
	* @apiName getSalon
	* @apiGroup salon
	*
	* @apiParam {Number} salonid 必填,店铺id.

	* @apiSuccess {Number} sn 店铺编号.
	* @apiSuccess {Number} salestatus 状态 0终止合作 1正常合作.
	* @apiSuccess {String} salonname 店铺名.
	* @apiSuccess {Number} district 行政地区 . 
	* @apiSuccess {String} addr 详细街道信息.
	* @apiSuccess {Number} addrlati 地理坐标纬度.
	* @apiSuccess {Number} addrlong 地理坐标经度.
	* @apiSuccess {Number} zone 所属商圈 .
	* @apiSuccess {Number} shopType 店铺类型  1预付款店 2投资店 3金字塔店.
	* @apiSuccess {String} contractTime 合同日期   时间戳.
	* @apiSuccess {String} contractPeriod 合同期限 y_m.
	* @apiSuccess {String} bargainno 合同编号.
	* @apiSuccess {String} bcontacts 联系人.
	* @apiSuccess {String} tel 联系电话.
	* @apiSuccess {String} phone 店铺座机.
	* @apiSuccess {String} corporateName 法人代表.
	* @apiSuccess {String} corporateTel 法人电话.
	* @apiSuccess {Number} businessId 业务代表ID.
	* @apiSuccess {String} bankName 银行名称.
	* @apiSuccess {String} beneficiary 收款人.
	* @apiSuccess {String} bankCard 银行卡号.
	* @apiSuccess {String} branchName 支行名称.
	* @apiSuccess {Number} accountType 帐户类型 1. 对公帐户 2.对私帐户.
	* @apiSuccess {String} salonArea 店铺面积.
	* @apiSuccess {String} dressingNums 镜台数量.
	* @apiSuccess {Number} staffNums 员工总人数.
	* @apiSuccess {Number} stylistNums 发型师人数.
	* @apiSuccess {String} monthlySales 店铺平均月销售额.
	* @apiSuccess {String} totalSales 年销售总额.
	* @apiSuccess {String} price 本店客单价.
	* @apiSuccess {String} payScale 充值卡占月销售额.
	* @apiSuccess {String} payMoney 销售最多的充值卡金额.
	* @apiSuccess {String} payMoneyScale 销售最多的充值卡折扣.
	* @apiSuccess {String} payCountScale 占全部充值总额.
	* @apiSuccess {String} cashScale 每月非充值卡现金占销售额.
	* @apiSuccess {String} blowScale 洗剪吹占销售额.
	* @apiSuccess {String} hdScale 烫染占销售额.
	* @apiSuccess {String} platformName O2O平台合作.
	* @apiSuccess {String} platformScale 合作O2O销售额占比.
	* @apiSuccess {String} receptionNums 本店正常工作时间每日最多可接待人次理论数.
	* @apiSuccess {String} receptionMons 均实际每月接待.
	* @apiSuccess {String} setupTime 店铺成立时间 (时间戳).
	* @apiSuccess {String} hotdyeScale 店铺租金.
	* @apiSuccess {String} lastValidity 店铺租赁合同剩余有效期.
	* @apiSuccess {String} salonType 店铺类型 1纯社区店 2社区商圈店 3商圈店 4商场店 5工作室（写字楼)）,多选  1_3  下划线拼接.
	* @apiSuccess {String} contractPicUrl 合同图片 json数组.
	* @apiSuccess {String} licensePicUrl 营业执照 json数组.
	* @apiSuccess {String} corporatePicUrl 法人执照 json数组.
	* @apiSuccess {String} zoneName 商圈名.
	* @apiSuccess {String} districtName 区域名称.
	* @apiSuccess {String} citiesName 市名称.
	* @apiSuccess {String} citiesId 市Id.
	* @apiSuccess {String} provinceName 省名称.
	* @apiSuccess {String} provinceId 省Id
	* 
	*/
	public function getSalon()
	{
		$param = $this->param;
		$salonid = isset($param["salonid"])?intval($param["salonid"]):0;//店铺id
		if(!$salonid)
		{
			return $this->error("参数错误");
		}
		$salonList = Salon::getSalon($salonid);
		
		return $this->success($salonList);
	}
	

	
	/**
	 * 添加修改操作
	 * 
	 * */
	private  function doadd($data,$dataInfo,$where='',$whereInfo='')
	{

		DB::beginTransaction();
		if($where)//修改
		{
			Salon::where($where)->update($data);
			if(!SalonInfo::where($whereInfo)->get())
			{
				DB::table('salon_info')->insertGetId(array("salonid"=>$whereInfo["salonid"]));
			}
			$affectid = SalonInfo::where($where)->update($dataInfo);
		}
		else //添加
		{
			$salonId = DB::table('salon')->insertGetId($data);
			if($salonId)
			{
					$dataInfo["salonid"] = $salonId;
					$affectid = DB::table('salon_info')->insertGetId($dataInfo);
					DB::table('merchant')->where("id","=",$data["merchantId"])->increment('salonNum',1);//店铺数量加1
			}
		}
		
		if($affectid)
		{
			DB::commit();
		}
		else 
		{
			DB::rollBack();  
		}
		return $affectid;

	}
	
	
	/**
	* @api {post} /salon/checkSalonSn 5.检测店铺编号是否重复
	* @apiName checkSalonSn
	* @apiGroup salon
	*
	*@apiParam {String} sn 必填店铺编号.
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
	public function checkSalonSn()
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
			return $this->error('店铺编号重复已经存在');
		}
		else 
		{
			return $this->success();
		}
	}
	
	/**
	 * @api {post} /salon/endCooperation 6.终止合作
	 * @apiName endCooperation
	 * @apiGroup salon
	 *
	 *@apiParam {Number} salonid 必填,店铺ID.
	 *@apiParam {Number} type 必填,操作类型 1终止合作 2恢复店铺.
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
	 *		    "msg": "操作失败请重新再试"
	 *		}
	 */	
	public function endCooperation()
	{
		$param = $this->param;
		$salonid = isset($param["salonid"])?intval($param["salonid"]):0;
		$type = isset($param["type"])?intval($param["type"]):1;
		
		if(!$salonid || !in_array($type, array(1,2)))
		{
			return $this->error('参数错误');
		}
		$result = DB::table('salon')
				->where('salonid',"=", $salonid)
				->where('salestatus',"!=", '3')
				->select(["salestatus","merchantId"])
				->first();
		$rs = (array)$result;
		if(!$rs)
		{
			return $this->error('操作店铺不存在');
		}
		if($rs["salestatus"] == 1 && $type == 2)
		{
			return $this->error('该店铺不是终止合作的店铺');
		}
		elseif($rs["salestatus"] == 0 && $type == 1)
		{
			return $this->error('该店铺已经终止合作');
		}

		$busId = Salon::doendact($salonid,$type,$rs["merchantId"]);
		if($busId)
		{
			return $this->success();
		}
		else
		{
			return $this->error('操作失败请重新再试');
		}
		
		
	}	
	
	/**
	 * @api {post} /salon/del 7.删除店铺
	 * @apiName del
	 * @apiGroup salon
	 *
	 *@apiParam {Number} salonid 删除必填,店铺Id.
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
	 *		    "msg": "删除失败"
	 *		}
	 */	
	public function del()
	{
		$param = $this->param;
		$query = Salon::getQuery();
		
		$salonid = isset($param["salonid"])?$param["salonid"]:0;
		if(!$salonid)
		{
			return $this->error('参数错误');
		}

		$status = Salon::dodel($salonid);
		if($status == -1)
		{
			return $this->error('该店铺未停止合作');
		}
		elseif($status == -2 || !$status)
		{
			return $this->error('操作失败 或者 数据不存在');
		}
		else 
		{
			return $this->success();
		}

		
	}
	
	
	/**
	 * 检测店铺编号是否存在
	 * 
	 * */
	public function getCheckSn($sn,$id=0)
	{
		$query = Salon::getQuery();
		$query->where('sn',$sn);
		if($id)
		{
			$query->where('id',$id);
		}
		return  $query->count();
	}
	
	
}

?>