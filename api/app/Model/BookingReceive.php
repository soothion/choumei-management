<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
Use PDO;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;

class BookingReceive extends Model
{
    protected $table = 'booking_receive';
    protected $primaryKey = 'id';
    public $timestamps = false;
    

    public static function getByBookingId($booking_id,$fields=['*'])
    {
        $base = self::where('booking_id',$booking_id)->first($fields);
        if(empty($base))
        {
            return null;
        }
        $res = $base->toArray();
        $res['manager']=Manager::getBaseInfo($res['uid']);
        return $res;
    }
    
    /**
     * 
     * @param int $id
     * @param array $params
     * @param string $copy_from_base
     */
    public static function receive($id,$params,$copy_from_base=false)
    {
        $base = BookingOrder::where('ID',$id)->first();
        if(empty($base))
        {
            throw new ApiException("定妆单{$id}不存在或者已经被删除!", ERROR::ORDER_NOT_EXIST);
        }
        $base= $base->toArray();
        if($base['STATUS'] !== "PYD")
        {
            throw new ApiException("定妆单{$id}状态不正确!", ERROR::ORDER_STATUS_WRONG);
        }
        $ordersn = $base['ORDER_SN'];
        if($copy_from_base)
        {
            self::makeFromOrigin($ordersn,$params['uid']);
        }
        else
        {
            self::deleteOldItems($ordersn);        
            self::insertNewItems($ordersn, $params['item_ids']);
        }
        
        $datetime = date("Y-m-d H:i:s");
        $attr = [
            'booking_id'=>$id,
            'order_sn'=>$ordersn,
            'booking_sn'=>empty($base['BOOKING_SN'])?"":$base['BOOKING_SN'],
            'uid'=>$params['uid'],
            'created_at'=>$datetime,
        ];
        if(isset($params['update_booking_date']))
        {
            $attr['update_booking_date'] = date("Y-m-d",strtotime($params['update_booking_date']));
        }
        if(isset($params['remark']))
        {
            $attr['remark'] = $params['remark'];
        }
        if(isset($params['arrive_at']))
        {
            $attr['arrive_at'] = date("Y-m-d H:i:s",strtotime($params['arrive_at']));
        }
        else 
        {
            $attr['arrive_at'] = $datetime;
        }  
        BookingReceive::where("booking_id",$id)->delete();
        BookingReceive::create($attr);
        return $base;
    }
    
    public static function makeFromOrigin($ordersn,$uid)
    {
        $items = BookingOrderItem::where('ORDER_SN',$ordersn)->get(['ITEM_ID'])->toArray();
        $item_ids = array_column($items, "ITEM_ID");
        self::deleteOldItems($ordersn);
        self::insertNewItems($ordersn, $item_ids);
    }
    
    public static function deleteOldItems($ordersn)
    {
        BeautyOrderItem::where('order_sn',$ordersn)->delete();
    }  
      
    public static function insertNewItems($ordersn,$item_ids)
    {      
        if(count($item_ids)<1)
        {
            return ;
        }
        $datetime = date("Y-m-d H:i:s");        
        $origin_items = BeautyItem::whereIn('item_id',$item_ids)->get(['item_id','beauty_id','name','price','vip_price'])->toArray();   
      
        foreach ($origin_items as $item)
        {       
            $attrs = [];
            $attrs['order_sn'] = $ordersn;
            $attrs['created_at'] = $datetime;
            $attrs['beauty_id'] = $item['beauty_id'];
            $attrs['item_id'] = $item['item_id'];
            $attrs['item_name'] = $item['name'];
            $attrs['quantity'] = 1;
            $attrs['price'] = $item['price'];
            $attrs['discount_price'] = $item['vip_price'];
            $attrs['amount'] = $item['price'];
            $attrs['to_pay_amount'] = $item['vip_price'];            
            BeautyOrderItem::create($attrs);  
        }
    }
    
    public function isFillable($key)
    {
        return true;
    }
}
