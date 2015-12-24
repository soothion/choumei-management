<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
Use PDO;
use Illuminate\Pagination\AbstractPaginator;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;
use App\BookingOrderItem;
use App\Model\SeedPool;

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
        return $this->hasMany(BeautyOrderItem::class,'order_sn','ORDER_SN');
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
        $base = self::where('ID',$id)->first();
        
        if(empty($base))
        {
            throw new ApiException("预约单{$id} 不存在", ERROR::ORDER_NOT_EXIST);
        }
        
        $base = $base->toArray();
        $ordersn = $base['ORDER_SN'];
        $manager_id = $base['CUSTOMER_SERVICE_ID'];
        $item_fields = ['ORDER_SN','ITEM_ID','ITEM_NAME','AMOUNT','PAYABLE'];
        $beauty_item_fields = ['order_sn','item_id','item_name','norm_id','norm_name','amount','to_pay_amount'];
        $items = BookingOrderItem::where('ORDER_SN',$ordersn)->get($item_fields)->toArray();
        $beauty_items = BeautyOrderItem::where('order_sn',$ordersn)->get($beauty_item_fields)->toArray();
        $fundflows = Fundflow::where('record_no',$ordersn)->get(['record_no','pay_type'])->toArray();
        $payment_log = PaymentLog::where('ordersn',$ordersn)->first(['ordersn','tn','amount']);
        $recommend = RecommendCodeUser::where('user_id',$base['USER_ID'])->whereIn('type',[2,3])->first(['id','user_id','recommend_code']);
        $refund_fields = ['ordersn','user_id','money','opt_user_id','rereason','add_time','opt_time','status','booking_sn','item_type','rereason','other_rereason'];
        $order_refund = OrderRefund::where('ordersn',$ordersn)->where('status',1)->first($refund_fields);
        $base['manager'] = null;
        if(!empty($manager_id))
        {
            $base['manager'] = Manager::getBaseInfo($manager_id);
        }
        $base['help_info'] = self::getHelpUserInfo($base['SUBSTITUTOR'],$base['RECOMMENDER']);
        if(empty($payment_log))
        {
            $payment_log = NULL;
        }
        else
        {
            $payment_log = $payment_log->toArray();
        }
        if(empty($order_refund))
        {
            $order_refund = NULL;
        }
        else 
        {
            $order_refund = $order_refund->toArray();
            $order_refund['manager'] = Manager::getBaseInfo($order_refund['opt_user_id']);
            if ($base['STATUS'] == 'RFN' && $order_refund['status'] == 3) {
                $base['STATUS'] == 'RFE';
            }
            $reason = str_replace(array_keys(Mapping::BeautyRefundRereasonNames()), array_values(Mapping::BeautyRefundRereasonNames()), $order_refund['rereason']);
            $reason = !empty($reason) ? $reason . "," . $order_refund['other_rereason'] : $order_refund['other_rereason'];
            $order_refund['rereason'] = $reason;
            $order_refund['refund_desc'] = $reason;
            $order_refund['add_time'] = date("Y-m-d H:i:s",$order_refund['add_time']);
            $order_refund['opt_time'] = empty($order_refund['opt_time'])?"":date("Y-m-d H:i:s",$order_refund['opt_time']);
            $order_refund['complete_time'] = $order_refund['opt_time'] ;
        }
        $item_amount = 0;
        $is_virtual_beauty = false;
        if(empty($beauty_items))
        {
            $is_virtual_beauty = true;
            $item_ids = array_map("intval",array_column($items, "ITEM_ID"));
            $beauty_items = BookingReceive::getItemNormInfoAtFirst($item_ids);
        }               
        $to_pay_amounts = array_map("floatval",array_column($beauty_items, "to_pay_amount"));
        $item_amount = array_sum($to_pay_amounts);
        if($is_virtual_beauty)
        {
            $beauty_items = [];
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
            'order_refund'=>$order_refund,
        ];
    }
    
    public static function book($params)
    {
        $item_infos = self::getBaseItemInfo($params['item_ids']);
        $exist_item_ids = array_column($item_infos, 'item_id');
        $diff_ids = array_diff($exist_item_ids, $params['item_ids']);
        if(count($diff_ids)>0)
        {
            throw new ApiException("所选(部分)项目不存在!",ERROR::PARAMETER_ERROR);
        }
        $ordersn = self::makeOrdersn();        
        $user_id = self::getUserid($params['phone'], $params['name'], $params['sex']);
        
        self::checkUserRecommendCode($user_id,$params['recomment_code']);
        
        $datetime = date("Y-m-d H:i:s");
        self::AddBookingItem($ordersn,$item_infos);
        $bookingsn = self::makeBookingsn();
        $attr = [
            'ORDER_SN'=>$ordersn,
            'BOOKING_SN'=>$bookingsn,
            'USER_ID'=>$user_id,
            'BOOKING_DATE'=>date("Y-m-d",strtotime($params['date'])),      
            'QUANTITY'=>count($item_infos),
            'AMOUNT'=>Utils::column_sum('price',$item_infos),
            'PAYABLE'=>0,
            'BOOKER_NAME'=>$params['name'],
            'BOOKER_SEX'=>$params['sex']==1?"M":"F",
            'BOOKER_PHONE'=>$params['phone'],
            'STATUS'=>'PYD',
            'CREATE_TIME'=>$datetime,
            'UPDATE_TIME'=>$datetime,
        ];
        if(!empty($params['recomment_code']))
        {
            $attr['RECOMMENDER'] = $params['recomment_code'];
        }
        $id = self::insertGetId($attr);
        $attr['ID'] = $id;
        return $attr;
    }
    
    public static function getCondition($params)
    {
        $select_fields = [
            'cm_booking_order.*',
            'cm_beauty_makeup.id as beauty_makeup_id',
            'cm_order_refund.status as refund_status',
            'cm_fundflow.record_no as record_no',
            'cm_fundflow.pay_type as pay_type',
            'cm_recommend_code_user.recommend_code as recommend_code',
        ];
        
        $base = self::selectRaw(implode(',',$select_fields))->where('booking_order.STATUS', "<>","NEW");
        
        $key = NULL;
        $keyword = NULL;
        $pay_type = NULL;
        $pay_state = NULL;
        if(isset($params['pay_type']) && !empty($params['pay_type']))
        {
            $pay_type = $params['pay_type'];
        }
        if(isset($params['status']) && !empty($params['status']))
        {
            $pay_state = $params['status'];
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
        $beauty_makeup_joined = false;
        $order_refund_joined = false;
        if (! empty($pay_state)) {
            if ($pay_state == "Y") {
                $beauty_makeup_joined = true;
                $base->where("booking_order.STATUS","CSD");
                $base->join('beauty_makeup', function ($join)
                {
                    $join->on('beauty_makeup.booking_id', '=', 'booking_order.ID');
                });
            } elseif ($pay_state == "FAILD") {
                $order_refund_joined = true;
                $base->join('order_refund', function ($join)
                {
                    $join->on('order_refund.ordersn', '=', 'booking_order.ORDER_SN')->where('order_refund.status','=', 3);
                });
            } elseif ($pay_state == "RFD") {
                $base->whereIn('booking_order.STATUS', [
                    'RFD',
                    'RFD-OFL'
                ]);
            } else {
                $base->where('booking_order.STATUS', $pay_state);
            }
        }
        
        if (! $order_refund_joined) {
            
            $base->leftJoin('order_refund', function ($join)
            {
                $join->on('order_refund.ordersn', '=', 'booking_order.ORDER_SN')
                    ->where('order_refund.status', '<>', 2);
            });
        }
        if (! $beauty_makeup_joined) {
            
            $base->leftJoin('beauty_makeup', function ($join)
            {
                $join->on('beauty_makeup.booking_id', '=', 'booking_order.ID');
            });
        }
         
         if($key == 1 && ! empty($keyword))
         {
             $base->where('booking_order.BOOKER_PHONE','like',$keyword);
         }   
         if($key == 2 && ! empty($keyword))
         {
             $base->where('booking_order.BOOKING_SN','like',$keyword);
         } 
            
        if(!empty($pay_type))
        {
            $base->join('fundflow', function ($join) use($pay_type)
            {
                $join->on('booking_order.ORDER_SN', '=', 'fundflow.record_no')->where('fundflow.pay_type', '=', $pay_type);
            });
        }
        else 
        {
            $base->leftJoin('fundflow', function ($join)
            {
                $join->on('booking_order.ORDER_SN', '=', 'fundflow.record_no');
            });
        }
        
        if ($key == 3 && !empty($keyword))
        {
            $base->join('recommend_code_user', function ($join) use($key,$keyword)
            {
                $join->on('booking_order.USER_ID', '=', 'recommend_code_user.user_id')->whereIn('recommend_code_user.type',[2,3])->where('recommend_code_user.recommend_code', 'like', $keyword);
             
            });
        }
        else 
        {
            $base->leftJoin('recommend_code_user', function ($join) 
            {
                $join->on('booking_order.USER_ID', '=', 'recommend_code_user.user_id')->whereIn('recommend_code_user.type',[2,3]);
            });            
        }
       
        $booking_order_item_fields = [
            'ORDER_SN',
            'ITEM_NAME'
        ];
        $beauty_order_item_fields = [
            'order_sn',
            'item_name',
            'norm_name',
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
    
    
    public static function makeOrdersn()
    {     
        return substr(strval(time()),2)."1".mt_rand(1000, 9999);
    }
    

    public static function makeBookingsn()
    {
        $seed = SeedPool::where('TYPE','TKT')->where('STATUS','NEW')->first();
        if(empty($seed))
        {
            throw new ApiException('无可用定妆单号',ERROR::UNKNOWN_ERROR);
        }
        $res = $seed->SEED;
        $seed->update(['STATUS'=>'USD','UPDATE_TIME'=>date("Y-m-d H:i:s")]);
        return $res;
    }
    
    public static function getBaseItemInfo($item_ids)
    {
        $fields = ['item_id','beauty_id','type','name','price','vip_price'];
        $item_info = BeautyItem::whereIn('item_id',$item_ids)->get($fields)->toArray();
        return $item_info;
    }
    
    public static function getUserid($mobilephone,$nickname,$sex)
    {
        $user = User::where('mobilephone',$mobilephone)->first();
        if(!empty($user))
        {
            return $user->user_id;
        }
        else 
        {
            $attr = [
                'mobilephone'=>$mobilephone,            
                'username'=>strval(Username::makeUsername()),
                'password'=>md5(strval(mt_rand(1,99999))),
                'img'=>'',
                'add_time'=>time(),
                'nickname'=>$nickname,
                'sex'=>$sex,
                'area'=>'',
                'couponmoney' => 0,
            ];
            return User::insertGetId($attr);
        }
    }
    
    public static function AddBookingItem($ordersn,$items)
    {
        $datetime = date("Y-m-d H:i:s");
        foreach ($items as $item)
        {
            $attr = [
                'ORDER_SN' => $ordersn,
                'BEAUTY_ID' => $item['beauty_id'],
                'ITEM_TYPE' => $item['type'] == 1?"FFA":"SPM",
                'ITEM_ID' => $item['item_id'],
                'ITEM_NAME' => $item['name'],
                'QUANTITY' => 1,
                'PRICE' => $item['price'],
                'DISCOUNT_PRICE' => $item['vip_price'],
                'AMOUNT' => $item['price'],
                'PAYABLE' => $item['vip_price'],
                'CREATE_TIME' => $datetime,
                'UPDATE_TIME' => $datetime,
            ];
            BookingOrderItem::create($attr);            
        }
    }
    
    public static function checkUserRecommendCode($uid,$recommend_code)
    {
        if(empty($recommend_code))
        {
            return ;
        }
        $oldRecommend = RecommendCodeUser::where('user_id',$uid)->first();
        if(!empty($oldRecommend))
        {
            $code = $oldRecommend->recommend_code;
            throw new ApiException("当前用户已绑定{$code}推荐码!",ERROR::PARAMETER_ERROR);
        }
        $order = self::where("USER_ID",$uid)->whereNotIn("STATUS",['RFD','RFD-OFL'])->where('RECOMMENDER','<>','')->whereNotNull('RECOMMENDER')->first();
        if(!empty($order))
        {
            $code = $order->RECOMMENDER;
            throw new ApiException("当前用户已在使用{$code}推荐码!",ERROR::PARAMETER_ERROR);
        }
        
        if(strlen($recommend_code)>=11)
        {
            $user = User::where('mobilephone',$recommend_code)->first();
            if(empty($user))
            {
                throw new ApiException("推荐用户不存在 {$recommend_code}!",ERROR::PARAMETER_ERROR);
            }
        }
        else
        {
            $dividend = Dividend::where('recommend_code',$recommend_code)->first();
            if(empty($dividend))
            {
                throw new ApiException("推荐码 {$recommend_code} 不可用!",ERROR::PARAMETER_ERROR);
            }
        }
    }
    
    public static function getHelpUserInfo($SUBSTITUTOR,$RECOMMENDER)
    {    
        $res = null; 
        $from = "";
        if(!empty($SUBSTITUTOR))
        {
            $user = User::where('mobilephone',$RECOMMENDER)->first(['nickname','mobilephone']);
          
            if(strlen($RECOMMENDER)<11)
            {
                if(!empty($RECOMMENDER))
                {
                    $salon = Dividend::where('recommend_code',$RECOMMENDER)->LeftJoin("salon",function($join){
                        $join->on('dividend.salon_id','=','salon.salonid');
                    })->first(['salon.salonname']);
                    if(!empty($salon))
                    {
                        $from = $salon->salonname;
                    }
                }
            }
            $res = ['recommend_code'=>$RECOMMENDER,'from'=>$from];
            if(!empty($user))
            {
                $res['mobilephone'] = $user->mobilephone;                
            }
        }
       
        return $res;        
    }

    public function isFillable($key)
    {
        return true;
    }
}