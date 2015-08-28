<?php namespace App\Http\Controllers\Merchant;


use App\Http\Controllers\Controller;
use App\Salon;
use Illuminate\Pagination\AbstractPaginator;
use DB;
use App\Merchant;
use App\SalonInfo;
use App\Dividend;
use App\Town;
use App\SalonUser;
use Excel;
use Event;
use App\CompanyCodeCollect;

class SalonController extends Controller {
		
	//预定码
	private $codeArr = array('6890','9988','9966','5988','6990','8668','6666','6888','5800','6688','8918','9898','1200','1201','1202','1299','1211','8608','1206','1207','1208','1298','1210','1279','1212','1213','1222','1215','1217','1218','0202','1258','1290','1220','1221','1223','1224','1887','1881','1890','1818','1877','1876','1875','1873','1872','1871','1870','1869','1868','1867','1866','1865','1863','1861','1860','1858','1857','1856','1855','1853','1852','1851','1850','1839','1836','1835','1832','1831','1900','1909','1828','1827','1826','1825','1823','1822','1821','1820','1901','1902','1903','1905','1906','1907','1226','1227','1287','1230','1286','1717','5917','0628','7777','0718','3333','1111','0805','0810','0822','1777','1010','0626');
	
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
    					"contractEndTime",
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
    					"salonCategory",
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
	* @apiParam {String} sn 可选,店铺编号
	* @apiParam {String} merchantName 可选,商户名
	* @apiParam {String} salestatus 状态 0终止合作 1正常合作.
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
		$sn = isset($param["sn"])?$param["sn"]:0;//店铺编号
		$merchantName = isset($param["merchantName"])?urldecode($param["merchantName"]):"";//商户名称
		$businessName = isset($param["businessName"])?urldecode($param["businessName"]):"";//业务代表
		$salestatus = isset($param["salestatus"])?$param["salestatus"]:0;//店铺状态 
		
