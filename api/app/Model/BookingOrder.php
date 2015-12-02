<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
Use PDO;
use Illuminate\Pagination\AbstractPaginator;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;
use App\BookingOrderItem;

class BookingOrder extends Model
{
    protected $table = 'booking_order';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    
    public function booking_order_item()
    {
        return $this->hasMany(BookingOrderItem::class,'ORDER_SN','ORDER_SN');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class,'USER_ID','user_id');
    }
    
    public function bill()
    {
        return $this->belongsTo(BookingBill::class,'ID','booking_id');
    } 
    
    public function cash()
    {
        return $this->belongsTo(BookingCash::class,'ID','booking_id');
    }
    
    public function receive()
    {
        return $this->belongsTo(BookingReceive::class,'ID','booking_id');
    }
    
    public function salon_refund()
    {
        return $this->belongsTo(BookingSalonRefund::class,'ID','booking_id');
    }
    
    public static function search($params)
    {
        $bases = self::getCondition($params);
        // 页数
        $page = isset($params['page']) ? max(intval($params['page']), 1) : 1;
        $size = isset($params['page_size']) ? max(intval($params['page_size']), 1) : 20;
        AbstractPaginator::currentPageResolver(function () use($page)
        {
            return $page;
        });
        $res = $bases->paginate($size)->toArray();
        unset($res['next_page_url']);
        unset($res['prev_page_url']);
        return $res;
    }
    
    public static function detail($id)
    {
        $base = self::where('ID',$id)->with(['user'=>function($q){
            $q->get(['user_id','nickname','sex']);
        }])->first();
        
        if(empty($base))
        {
            throw new ApiException("预约单{$id} 不存在", ERROR::ORDER_NOT_EXIST);
        }
        
        $base = $base->toArray();
        
        $item_fields = ['ORDER_SN','ITEM_ID','ITEM_NAME'];
        $items = BookingOrderItem::where('ORDER_SN',$base['ORDER_SN'])->get($item_fields)->toArray();
        $ordersn = $base['ORDER_SN'];
        $fundflows = Fundflow::where('record_no',$ordersn)->get(['record_no','pay_type'])->toArray();
        $payment_log = PaymentLog::where('ordersn',$ordersn)->first(['ordersn','tn','amount'])->toArray();
        return [
            'order'=>$base,
            'order_item'=>$items,
            'fundflow'=>$fundflows,
            'payment_log'=>$payment_log,
            'booking_bill'=>BookingBill::getByBookingId($id),
            'booking_cash'=>BookingCash::getByBookingId($id),
            'booking_receive'=>BookingReceive::getByBookingId($id),
            'booking_salon_refund'=>BookingSalonRefund::getByBookingId($id),
        ];
    }
    
    public static function getCondition($params)
    {
        $select_fields = [
            'cm_booking_order.*',
            'cm_fundflow.record_no as record_no',
            'cm_fundflow.pay_type as pay_type',
        ];
        
        $base = self::selectRaw(implode(',',$select_fields));
        
        $key = NULL;
        $keyword = NULL;
        $pay_type = NULL;
        $pay_state = NULL;
        if(isset($params['pay_type']) && !empty($params['pay_type']))
        {
            $pay_type = $params['pay_type'];
        }
        if(isset($params['pay_state']) && !empty($params['pay_state']))
        {
            $pay_state = $params['pay_state'];
        }
        if (isset($params['key']) && ! empty($params['key']) && isset($params['keyword']) && ! empty(trim($params['keyword'])))
        {
            $key = $params['key'];
            $keyword = Utils::getSearchWord($params['keyword']);
        }
        
        if (isset($params['min_time']) && !empty($params['min_time']) && preg_match("/^\d{4}\-\d{2}\-\d{2}$/", trim($params['min_time']))) {
            $min_time = $params['min_time'];
            $base->whereRaw("(`cm_booking_order`.`UPDATED_BOOKING_DATE` is NULL AND `cm_booking_order`.`BOOKING_DATE` >= '{$min_time}') OR (`cm_booking_order`.`UPDATED_BOOKING_DATE` is NOT NULL AND `cm_booking_order`.`UPDATED_BOOKING_DATE` >= '{$min_time}')");
        }
        if (isset($params['max_time']) && !empty($params['max_time']) && preg_match("/^\d{4}\-\d{2}\-\d{2}$/", trim($params['max_time']))) {
            $max_time = $params['max_time'];
            $base->whereRaw("(`cm_booking_order`.`UPDATED_BOOKING_DATE` is NULL AND `cm_booking_order`.`BOOKING_DATE` <= '{$max_time}') OR (`cm_booking_order`.`UPDATED_BOOKING_DATE` is NOT NULL AND `cm_booking_order`.`UPDATED_BOOKING_DATE` <= '{$max_time}')");
         }
         if(!empty($pay_state))
         {
             $base->where('booking_order.STATUS',$pay_state);
         }
         
         if($key == 1 && ! empty($keyword))
         {
             $base->where('booking_order.BOOKER_PHONE','like',$keyword);
         }   
         if($key == 2 && ! empty($keyword))
         {
             $base->where('booking_order.BOOKING_SN','like',$keyword);
         }    
        
        $base->join('fundflow', function ($join) use($pay_type)
        {
            $join->on('booking_order.ORDER_SN', '=', 'fundflow.record_no');
            if (! empty($pay_type)) {
                $join->where('fundflow.pay_type', '=', $pay_type);
            }
        });
        
        $booking_order_item_fields = [
            'ORDER_SN',
            'ITEM_NAME'
        ];
        
        $base->with([
            'booking_order_item' => function ($q) use($booking_order_item_fields)
            {
                $q->get($booking_order_item_fields);
            }
        ]);
        $base->orderBy('booking_order.CREATE_TIME', 'DESC');
        return $base;
    }
}