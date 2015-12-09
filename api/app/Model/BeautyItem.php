<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator;
use App\BookingCalendar;
use App\BeautyItemNorm;
class BeautyItem extends Model {

	protected $table = 'beauty_item';
	
	public $timestamps = false;
	
	protected $primaryKey = 'item_id';
	
	public static function getBeautyItem($page,$page_size,$type,$is_gift)
	{
		//$fields = ['item_id','beauty_id','type','name','detail','description','archive','beauty','register_detail','equipment','equipment_cover','register_workflow','presen','logo','images','level','price','vip_price','expire','present_explain','is_gift','created_at','updated_at'];
		$fields = ['item_id','beauty_id','type','name','level','price','vip_price'];
		//手动设置页数
		AbstractPaginator::currentPageResolver(function() use ($page) {
		    return $page;
		});
		
	    $query = self::select($fields)->orderBy('level', 'asc')->orderBy('item_id', 'desc');
		if(isset($type) && intval($type))
		{
			$query = $query->where('type','=',$type);
		}
		if(isset($is_gift))
		{
			$query = $query->where('is_gift','=',$is_gift);
		}
		$result = $query->paginate($page_size)->toArray();
		
		if($result)
		{
		
			foreach($result['data'] as $key=>$val)
			{	
				$quantityRs = self::getQuantity($val['item_id']);
				$result['data'][$key]['quantity'] =  $quantityRs->quantity?$quantityRs->quantity:0;
				if($val['type'] == 2)//韩式快时尚 多规格价格查询
				{
					$priceRs = self::getMinMaxPrices($val['item_id']);
					$result['data'][$key]['prices'] =  $priceRs;
					unset($result['data'][$key]['price']);
					unset($result['data'][$key]['vip_price']);
				}
			}
			unset($result['next_page_url']);
			unset($result['prev_page_url']);
		}
        return $result;
	}
	
	/**
	*获取多规格 价格区间
	*item_id 项目Id
	*/
	public static function getMinMaxPrices($item_id)
	{
		return BeautyItemNorm::selectRaw('min(price) as `min_price`,max(price) as `max_price`,min(vip_price) as `min_vip_price`,max(vip_price) as `max_vip_price`')->where(['item_id'=>$item_id])->first();		
	}
	
	/**
	*获取总预约数
	*item_id 项目Id
	*/
	public static function getQuantity($item_id)
	{
		return  BookingCalendar::selectRaw('SUM(QUANTITY) as `quantity`')->where(['ITEM_ID'=>$item_id])->first();
	}	
	
}