		$sort_key = isset($param["sort_key"])?$param["sort_key"]:"salonid";
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
    	if($merchantName)
    	{
    		$where["merchantName"] = $merchantName;
    	}
    	if($sn)
    	{
    		$where["sn"] = $sn;
    	}
    	if(isset($param["salestatus"]))
    	{
    		$where["salestatus"] = $salestatus;
    	}
		if($businessName)
		{
			$where["businessName"] = $businessName;
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
	* @apiParam {String} salonname 必填,店名.
	* @apiParam {Number} district 必填,行政地区 . 
	* @apiParam {String} addr 必填,详细街道信息.
	* @apiParam {Number} addrlati 必填,地理坐标纬度.
	* @apiParam {Number} addrlong 必填,地理坐标经度.
	* @apiParam {Number} zone 必填,所属商圈 .
	* @apiParam {Number} shopType 必填,店铺类型  1预付款店 2投资店 3金字塔店.
	* @apiParam {String} contractTime 可选,合同日期  Y-m-d.
	* @apiParam {String} contractEndTime 可选,合同截止日期 Y-m-d.
	* @apiParam {String} bargainno 可选,合同编号.
	* @apiParam {String} bcontacts 可选,联系人.
	* @apiParam {String} tel 必填,店铺座机.
	* @apiParam {String} phone 必填,联系电话.
	* @apiParam {String} corporateName 必填,法人代表.
	* @apiParam {String} corporateTel 必填,法人电话.
	* @apiParam {Number} businessId 必填,业务代表Id.
	* @apiParam {Number} salonCategory 必填,店铺分类 1工作室2店铺.
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
	* @apiParam {String} salonGrade 可选,店铺当前等级 1特级店2A级店3B级店4C级店4淘汰店.
	* @apiParam {String} salonChangeGrade 必填,店铺调整等级.
	* @apiParam {String} changeInTime 必填,调整生效日期 Y-m-d.
	* @apiParam {String} floorDate 可选,落地日期Y-m-d.
	* @apiParam {String} advanceFacility 可选,预付款额度.
	* @apiParam {String} commissionRate 可选,佣金率.
	* @apiParam {String} dividendPolicy 可选,分红政策.
	* @apiParam {String} rebatePolicy 可选,返佣政策.
	* @apiParam {String} basicSubsidies 可选,基础补贴政策.
	* @apiParam {String} bsStartTime 可选,基础补贴起始日.
	* @apiParam {String} bsEndTime 可选,基础补贴截止日.
	* @apiParam {String} strongSubsidies 可选,强补贴政策.
	* @apiParam {String} ssStartTime 可选,强补贴起始日.
	* @apiParam {String} ssEndTime 可选,强补贴截止日.
	* @apiParam {String} strongClaim 可选,强补贴月交易单数要求.
	* @apiParam {String} subsidyPolicy 可选,首单指标补贴政策.
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
	* @apiParam {String} contractTime 可选,合同开始日期  Y-m-d.
	* @apiParam {String} contractEndTime 可选,合同截止日期 Y-m-d.
	* @apiParam {String} bargainno 可选,合同编号.
	* @apiParam {String} bcontacts 可选,联系人.
	* @apiParam {String} tel 必填,店铺座机.
	* @apiParam {String} phone 必填,联系电话.
	* @apiParam {String} corporateName 必填,法人代表.
	* @apiParam {String} corporateTel 必填,法人电话.
	* @apiParam {Number} businessId 必填,业务代表Id.
	* @apiParam {Number} dividendStatus 可选, 1退出分红联盟 0加入分红联盟.
	* @apiParam {Number} salonCategory 必填,店铺分类 1工作室2店铺.
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
	* @apiParam {String} salonGrade 可选,店铺当前等级 1特级店2A级店3B级店4C级店4淘汰店.
	* @apiParam {String} salonChangeGrade 必填,店铺调整等级.
	* @apiParam {String} changeInTime 必填,调整生效日期 Y-m-d.
	* @apiParam {String} floorDate 可选,落地日期Y-m-d.
	* @apiParam {String} advanceFacility 可选,预付款额度.
	* @apiParam {String} commissionRate 可选,佣金率.
	* @apiParam {String} dividendPolicy 可选,分红政策.
	* @apiParam {String} rebatePolicy 可选,返佣政策.
	* @apiParam {String} basicSubsidies 可选,基础补贴政策.
	* @apiParam {String} bsStartTime 可选,基础补贴起始日.
	* @apiParam {String} bsEndTime 可选,基础补贴截止日.
	* @apiParam {String} strongSubsidies 可选,强补贴政策.
	* @apiParam {String} ssStartTime 可选,强补贴起始日.
	* @apiParam {String} ssEndTime 可选,强补贴截止日.
	* @apiParam {String} strongClaim 可选,强补贴月交易单数要求.
	* @apiParam {String} subsidyPolicy 可选,首单指标补贴政策.
	* 
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
		//$data["sn"] = isset($param["sn"])?trim($param["sn"]):"";//店铺编号  1.3自动生成
		$data["salonname"] = isset($param["salonname"])?trim($param["salonname"]):"";//店铺名称
		$data["district"] = isset($param["district"])?trim($param["district"]):"";//行政地区  
		$data["addr"] = isset($param["addr"])?trim($param["addr"]):"";//详细街道信息
		
		$data["addrlati"] = isset($param["addrlati"])?trim($param["addrlati"]):"";//地理坐标纬度   
		$data["addrlong"] = isset($param["addrlong"])?trim($param["addrlong"]):"";//地理坐标经度
		
		$data["zone"] = isset($param["zone"])?trim($param["zone"]):"";//所属商圈 - 位置地区
		$data["shopType"] = isset($param["shopType"])?intval($param["shopType"]):0;//店铺类型
		$data["contractTime"] = isset($param["contractTime"])?strtotime($param["contractTime"]):"";//合同日期
		//$data["contractPeriod"] = isset($param["contractPeriod"])?trim($param["contractPeriod"]):"";//合同期限
		
		$data["bargainno"] = isset($param["bargainno"])?trim($param["bargainno"]):"";//合同编号
		$data["bcontacts"] = isset($param["bcontacts"])?trim($param["bcontacts"]):"";//联系人
		$data["tel"] = isset($param["tel"])?trim($param["tel"]):"";//店铺座机
		$data["phone"] = isset($param["phone"])?trim($param["phone"]):"";//联系电话
		$data["corporateName"] = isset($param["corporateName"])?trim($param["corporateName"]):"";//法人代表
		$data["corporateTel"] = isset($param["corporateTel"])?trim($param["corporateTel"]):"";//法人电话
		$data["businessId"] = isset($param["businessId"])?trim($param["businessId"]):"";//业务代表ID
		$data["salonCategory"] = isset($param["salonCategory"])?trim($param["salonCategory"]):"";//店铺分类
		
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
		
		//1.3版本新增字段
		$data["contractEndTime"] = isset($param["contractEndTime"])?strtotime($param["contractEndTime"]):"";//合同截止日期
		$data["salonChangeGrade"] = isset($param["salonChangeGrade"])?trim($param["salonChangeGrade"]):"";//店铺调整等级   
		$data["changeInTime"] = isset($param["changeInTime"])?strtotime($param["changeInTime"]):"";//调整生效日期
		
		$dataInfo["floorDate"] = isset($param["floorDate"])?strtotime($param["floorDate"]):"";//落地日期
		$dataInfo["advanceFacility"] = isset($param["advanceFacility"])?trim($param["advanceFacility"]):"";//预付款额度
		$dataInfo["commissionRate"] = isset($param["commissionRate"])?trim($param["commissionRate"]):"";//佣金率
		$dataInfo["dividendPolicy"] = isset($param["dividendPolicy"])?trim($param["dividendPolicy"]):"";//分红政策
		$dataInfo["rebatePolicy"] = isset($param["rebatePolicy"])?trim($param["rebatePolicy"]):"";//返佣政策
		$dataInfo["basicSubsidies"] = isset($param["basicSubsidies"])?trim($param["basicSubsidies"]):"";//基础补贴政策
		$dataInfo["bsStartTime"] = isset($param["bsStartTime"])?strtotime($param["bsStartTime"]):"";//基础补贴起始日
		$dataInfo["bsEndTime"] = isset($param["bsEndTime"])?strtotime($param["bsEndTime"]):"";//基础补贴截止日
		$dataInfo["strongSubsidies"] = isset($param["strongSubsidies"])?trim($param["strongSubsidies"]):"";//强补贴政策
		$dataInfo["ssStartTime"] = isset($param["ssStartTime"])?strtotime($param["ssStartTime"]):"";//强补贴起始日
		$dataInfo["ssEndTime"] = isset($param["ssEndTime"])?strtotime($param["ssEndTime"]):"";//强补贴截止日
		$dataInfo["strongClaim"] = isset($param["strongClaim"])?trim($param["strongClaim"]):"";//强补贴月交易单数要求
		$dataInfo["subsidyPolicy"] = isset($param["subsidyPolicy"])?trim($param["subsidyPolicy"]):"";//首单指标补贴政策
		
		

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
		$joinDividend = isset($param['dividendStatus'])?intval($param['dividendStatus']):'';
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
			
		}
		else 
		{
			$where = '';
			$whereInfo = '';
			$data["add_time"] = time();
			$data["bountyType"] = 3;//店铺等级C
			$dataInfo["addTime"] = time();
		}
		
