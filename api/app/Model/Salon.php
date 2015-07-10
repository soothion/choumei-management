<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator;
use DB;
use App\SalonUser;
class Salon extends Model {

	protected $table = 'salon';
	protected $primaryKey = 'salonid';
	public $timestamps = false;
	
	//protected $fillable = ['id', 'sn','name','contact','mobile','phone','email','addr','foundingDate','salonNum','addTime' ];
	
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
		if(isset($where["businessId"]))
        {
        	$query =  $query ->where("businessId","=",$where["businessId"]);
        }
		if(isset($where['salonname'])&&$where['salonname'])
		{
			$keyword = '%'.$where['salonname'].'%';
			$query = $query->where('salonname','like',$keyword);
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
		foreach($result["data"] as $key=>$val)
		{
			$tmpVal = (array)$val;
			$data[$key] = $tmpVal;
			$areaArr = Salon::getAreaMes(array("zone"=>$tmpVal["zone"],"district"=>$tmpVal["district"])) ;
			if($areaArr)
			{
				$data[$key] = array_merge($data[$key],$areaArr);
			}
		}
		
		foreach($data as $key=>$val)
		{
			if(is_null($val) == true)//null 数据转化为 空字符串
			{
				$data[$key] = "";
			}
		}
		$list["data"] = $data;
           
        return $list;
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
		}
		else
		{
			$save["salestatus"] = 1;
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
		$result = DB::table('salon')->select(["salestatus"])->where('salonid', $salonid)->first();
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
					'b.businessName'
				);
			$salonList =  DB::table('salon as s')
	            ->leftjoin('salon_info as i', 'i.salonid', '=', 's.salonid')
	            ->leftjoin('merchant as m', 'm.id', '=', 's.merchantId')
	            ->leftjoin('business_staff as b', 'b.id', '=', 's.businessId')
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
				$rs["citiesName"] = $cityList->iname? $cityList->iname:"";
				$rs["citiesId"] = $cityList->iid? $cityList->iid:"";
				
				
				//省
				$provinceList = DB::table('province')
	                    ->where(array("pid"=>$cityList->pid))
	                    ->first();     
				$rs["provinceName"] = $provinceList->pname? $provinceList->pname:"";
				$rs["provinceId"] = $provinceList->pid? $provinceList->pid:"";
            }
            return $rs;
	}

}

