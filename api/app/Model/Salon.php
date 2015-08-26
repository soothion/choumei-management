<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator;
use DB;
use App\SalonUser;
use App\Merchant;
class Salon extends Model {

	protected $table = 'salon';
	protected $primaryKey = 'salonid';
	public $timestamps = false;
	
	//protected $fillable = ['id', 'sn','name','contact','mobile','phone','email','addr','foundingDate','salonNum','addTime' ];

    public function rebate(){
        return $this->hasMany('App\Rebate');
    }    

	/**
	 * 店铺列表
	 */
	public static  function getSalonList( $where = '' , $page=1, $page_size=20,$orderName = ' add_time  ',$order = 'desc' )
	{
		$fields = array(
				's.salonid',
				's.salonname',
				's.shopType',
				's.zone',
				's.district',
				's.salestatus',
                's.businessId',
				's.sn',
				's.add_time',
				'm.name',
				'm.id as merchantId',
				'b.businessName',
			);
		//手动设置页数
		AbstractPaginator::currentPageResolver(function() use ($page) {
		    return $page;
		});
		$query =  DB::table('salon as s')
            ->leftjoin('salon_info as i', 'i.salonid', '=', 's.salonid')
            ->leftjoin('merchant as m', 'm.id', '=', 's.merchantId')
            ->leftjoin('business_staff as b', 'b.id', '=', 's.businessId')
            ->select($fields)
            ->orderBy($orderName,$order)
            ;
        $query =  $query ->where("salestatus","!=","2");//剔除删除
       // $query =  $query ->where("m.status","!=","2");//剔除商户删除
        if(isset($where["shopType"]))
        {
        	$query =  $query ->where("shopType","=",$where["shopType"]);
        }
		if(isset($where["zone"]))
        {
        	$query =  $query ->where("zone","=",$where["zone"]);
        }
		if(isset($where["district"]))
        {
        	$query =  $query ->where("district","=",$where["district"]);
        }
		if(isset($where["businessName"]))
		{
			$keyword = '%'.$where['businessName'].'%';
			$query = $query->where('b.businessName','like',$keyword);
		}
		if(isset($where['salonname'])&&$where['salonname'])
		{
			$keyword = '%'.$where['salonname'].'%';
			$query = $query->where('salonname','like',$keyword);
		}
		if(isset($where['sn'])&&$where['sn'])
		{
			$keyword = '%'.$where['sn'].'%';
			$query = $query->where('s.sn','like',$keyword);
		}
		if(isset($where['merchantName'])&&$where['merchantName'])
		{
			$keyword = '%'.$where['merchantName'].'%';
			$query = $query->where('m.name','like',$keyword);
		}
		if(isset($where["salestatus"]))
		{
			$query =  $query ->where("salestatus","=",$where["salestatus"]);
		}
         
        $salonList =    $query->paginate($page_size);
        $result = $salonList->toArray();

		$list["total"] = $result["total"];
		$list["per_page"] = $result["per_page"];
		$list["current_page"] = $result["current_page"];
		$list["last_page"] = $result["last_page"];
		$list["from"] = $result["from"];
		$list["to"] = $result["to"];
		unset($result['total']);
		unset($result['per_page']);
		unset($result['current_page']);
		unset($result['last_page']);
		unset($result['from']);
		unset($result['to']);
		unset($result['next_page_url']);
		unset($result['prev_page_url']);
		$data = array();
		$rs = array();
		foreach($result["data"] as $key=>$val)
		{
			$tmpVal = (array)$val;
			$data[$key] = $tmpVal;
			$areaArr = Salon::getAreaMes(array("zone"=>$tmpVal["zone"],"district"=>$tmpVal["district"])) ;
			if($areaArr)
			{
				$rs[$key] = array_merge($data[$key],$areaArr);
			}
			else 
			{
				$rs[$key] = $data[$key];
			}
		}
		
		foreach($rs as $key=>$val)
		{
			if(is_null($val) == true)//null 数据转化为 空字符串
			{
				$rs[$key] = "";
			}
		}
		$list["data"] = $rs;
           
        return $list;
	}
	
	
	/**
	 * 店铺列表导出
	 */
	public static  function getSalonListExport( $where = '',$orderName = 's.add_time  ',$order = 'desc' )
	{
		$fields = array(
				's.salonid',
				's.salonname',
				's.addr',
				's.addrlati',
				's.addrlong',
				's.zone',
				's.district',
				's.shopType',
				's.contractTime',
				's.contractPeriod',
				's.bargainno',
				's.bcontacts',
				's.tel',
				's.phone',
				's.corporateName',
				's.corporateTel',
				's.sn',
				's.salestatus',
				's.businessId',
				's.add_time',
				's.contractEndTime',
				'i.bankName',
				'i.beneficiary',
				'i.bankCard',
				'i.branchName',
				'i.accountType',
				'i.salonArea',
				'i.dressingNums',
				'i.staffNums',
				'i.stylistNums',
				'i.monthlySales',
				'i.totalSales',
				'i.price',
				'i.payScale',
				'i.payMoney',
				'i.payMoneyScale',
				'i.payCountScale',
				'i.cashScale',
				'i.blowScale',
				'i.hdScale',
				'i.platformName',
				'i.platformScale',
				'i.receptionNums',
				'i.receptionMons',
				'i.setupTime',
				'i.hotdyeScale',
				'i.lastValidity',
				'i.salonType',
				'i.contractPicUrl',
				'i.licensePicUrl',
				'i.corporatePicUrl',
				'm.name',
				'm.id as merchantId',
				'm.sn as msn',
				'b.businessName',
				'd.status as dividendStatus',
				'd.recommend_code',	
		);

		$query =  DB::table('salon as s')
		->leftjoin('salon_info as i', 'i.salonid', '=', 's.salonid')
		->leftjoin('merchant as m', 'm.id', '=', 's.merchantId')
		->leftjoin('business_staff as b', 'b.id', '=', 's.businessId')
		->leftjoin('dividend as d', 'd.salon_id', '=', 's.salonid')
		->select($fields)
		->orderBy($orderName,$order)
		;
		$query =  $query ->where("salestatus","!=","2");//剔除删除
		// $query =  $query ->where("m.status","!=","2");//剔除商户删除
		if(isset($where["shopType"]))
		{
			$query =  $query ->where("shopType","=",$where["shopType"]);
		}
		if(isset($where["zone"]))
		{
			$query =  $query ->where("zone","=",$where["zone"]);
		}
		if(isset($where["district"]))
		{
			$query =  $query ->where("s.district","=",$where["district"]);
		}
		if(isset($where["businessName"]))
		{
			$keyword = '%'.$where['businessName'].'%';
			$query = $query->where('b.businessName','like',$keyword);
		}
		if(isset($where['salonname'])&&$where['salonname'])
		{
			$keyword = '%'.$where['salonname'].'%';
			$query = $query->where('salonname','like',$keyword);
		}
		if(isset($where['sn'])&&$where['sn'])
		{
			$keyword = '%'.$where['sn'].'%';
			$query = $query->where('s.sn','like',$keyword);
		}
		if(isset($where['merchantName'])&&$where['merchantName'])
		{
			$keyword = '%'.$where['merchantName'].'%';
			$query = $query->where('m.name','like',$keyword);
		}
		$rs = array();
		$salonList =    $query->get();
		if($salonList)
		{
			foreach ($salonList as $key=>$val)
			{
				$result[$key] = (array)$val;
			}
			$data = array();
			$rs = array();
			foreach($result as $key=>$val)
			{
				$tmpVal = (array)$val;
				$data[$key] = $tmpVal;
				$areaArr = Salon::getAreaMes(array("zone"=>$tmpVal["zone"],"district"=>$tmpVal["district"])) ;
				if($areaArr)
				{
					$rs[$key] = array_merge($data[$key],$areaArr);
				}
				else
				{
					$rs[$key] = $data[$key];
				}
			}
			
			foreach($rs as $key=>$val)
			{
				if(is_null($val) == true)//null 数据转化为 空字符串
				{
					$rs[$key] = "";
				}
			}	
		}
		return $rs;
	}
	