		$row = $this->doadd($data,$dataInfo,$where,$whereInfo,$joinDividend);
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
	* @apiSuccess {String} contractTime 合同日期   (时间戳).
	* @apiSuccess {String} contractEndTime 合同截止日期 (时间戳).
	* @apiSuccess {String} bargainno 合同编号.
	* @apiSuccess {String} bcontacts 联系人.
	* @apiSuccess {String} tel 店铺座机.
	* @apiSuccess {String} phone 联系电话.
	* @apiSuccess {String} corporateName 法人代表.
	* @apiSuccess {String} corporateTel 法人电话.
	* @apiSuccess {Number} businessId 业务代表ID.
	* @apiSuccess {Number} salonCategory 店铺分类 1工作室2店铺.
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
	* @apiSuccess {String} recommend_code 推荐码.
	* @apiSuccess {String} dividendStatus 分红联盟状态 0：开启  1关闭.
	* @apiSuccess {String} salonGrade 店铺当前等级 1特级店2A级店3B级店4C级店4淘汰店.
	* @apiSuccess {String} salonChangeGrade 店铺调整等级.
	* @apiSuccess {String} changeInTime 调整生效日期 (时间戳).
	* @apiSuccess {String} floorDate 落地日期 (时间戳).
	* @apiSuccess {String} advanceFacility 预付款额度.
	* @apiSuccess {String} commissionRate 佣金率.
	* @apiSuccess {String} dividendPolicy 分红政策.
	* @apiSuccess {String} rebatePolicy 返佣政策.
	* @apiSuccess {String} basicSubsidies 基础补贴政策.
	* @apiSuccess {String} bsStartTime 基础补贴起始日 (时间戳).
	* @apiSuccess {String} bsEndTime 基础补贴截止日 (时间戳).
	* @apiSuccess {String} strongSubsidies 强补贴政策.
	* @apiSuccess {String} ssStartTime 强补贴起始日 (时间戳).
	* @apiSuccess {String} ssEndTime 强补贴截止日 (时间戳).
	* @apiSuccess {String} strongClaim 强补贴月交易单数要求.
	* @apiSuccess {String} subsidyPolicy 首单指标补贴政策.
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
	private  function doadd($data,$dataInfo,$where='',$whereInfo='',$joinDividend=0)
	{

		DB::beginTransaction();
		if($where)//修改
		{
			Salon::where($where)->update($data);
			$salonTmpInfo = SalonInfo::where($whereInfo)->first();
			if(!$salonTmpInfo)
			{
				DB::table('salon_info')->insertGetId(array("salonid"=>$whereInfo["salonid"]));
			}
			$affectid = SalonInfo::where($where)->update($dataInfo);
			$salonId = $whereInfo["salonid"];
			if($affectid)
			{
				//触发事件，写入日志
				Event::fire('salon.update','店铺Id:'.$salonId." 店铺名称：".$data['salonname']);
			}
			$this->addSalonCode($data,$salonId,2,$joinDividend);//店铺邀请码
		}
		else //添加
		{
			
			$data['sn'] = Salon::getSn($data['merchantId']);//店铺编号
			$salonId = DB::table('salon')->insertGetId($data);
			if($salonId)
			{
					$dataInfo["salonid"] = $salonId;
					$affectid = DB::table('salon_info')->insertGetId($dataInfo);
					if($affectid)
					{
						DB::table('merchant')->where("id","=",$data["merchantId"])->increment('salonNum',1);//店铺数量加1
						//触发事件，写入日志
						Event::fire('salon.save','店铺Id:'.$salonId." 店铺名称：".$data['salonname']);
					}
					
			}
			$this->addSalonCode($data,$salonId,1,$joinDividend);//添加店铺邀请码
		}
		
		
		
		//超级管理员设置
		Salon::where(array('salonid'=>$salonId))->update(array('puserid'=>$this->setAdminAccount($data["merchantId"])));
		
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
	 * 设置超级管理员账户
	 * 
	 * */
	private function setAdminAccount($merchantId)
	{
		$userId = 0;
		$salonAccount = SalonUser::where(array("merchantId"=>$merchantId,"roleType"=>2,"status"=>1))->select(array("salon_user_id"))->first();
		if($salonAccount)
		$userId = $salonAccount->salon_user_id;
		
		return $userId;
		
	}
	
	
	/**
	 * 添加店铺邀请码
	 * act 1添加 2修改
	 * */
	private function addSalonCode($data,$salonid,$act,$joinDividend)
	{
		if(!$salonid){ return false;}
		$query = Dividend::getQuery();
		if($joinDividend == 1)//关闭
		{
			$status = 1;
		}
		else   //开启
		{
			$status = 0;
		}
		$info = Dividend::where(array('salon_id'=>$salonid))->first();
		if($data['shopType'] == 3 && $info)  //金字塔店
		{
			$query->where('salon_id',$salonid)->update(array('status'=>$status,'update_time'=>time()));
		}
		else if($data['shopType'] != 3 && $info) //修改店铺类型不是 金字塔    --关闭
		{
			$query->where('salon_id',$salonid)->update(array('status'=>1,'update_time'=>time()));
		}
		elseif ($data['shopType'] == 3 && !$info) //添加
		{
			$code = $this->getRecommendCode();
			$townInfo = Town::where(array("tid"=>$data["district"]))->first();
			// 写入推荐码表
			$datas=array (
					"salon_id" => $salonid,
					"district" => $townInfo["tname"]?:'',
					"recommend_code" => $code,
					"status" => $status,
					"add_time" => time ()
			);
			DB::table('dividend')->insertGetId($datas);
		}
		
	}
	
	/**
	 * 获取推荐码
	 * 
	 * @return integer
	 */
	private function getRecommendCode() {
		$code = $this->randNum ( 4 );
        // 如果不是四位随机数则重新生成
        if (intval ( $code ) < 1000){
            return $this->getRecommendCode ();
        }
        
        $codeArr = $this->codeArr;//屏蔽预定码
        if(in_array($code, $codeArr))
        {
        	return $this->getRecommendCode ();
        }

		// 如果数据库中已在存在则继续执行
        $codeTmpInfo = Dividend::where(array("recommend_code"=>$code))->first();
		if ($codeTmpInfo){
            return $this->getRecommendCode ();
        }
        
        // 集团码
        $codeComTmpInfo = CompanyCodeCollect::where(array("code"=>$code))->first();
        if ($codeComTmpInfo){
        	return $this->getRecommendCode ();
        }

		return $code;
	}
	
	/**
	 * 生成随即数字
	 * @param int $length
	 * @return string
	 */
	private function randNum($length){
		$pattern = '12356890';    //字符池,可任意修改
		$key = '';
	
		for($i=0;$i<$length;$i++)    {
			$key .= $pattern{mt_rand(0, strlen($pattern) - 1)};    //生成php随机数
		}
	
		return $key;
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
			Event::fire('salon.endCooperation','店铺Id:'.$salonid." 店铺名称：".$this->getSalonName($salonid));
			return $this->success();
		}
		else
		{
			return $this->error('操作失败请重新再试');
		}
		
		
	}	
	
