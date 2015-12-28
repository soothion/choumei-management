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
        $old_booking_date = empty($base['UPDATED_BOOKING_DATE'])?$base['BOOKING_DATE']:$base['UPDATED_BOOKING_DATE'];
        $update_booking_date = isset($params['update_booking_date'])?date("Y-m-d",strtotime($params['update_booking_date'])):NULL;
        // 任何状态下都允许接待
//         if($base['STATUS'] !== "PYD")
//         {
//             throw new ApiException("定妆单{$id}状态不正确!", ERROR::ORDER_STATUS_WRONG);
//         }
        $ordersn = $base['ORDER_SN'];
        
        $datetime = date("Y-m-d H:i:s");
        $attr = [
            'booking_id'=>$id,
            'order_sn'=>$ordersn,
            'booking_sn'=>empty($base['BOOKING_SN'])?"":$base['BOOKING_SN'],
            'uid'=>$params['uid'],
            'created_at'=>$datetime,
        ];
        $base_update_attr = ['COME_SHOP'=>'COME'];
        if(!empty($update_booking_date))
        {
            $attr['update_booking_date'] = $update_booking_date;
            $base_update_attr['UPDATED_BOOKING_DATE'] = $update_booking_date;
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
        
        $arrive_date = date("Y-m-d",strtotime($attr['arrive_at']));        
        
        if($copy_from_base)
        {
            self::makeFromOrigin($ordersn,$params['uid']);
        }
        else
        {
            self::deleteOldItems($ordersn,$old_booking_date,$arrive_date);
            $id_infos = self::getItemIdsNormIds($params['item_ids']);
            $item_infos = self::getItemNormInfo($id_infos);
            self::insertNewItems($ordersn, $item_infos,$update_booking_date,$arrive_date);
        }
        
       
        BookingReceive::where("booking_id",$id)->delete();
        BookingOrder::where('ID',$id)->update($base_update_attr);
        BookingReceive::create($attr);
        return $base;
    }
    
    public static function makeFromOrigin($ordersn,$uid)
    {
        $items = BookingOrderItem::where('ORDER_SN',$ordersn)->get(['ITEM_ID'])->toArray();
        $item_ids = array_column($items, "ITEM_ID");
        $item_infos = self::getItemNormInfoAtFirst($item_ids);
        self::deleteOldItems($ordersn);
        self::insertNewItems($ordersn, $item_infos);
    }
    
    public static function deleteOldItems($ordersn,$old_date=NULL,$arrive_date=NULL)
    {
        $items = BeautyOrderItem::where('order_sn',$ordersn)->get(['item_id'])->toArray();
        $item_ids = array_column($items, 'item_id');
        if(!empty($old_date))
        {           
            BookingCalendar::change_items_date($item_ids, $old_date,BookingCalendar::CHANGE_TYPE_OF_DEL);
        }
        if(!empty($arrive_date))
        {
            BookingCalendar::arrive($item_ids, $arrive_date,BookingCalendar::CHANGE_TYPE_OF_DEL);
        }
        BeautyOrderItem::where('order_sn',$ordersn)->delete();
    }  
      
    public static function insertNewItems($ordersn,$item_infos,$update_booking_date=NULL,$arrive_date=NULL)
    {      
        if(count($item_infos)<1)
        {
            return ;
        }
        $datetime = date("Y-m-d H:i:s");   
        if(!empty($update_booking_date))
        {
            BookingCalendar::change_items_date(array_column($item_infos, 'item_id'), $update_booking_date);
        }
        if(!empty($arrive_date))
        {
            BookingCalendar::arrive(array_column($item_infos, 'item_id'), $arrive_date);
        }
        foreach ($item_infos as $item)
        {       
            $attrs = [];
            $attrs['order_sn'] = $ordersn;
            $attrs['created_at'] = $datetime;
            $attrs['beauty_id'] = $item['beauty_id'];
            $attrs['item_id'] = $item['item_id'];
            $attrs['item_name'] = $item['item_name'];
            $attrs['norm_id'] = $item['norm_id'];
            $attrs['norm_name'] = $item['norm_name'];
            $attrs['quantity'] = 1;
            $attrs['price'] = $item['price'];
            $attrs['discount_price'] = $item['discount_price'];
            $attrs['amount'] = $item['amount'];
            $attrs['to_pay_amount'] = $item['to_pay_amount'];            
            BeautyOrderItem::create($attrs);  
        }
    }
    
    public static function getItemIdsNormIds($itemStr)
    {
        $res = [];
        foreach ($itemStr as $idStr)
        {
            $ids = explode("_", $idStr);
            $item_id = array_shift($ids);
            if(count($ids)>0)
            {
                foreach($ids as $id)
                {
                    $res[] = ['item_id'=>$item_id,'norm_id'=>$id];
                }
            }
            else 
            {
                $res[] = ['item_id'=>$item_id,'norm_id'=>null];
            }
        }
        return $res;
    }
    
    public static function getItemNormInfo($ids_info)
    {
        $res = [];
        $item_ids = array_map("intval",array_column($ids_info, "item_id"));
        $norm_ids = array_map("intval",array_column($ids_info, "norm_id"));
        $item_infos = BeautyItem::whereIn('item_id',$item_ids)->get(['item_id','beauty_id','name','price','vip_price'])->toArray();
        $item_info_idx = Utils::column_to_key("item_id",$item_infos);
        $norm_infos = BeautyItemNorm::whereIn('id',$norm_ids)->get(['id','norm','price','vip_price'])->toArray();
        $norm_info_idx = Utils::column_to_key("id",$norm_infos);
        foreach($ids_info as $info)
        {
            $item_id = $info['item_id'];
            $norm_id = $info['norm_id'];
            if(!isset($item_info_idx[$item_id]))
            {
                throw new ApiException("项目 {$item_id} 不存在或者已删除!",ERROR::BEAUTY_ITEM_ERROR);
            }
            $tmp =
            [
                'item_id'=>$item_id,
                'item_name'=>$item_info_idx[$item_id]['name'],
                'beauty_id'=>$item_info_idx[$item_id]['beauty_id'],
            ];
            if(!empty($norm_id) && isset($norm_info_idx[$norm_id]))
            {
                $tmp['norm_id'] = $norm_info_idx[$norm_id]['id'];
                $tmp['norm_name'] = $norm_info_idx[$norm_id]['norm'];
                $tmp['price'] = $norm_info_idx[$norm_id]['price'];
                $tmp['discount_price'] = $norm_info_idx[$norm_id]['vip_price'];
                $tmp['amount'] = $norm_info_idx[$norm_id]['price'];
                $tmp['to_pay_amount'] = $norm_info_idx[$norm_id]['vip_price'];
            }
            else 
            {
                $tmp['norm_id'] = 0;
                $tmp['norm_name'] = "";    
                $tmp['price'] =$item_info_idx[$item_id]['price'];
                $tmp['discount_price'] = $item_info_idx[$item_id]['vip_price'];
                $tmp['amount'] =$item_info_idx[$item_id]['price'];
                $tmp['to_pay_amount'] = $item_info_idx[$item_id]['vip_price'];
            }
            $res[] = $tmp;
        }
        return $res;
    }
    
    public static function getItemNormInfoAtFirst($item_ids)
    {
        $res = [];
        $items = BeautyItem::whereIn('item_id',$item_ids)->get(['item_id','beauty_id','name','price','vip_price'])->toArray();
        $norms = BeautyItemNorm::whereIn('item_id',$item_ids)->groupBy('item_id')->orderBy('id','ASC')->get(['id','item_id','norm','price','vip_price'])->toArray();
        $norm_info_idx = Utils::column_to_key("item_id",$norms);
        $item_info_idx = Utils::column_to_key("item_id",$items);
        foreach($item_ids as $item_id)
        {
           if(!isset($item_info_idx[$item_id]))
           {
               continue;
           }
            $info = $item_info_idx[$item_id];
            $tmp =
            [
                'item_id'=>$item_id,
                'item_name'=>$info['name'],
                'beauty_id'=>$info['beauty_id'],
                'norm_id' => 0,
                'norm_name' => "",
                'price' => $info['price'],
                'discount_price' => $info['vip_price'],
                'amount' => $info['price'],
                'to_pay_amount' => $info['vip_price'],
            ];
            if( isset($norm_info_idx[$item_id]))
            {
                $tmp['norm_id'] = $norm_info_idx[$item_id]['id'];
                $tmp['norm_name'] = $norm_info_idx[$item_id]['norm'];
                $tmp['price'] = $norm_info_idx[$item_id]['price'];
                $tmp['discount_price'] = $norm_info_idx[$item_id]['vip_price'];
                $tmp['amount'] = $norm_info_idx[$item_id]['price'];
                $tmp['to_pay_amount'] = $norm_info_idx[$item_id]['vip_price'];
            }
            $res[] = $tmp;
        }

        return $res;
    }
    
    
    public function isFillable($key)
    {
        return true;
    }
}