	/**
	 * 终止合作 恢复店铺
	 * type = 1 终止合作  2 恢复店铺
	 */
	public static function doendact($salonid,$type,$merchantId)
	{
		if($type == 1)
		{
			$save["salestatus"] = 0;
			$save["status"] = 2;
		}
		else
		{
			$save["salestatus"] = 1;
			$save["status"] = 1;
		}
		DB::beginTransaction();
		$affectid =  DB::table('salon')
            ->where('salonid', $salonid)
            ->update($save);
            
            
		if($affectid && $type == 1)
		{
			SalonUser::where(['salonid'=>$salonid])->update(['status'=>2]);//停用普通用户账号
			
			$usersCount = DB::table('salon_user')
						->where('merchantId',"=" ,$merchantId)
						->where('salonid',"!=" ,0)
						->where('status',"=" ,1)
						->count();
			if(!$usersCount)
			{
				DB::table('salon_user')//停用账号  超级管理员
		            ->where('salonid',"=" ,0)
		            ->where('merchantId',"=" ,$merchantId)
		            ->update(['status'=>2]);
			}			
			$flag = DB::table('merchant')->where("id","=",$merchantId)->decrement('salonNum',1);//店铺数量减1
			
		}
		elseif($affectid && $type == 2)
		{
			SalonUser::where(['salonid'=>$salonid])->update(['status'=>1]);//恢复普通用户账号
			$usersCount = DB::table('salon_user')
						->where('merchantId',"=" ,$merchantId)
						->where('salonid',"!=" ,0)
						->where('status',"=" ,1)
						->count();
			if($usersCount)
			{
				DB::table('salon_user')//恢复账号  超级管理员
		            ->where('salonid',"=" ,0)
		            ->where('merchantId',"=" ,$merchantId)
		            ->update(['status'=>1]);
			}
			
			$flag = DB::table('merchant')->where("id","=",$merchantId)->increment('salonNum',1);//店铺数量加1
		}
		if($flag)
		{
			DB::commit();
		}
		else
		{
			DB::rollBack();  
		}
		return $flag;
	}
	
