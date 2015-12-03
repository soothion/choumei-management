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
    
    public function beauty_order_item()
    {
        return $this->hasMany(BeautyOrderItem::class,'ORDER_SN','order_sn');
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
        $ordersn = $base['ORDER_SN'];
        $item_fields = ['ORDER_SN','ITEM_ID','ITEM_NAME','AMOUNT','PAYABLE'];
        $beauty_item_fields = ['order_sn','item_id','item_name','amount','to_pay_amount'];
        $items = BookingOrderItem::where('ORDER_SN',$ordersn)->get($item_fields)->toArray();
        $beauty_items = BeautyOrderItem::where('order_sn',$ordersn)->get($beauty_item_fields)->toArray();
        $fundflows = Fundflow::where('record_no',$ordersn)->get(['record_no','pay_type'])->toArray();
        $payment_log = PaymentLog::where('ordersn',$ordersn)->first(['ordersn','tn','amount'])->toArray();
        $recommend = RecommendCodeUser::where('user_id',$base['USER_ID'])->whereIn('type',[2,3])->first(['id','user_id','recommend_code']);
        
        $item_amount = 0;
        if(!empty($beauty_items))
        {
            $to_pay_amounts = array_map("floatval",array_column($beauty_items, "to_pay_amount"));
            $item_amount = array_sum($to_pay_amounts);
        }
        else
        {
            $to_pay_amounts = array_map("floatval",array_column($items, "PAYABLE"));
            $item_amount = array_sum($to_pay_amounts);
        }
        $base['item_amount'] = $item_amount;
        if(empty($recommend))
        {
            $recommend = NULL;
        }
        else 
        {
            $recommend = $recommend->toArray();
        }
        return [
            'order'=>$base,
            'order_item'=>$items,           
            'fundflow'=>$fundflows,
            'payment_log'=>$payment_log,
            'recommend'=>$recommend,
            'beauty_order_item'=>$beauty_items,
            'makeup'=>BeautyMakeup::getByBookingId($id),
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
            'cm_recommend_code_user.recommend_code as recommend_code',
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
          $base->where("booking_order.BOOKING_DATE",">=", $params['min_time']);
        }
        if (isset($params['max_time']) && !empty($params['max_time']) && preg_match("/^\d{4}\-\d{2}\-\d{2}$/", trim($params['max_time']))) {
            $base->where("booking_order.BOOKING_DATE","<=", $params['max_time']); 
        }
         if(!empty($pay_state))
         {
             if($pay_state == "Y")
             {
                 $base->where('booking_order.TOUCHED_UP',$pay_state);
             }
             else 
             {
                 $base->where('booking_order.STATUS',$pay_state);
             }
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
        
        $base->join('recommend_code_user', function ($join) use($key,$keyword)
        {
            $join->on('booking_order.USER_ID', '=', 'recommend_code_user.user_id')->whereIn('type',[2,3]);
            if ($key == 3 && !empty($keyword)) {
                $join->where('recommend_code_user.recommend_code', 'like', $keyword);
            }
        });
        
        $booking_order_item_fields = [
            'ORDER_SN',
            'ITEM_NAME'
        ];
        $beauty_order_item_fields = [
            'order_sn',
            'item_name'
        ];
        
        $base->with([
            'booking_order_item' => function ($q) use($booking_order_item_fields)
            {
                $q->get($booking_order_item_fields);
            },
            'beauty_order_item' => function ($q) use($beauty_order_item_fields)
            {
                $q->get($beauty_order_item_fields);
            },
        ]);
        $base->orderBy('booking_order.CREATE_TIME', 'DESC');
        return $base;
    }
}