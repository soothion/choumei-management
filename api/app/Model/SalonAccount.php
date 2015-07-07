<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SalonUser;
use Illuminate\Pagination\AbstractPaginator;
use DB;

class SalonAccount extends Model {
	
	
	/**
	 * 获取账号列表
	 * 
	 * */
	public static function getList($where = '' , $page=1, $page_size=20)
	{
		//手动设置页数
		AbstractPaginator::currentPageResolver(function() use ($page) {
		    return $page;
		});
		$fields = array("u.salon_user_id as salonUserId","u.username","u.salonid","u.roleType","u.addTime","u.status","u.merchantId","m.name","s.salonname");
		$query =  DB::table('salon_user as u')
            ->leftjoin('salon as s', 'u.salonid', '=', 's.salonid')
            ->leftjoin('merchant as m', 'm.id', '=', 'u.merchantId')
            ->select($fields)
            ->orderBy("salon_user_id","desc")
            ;
        if(isset($where['salonname'])&&$where['salonname'])//店铺名
		{
			$keyword = '%'.urldecode($where['salonname']).'%';
			$query = $query->where('s.salonname','like',$keyword);
		}
		if(isset($where['username'])&&$where['username'])//账号名
		{
			$keyword = '%'.urldecode($where['username']).'%';
			$query = $query->where('u.username','like',$keyword);
		}
		if(isset($where['name'])&&$where['name'])//商户名
		{
			$keyword = '%'.urldecode($where['name']).'%';
			$query = $query->where('m.name','like',$keyword);
		}
         
        $salonList =  $query->paginate($page_size);
        $result = $salonList->toArray();
        return $result;
	}
	
	
	/***
	 * 店铺账号添加操作
	 * */
	public static function dosave($data)
	{
		return SalonUser::insertGetId($data);
	}
	
	/***
	 * 店铺账号修改
	 * */
	public static function doUpdate($salon_user_id,$data)
	{
		 $query = SalonUser::getQuery();
		 return $query->where("salon_user_id","=",$salon_user_id)->update($data);
		 
	}
	
	/***
	 * 单个店铺只允许一个普通用户
     * 单个商户只允许一个超级管理员
     *    
	 * */
	public static function getAccountNums($data)
	{
	   $query = SalonUser::getQuery();
	   $query = $query->where('roleType','=',$data["roleType"]);
	   if($data["roleType"] == 1)
	   {
	  	 	$query = $query->where('salonid','=',$data["salonid"]);
	   }
	   $query = $query->where('merchantId','=',$data["merchantId"]);
	   return $query->select(array("salon_user_id","username"))->count();
	}
	
	
	

}
