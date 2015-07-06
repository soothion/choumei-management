<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator;
use DB;
class Salon extends Model {

	protected $table = 'salon';
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
           
        return $salonList;
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
		$affectid =  DB::table('salon')
            ->where('salonid', $salonid)
            ->update($save);
            
            
		if($affectid && $type == 1)
		{
			DB::table('merchant')->where("id","=",$merchantId)->decrement('salonNum',1);//店铺数量减1
		}
		elseif($affectid && $type == 2)
		{
			DB::table('merchant')->where("id","=",$merchantId)->increment('salonNum',1);//店铺数量加1
		}
		return $affectid;
	}
	
	/*
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
	
	public function businessStaff()
	{
        return $this->belongsTo('App\BusinessStaff');
    }
    
	public function salonInfo()
	{
        return $this->belongsTo('App\SalonInfo');
    }
    
	public function merchant()
	{
        return $this->belongsTo('App\Merchant');
    }

}