	/**
	 * 删除店铺
	 * */
	public static function dodel($salonid)
	{
		$result = DB::table('salon')->select(["salestatus","merchantId"])->where('salonid', $salonid)->first();
		$rs = (array)$result;	
		
		
		if(!$rs)
		{
			return -2;
		}
		elseif($rs["salestatus"] == 1 )
		{
			return -1;//未停止合作
		}

		$affectid =  DB::table('salon')
            ->where('salonid', $salonid)
            ->update(array("salestatus"=>2,"status"=>3));
		
		SalonUser::where(['salonid'=>$salonid])->update(['status'=>3]);//删除普通用户账号
		
		$merchantId = $rs["merchantId"];
		$usersCount = DB::table('salon_user')
		->where('merchantId',"=" ,$merchantId)
		->where('salonid',"!=" ,0)
		->where('status',"=" ,1)
		->count();
		if(!$usersCount)
		{
			DB::table('salon_user')//删除账号  超级管理员
			->where('salonid',"=" ,0)
			->where('merchantId',"=" ,$merchantId)
			->update(['status'=>3]);
		}
		
		
		return $affectid;
	}
	
	/**
	 * 获取店铺详情
	 * */
	public static function getSalon($salonid)
	{
		$fields = array(
					's.salonid',
					's.salonname',
					's.addr',
					's.addrlati',
					's.addrlong',
					's.zone',
	   			    's.district',
					's.shopType',
					's.contractTime',
					//'s.contractPeriod',
					's.bargainno',
					's.bcontacts',
					's.tel',
					's.phone',
					's.corporateName',
					's.corporateTel',
	                's.sn',
	                's.salestatus',
	                's.businessId',
					's.contractEndTime',
					's.salonGrade',
					's.salonChangeGrade',
					's.changeInTime',
					's.salonCategory',
					'i.bankName',
					'i.beneficiary',
					'i.bankCard',
					'i.branchName',
					'i.accountType',
					'i.salonArea',
					'i.dressingNums',
					'i.staffNums',
					'i.stylistNums',
					'i.monthlySales',
					'i.totalSales',
					'i.price',
					'i.payScale',
					'i.payMoney',
					'i.payMoneyScale',
					'i.payCountScale',
					'i.cashScale',
					'i.blowScale',
					'i.hdScale',
					'i.platformName',
					'i.platformScale',
					'i.receptionNums',
					'i.receptionMons',
					'i.setupTime',
					'i.hotdyeScale',
					'i.lastValidity',
					'i.salonType',
					'i.contractPicUrl',
					'i.licensePicUrl',
					'i.corporatePicUrl',
					'i.floorDate',
					'i.advanceFacility',
					'i.commissionRate',
					'i.dividendPolicy',
					'i.rebatePolicy',
					'i.basicSubsidies',
					'i.bsStartTime',
					'i.bsEndTime',
					'i.strongSubsidies',
					'i.ssStartTime',
					'i.ssEndTime',
					'i.strongClaim',
					'i.subsidyPolicy',
				
					'm.name',
					'm.id as merchantId',
					'b.businessName',
					'd.status as dividendStatus',
					'd.recommend_code',
					
				);
			$salonList =  DB::table('salon as s')
	            ->leftjoin('salon_info as i', 'i.salonid', '=', 's.salonid')
	            ->leftjoin('merchant as m', 'm.id', '=', 's.merchantId')
	            ->leftjoin('business_staff as b', 'b.id', '=', 's.businessId')
	            ->leftjoin('dividend as d', 'd.salon_id', '=', 's.salonid')
	            ->select($fields)
	            ->where(array("s.salonid"=>$salonid))
	            ->first();

			$salonList = (array)$salonList;
			
			if($salonList)
			{
				foreach($salonList as $key=>$val)
				{
					if(is_null($val) == true)//null 数据转化为 空字符串
					{
						$salonList[$key] = "";
					}
					
					if($val === "0.00")//0 0.00默认值 数据转化为 空字符串
					{
						$salonList[$key] = "";
					}
					
				}
			}
	
			if($salonList)
			{
				$areaArr = self::getAreaMes(array("zone"=>$salonList["zone"],"district"=>$salonList["district"])) ;
				if($areaArr)
				{
					$salonList = array_merge($salonList,$areaArr);
				}
			}
			return $salonList;
	}
	
