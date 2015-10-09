<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Salon;
use Illuminate\Pagination\AbstractPaginator;
use App\Hairstylist;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;
class SalonItem extends Model {

    /**
     * 上架状态
     * @var unknown
     */
    CONST STATUS_OF_UP = 1;
    
    /**
     * 下架状态
     * @var unknown
     */
    CONST STATUS_OF_DOWN = 2;
    
    /**
     * 删除状态
     * @var unknown
     */
    CONST STATUS_OF_DELETE = 3;
    
	protected $table = 'salon_item';

	public $timestamps = false;
	
	public function salonItemType()
	{
	    return $this->belongsTo(SalonItemType::class,'typeid','typeid');
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

	public static function composite($items,$formats)
	{
	    foreach($items as &$item)
	    {
	        $cat_id = $item['norms_cat_id'];
	        if(isset($formats[$cat_id]))
	        {
	            $item['salon_norms_cat'] = $formats[$cat_id];
	        }
	        else 
	        {
	            $item['salon_norms_cat'] = [];
	        }
	    }
	    return $items;
	}

	/**
	 * @param array $itemIds 要上架的salon item ids的数组
	 * @return array $result 检查的结果 或者 参数错误提示
	 */
	public static function checkUp($ids) 
	{
	    $ids = array_unique($ids);
	    $input_count = count($ids);
	    if($input_count<1)
	    {
	        return true;
	    }  
	    
	    $now_time = time();
	    $items = SalonItem::select(['itemid','itemname','exp_time','total_rep','sold'])->whereIn('itemid',$ids)->where('status',SalonItem::STATUS_OF_DOWN)->get();
	    if(empty($items))
	    {
	        throw new ApiException("要上架的项目不存在或者状态不正确",ERROR::ITEM_LOST_OR_WRONG_STATE);
	    }
	    $itemArr = $items->toArray();
	    $item_ids = array_column($itemArr, "itemid");
	    $error_ids = array_diff($item_ids, $ids);
	    if(count($error_ids)>0)
	    {
	        throw new ApiException("ids : [".implode(',', $error_ids)."] 项目不存在或者状态不正确",ERROR::ITEM_LOST_OR_WRONG_STATE);
	    }
	    
	    foreach ($itemArr as $item)
	    {
	        $id = $item['itemid'];
	        $name = $item['itemname'];
	        $exp_time = intval($item['exp_time']);
	        $total_rep = intval($item['total_rep']);
	        $sold = intval($item['sold']);
	        if($exp_time >0 && $exp_time < $now_time)
	        {
	            throw new ApiException("项目 [{$id} : $name] 有效期 [".date("Y-m-d H:i:s",$exp_time)."]应大于当前时间",ERROR::ITEM_WRONG_EXP_TIME);
	        }
	        
	        if($total_rep >0 && $total_rep < $sold)
	        {
	            throw new ApiException("项目 [{$id} : $name] 库存[{$total_rep}]应大于已售份数[{$sold}]",ERROR::ITEM_WRONG_TOTAL_REQ);
	        }
	    }
	    return true;
	}
	
}
