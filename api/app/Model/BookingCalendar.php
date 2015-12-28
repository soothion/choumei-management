<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;

class BookingCalendar extends Model {

	protected $table = 'booking_calendar';
	
	public $timestamps = false;
	
	protected $primaryKey = 'ID';
	
	CONST CHANGE_TYPE_OFF_ADD = 1;
	CONST CHANGE_TYPE_OFF_DEL = 2;
	
	public static function change_items_date($item_ids,$booking_date,$change_type = self::CHANGE_TYPE_OFF_ADD)
	{
	    if(count($item_ids)<1)
	    {
	        return;
	    }
	    $items = BeautyItem::whereIn('item_id',$item_ids)->get(['beauty_id','item_id'])->toArray();
	    $item_idx = Utils::column_to_key('item_id',$items);
	    if(count(array_diff($item_ids, array_keys($item_idx)))>0)
	    {
	        throw new ApiException("部分项目已经不存在!",ERROR::PARAMETER_ERROR);
	    }
	    
	    foreach ($item_ids as $item_id)
	    {
	        $beauty_id = 1;
	        if (isset($item_idx[$item_id]))
	        {
	            $beauty_id = $item_idx[$item_id]['beauty_id'];
	        }
	        self::upsert($item_id,$booking_date,$beauty_id,$change_type);
	    }
	}
	
	public static  function upsert($item_id,$booking_date,$beauty_id,$change_type = self::CHANGE_TYPE_OFF_ADD)
	{
	    $now_date = date("Y-m-d H:i:s");
        $item = self::where('ITEM_ID',$item_id)->where('BOOKING_DATE',$booking_date)->first();
        if(empty($item))
        {
            if ($change_type == self::CHANGE_TYPE_OFF_ADD)
            {
                self::create([
                    'ITEM_ID'=>$item_id,
                    'BOOKING_DATE'=>$booking_date,
                    'BEAUTY_ID'=>$beauty_id,
                    'QUANTITY'=>1,
                    'CREATE_TIME'=>$now_date,
                    'UPDATE_TIME'=>$now_date,
                ]);
            }
        }
        else 
        {
            if ($change_type == self::CHANGE_TYPE_OFF_ADD)
            {
                self::where('ITEM_ID',$item_id)->where('BOOKING_DATE',$booking_date)->increment('QUANTITY',1,['UPDATE_TIME'=>$now_date]);
            }
            else
            {
                self::where('ITEM_ID',$item_id)->where('BOOKING_DATE',$booking_date)->where('QUANTITY','>',0)->decrement('QUANTITY',1,['UPDATE_TIME'=>$now_date]);
            }
        }
	}
	
	public function isFillable($key)
	{
	    return true;
	}
	
}