	/**
	 * 获取省市区信息
	 * */
	public static  function getAreaMes($salonList)
	{
			$rs["zoneName"] = "";
			$rs["districtName"] = "";
			$rs["citiesName"] = "";
			$rs["citiesId"] = "";
			$rs["provinceName"] = "";
			$rs["provinceId"] = "";
			//商圈
			$zoneList = DB::table('salon_area')
                    ->where(array("areaid"=>$salonList["zone"]))
                    ->first();   
            if($zoneList)
            {
            	$rs["zoneName"] = $zoneList->areaname? $zoneList->areaname:"";
            } 
			//区
			$districtList = DB::table('town')
                    ->where(array("tid"=>$salonList["district"]))
                    ->first();  
            if($districtList)
            {
            	$rs["districtName"] = $districtList->tname? $districtList->tname:"";
			
				//市
				$cityList = DB::table('city')
	                    ->where(array("pid"=>$districtList->iid))
	                    ->first(); 
	            if($cityList)
	            {
	            	$rs["citiesName"] = $cityList->iname? $cityList->iname:"";
					$rs["citiesId"] = $cityList->iid? $cityList->iid:"";
				
				
					//省
					$provinceList = DB::table('province')
	                    ->where(array("pid"=>$cityList->pid))
	                    ->first();  
	                if($provinceList) 
	                {
	                	$rs["provinceName"] = $provinceList->pname? $provinceList->pname:"";
						$rs["provinceId"] = $provinceList->pid? $provinceList->pid:"";
	                }  
					
	            }    
				
            }
            return $rs;
	}
	
	/**
	 * 自动生成店铺编号
	 * */
	public static function getSn($merchantId)
	{
		$merchantSn = Merchant::where(array("id"=>$merchantId))->select(array("sn"))->first();
		$sCount = Salon::where(array("merchantId"=>$merchantId))->count();
		$sn = intval($sCount)+1; //店铺编号，根据商户编号+01，自增长3位
	    $tps = "";	 
		for($i=3;$i>strlen($sn);$i--)
		{
			$tps .= 0; 
		}
		return $merchantSn->sn.$tps.$sn;	
	}

}

