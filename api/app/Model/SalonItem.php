<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Salon;
use Illuminate\Pagination\AbstractPaginator;
use App\Hairstylist;
class SalonItem extends Model {

	protected $table = 'salon_item';

	public $timestamps = false;
	
	public function salonItemType()
	{
	    return $this->belongsTo(SalonItemType::class,'typeid','typeid');
	}
	
	public function salonNormsCat()
	{
	    return $this->belongsTo(SalonNormsCat::class,'norms_cat_id','salon_norms_cat_id');
	}
	
	public function salon()
	{
	    return $this->belongsTo(Salon::class,'salonid','salonid');
	}
	
	/*
	 * 获取店铺项目资料
	 * */
	public static function getSalonItem($key,$keyword,$page,$page_size)
	{

		//手动设置页数
		AbstractPaginator::currentPageResolver(function() use ($page) {
			return $page;
		});
		$query = Salon::select(['salonid','salonname'])->where('status','!=','3')->orderBy('salonid','desc');
		if($key == 1)
		{
			$keyword = '%'.$keyword.'%';
			$query = $query->where('salonname','like',$keyword);
		}
		elseif($key == 2)
		{
			$keyword = '%'.$keyword.'%';
			$query = $query->whereRaw("merchantId in (SELECT `id` FROM `cm_merchant` WHERE `name` LIKE '{$keyword}')");
		}
		
		$salonList =    $query->paginate($page_size);
		$result = $salonList->toArray();
		if($result)
		{
			foreach ($result['data'] as $key=>$val)
			{
				$result['data'][$key]['generalNums'] = self::where(['salonid'=>$val['salonid'],'status'=>1,'item_type'=>1])->count();//普通项目
				$result['data'][$key]['specialNums'] = self::where(['salonid'=>$val['salonid'],'status'=>1,'item_type'=>2])->count();//限时特价
				$result['data'][$key]['wareroomNums'] = self::where(['salonid'=>$val['salonid'],'status'=>2])->count();//项目仓库
				$result['data'][$key]['hairstyNums'] = Hairstylist::where(['salonid'=>$val['salonid'],'status'=>1])->count();//造型师
			}
		}
		return $result;
	}
	
}