	/**
	 * 查询店铺名
	 * */
	private function getSalonName($salonid)
	{
		$query = Salon::getQuery();
		$query->where('salonid',$salonid);
		$rs = $query->select('salonname')->first();
		return $rs->salonname;
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
			Event::fire('salon.del','店铺Id:'.$salonid." 店铺名称：".$this->getSalonName($salonid));
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
	
	/**
	 * @api {post} /salon/export 8.店铺列表导出
	 * @apiName export
	 * @apiGroup salon
	 *
	 * @apiParam {Number} shopType 可选,店铺类型
	 * @apiParam {Number} district 可选,区域
	 * @apiParam {Number} zone 可选,所属商圈
	 * @apiParam {String} salonname 可选,店名
	 * @apiParam {String} businessName 可选,业务代表
	 * @apiParam {String} sn 可选,店铺编号
	 * @apiParam {String} merchantName 可选,商户名
	 * @apiParam {Number} sort_key 可选,排序字段 shopType 店铺类型  salestatus 状态.
	 * @apiParam {Number} sort_type 可选,排序 DESC倒序 ASC升序.
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
		$where = "";
		$shopTypeArr = array(0=>'',1=>'预付款店',2=>'投资店',3=>'金字塔店');
		$accountTypeArr = array(0=>'',1=>'对公帐户',2=>'对私帐户');
		$statusArr = array(0=>'终止合作',1=>'正常',2=>'删除');
		$param = $this->param;
		$shopType = isset($param["shopType"])?intval($param["shopType"]):0;//店铺类型
		$zone = isset($param["zone"])?$param["zone"]:0;//所属商圈
		$salonname = isset($param["salonname"])?urldecode($param["salonname"]):"";//店名
		$district = isset($param["district"])?$param["district"]:0;//区域
		$sn = isset($param["sn"])?$param["sn"]:0;//店铺编号
		$merchantName = isset($param["merchantName"])?$param["merchantName"]:"";//商户名称
		$businessName = isset($param["businessName"])?urldecode($param["businessName"]):"";//业务代表
		$sort_key = isset($param["sort_key"])?$param["sort_key"]:"s.salonid";
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
		if($merchantName)
		{
			$where["merchantName"] = $merchantName;
		}
		if($sn)
		{
			$where["sn"] = $sn;
		}
		if($businessName)
		{
			$where["businessName"] = $businessName;
		}
		$list = Salon::getSalonListExport($where,$sort_key,$sort_type);
		$result = array();
		if($list)
		{
			foreach($list as $key=>$val)
			{
				$result[$key]['salonname'] = $val['salonname'];
				$result[$key]['recommend_code'] = $val['recommend_code'];
				$result[$key]['dividendStatus'] = $val['dividendStatus']?'未进入':'已加入';
				$result[$key]['name'] = $val['name'];
				$result[$key]['addr'] = $val['addr'];
				//$result[$key]['districtName'] = $val['districtName'];
				$result[$key]['zoneName'] = $val['zoneName'];

				$result[$key]['shopType'] = $shopTypeArr[$val['shopType']];
				$result[$key]['salestatus'] = $shopTypeArr[$val['salestatus']];
				$result[$key]['sn'] = $val['sn'];
				$result[$key]['salonid'] = $val['salonid'];
				$result[$key]['msn'] = $val['msn'];
				$result[$key]['add_time'] = $val['add_time']?date('Y-m-d H:i:s',$val['add_time']):'';
				
				$result[$key]['contractTime'] = $val['contractTime']?date('Y-m-d',$val['contractTime']):'';
				$result[$key]['contractEndTime'] = $val['contractEndTime']?date('Y-m-d',$val['contractEndTime']):'';
				/*$contractPeriod = $val['contractPeriod']?explode('_',$val['contractPeriod']):'';  合同期限  V1.3版本暂时去掉
				if($contractPeriod)
				{
					$result[$key]['contractPeriod'] = $contractPeriod[0].'年'.$contractPeriod[1]."月";
				}
				else
				{
					$result[$key]['contractPeriod'] = '';
				}*/
				
				$result[$key]['bargainno'] = $val['bargainno'];
				$result[$key]['bcontacts'] = $val['bcontacts'];
				$result[$key]['phone'] = $val['phone'];
				$result[$key]['tel'] = $val['tel'];
				$result[$key]['corporateName'] = $val['corporateName'];
				$result[$key]['corporateTel'] = $val['corporateTel'];
				$result[$key]['businessName'] = $val['businessName'];
				//银行卡信息
				$result[$key]['bankName'] = $val['bankName'];
				$result[$key]['branchName'] = $val['branchName'];
				$result[$key]['beneficiary'] = $val['beneficiary'];
				$result[$key]['bankCard'] = ' '.$val['bankCard'];
				$result[$key]['accountType'] = $val['accountType']?$accountTypeArr[$val['accountType']]:'';
	
			}
		}
		
		//触发事件，写入日志
		Event::fire('salon.export');
		
		//导出excel
		$title = '店铺列表'.date('Ymd');
		$header = ['店铺名称','店铺邀请码','分红联盟','所属商户','店铺地址','所属商圈','店铺类型','店铺状态','店铺编号','店铺id','商户编号','添加时间','合同开始时间','合同截止时间','合同编号','联系人','联系手机','店铺电话','法人代表','法人手机','业务代表','银行名称','支行名称','收款人','银行卡号','帐户类型'];
		Excel::create($title, function($excel) use($result,$header){
			$excel->sheet('Sheet1', function($sheet) use($result,$header){
				$sheet->fromArray($result, null, 'A1', false, false);//第五个参数为是否自动生成header,这里设置为false
				$sheet->prependRow(1, $header);//添加表头
		
			});
		})->export('xls');
	}
	
	
}

?>