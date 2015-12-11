<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
Use PDO;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;

class BookingCash extends Model
{
    protected $table = 'booking_cash';
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
        $res['manager'] = Manager::getBaseInfo($res['uid']);
        $res['expert'] = Artificer::getBaseInfo($res['expert_uid']);
        $res['assistant'] = Artificer::getBaseInfo($res['assistant_uid']);  
        return $res;
    }
    
    /**
     * 收银
     * @param int $booking_id
     * @param array $params
     */
    public static function cash($booking_id,$params)
    {
        $base = BookingOrder::where('ID',$booking_id)->first();
        if(empty($base))
        {
            throw new ApiException("定妆单{$booking_id}不存在或者已经被删除!", ERROR::ORDER_NOT_EXIST);
        }
        $base= $base->toArray();
        if($base['STATUS'] !== "PYD")
        {
            throw new ApiException("定妆单{$booking_id}状态不正确!", ERROR::ORDER_STATUS_WRONG);
        }
        $ordersn = $base['ORDER_SN'];
        $item_total = 0;
        $book_money = $base['PAYABLE'];
        $receive = BookingReceive::where('booking_id',$booking_id)->first();
        if(empty($receive))
        {
            $item_info = BookingOrderItem::where('ORDER_SN',$ordersn)->selectRaw("SUM(`PAYABLE`) as `to_pay_amount`")->first();
            if(!empty($item_info))
            {
                $item_total = $item_info->to_pay_amount;
            }
            BookingReceive::receive($booking_id, ['uid'=>$params['uid']],true);
        } 
        else 
        {
            $item_info = BeautyOrderItem::where('order_sn',$ordersn)->selectRaw("SUM(`to_pay_amount`) as `to_pay_amount`")->first();
            if(!empty($item_info))
            {
                $item_total = $item_info->to_pay_amount;
            }
        }   
        $params['cash_money'] = isset($params['cash_money'])?$params['cash_money']:0;
        $real_to_pay = bcsub ($item_total, $book_money,2);
        $input_to_pay = bcadd($params['cash_money'],$params['other_money'],2);
        $input_to_pay = bcadd($input_to_pay,$params['deduction_money'],2);
        if($real_to_pay !== $input_to_pay)
        {
            throw new ApiException("收银金额错误，请查询",ERROR::PARAMETER_ERROR);
        }
        $time = time();
        $datetime = date("Y-m-d H:i:s",$time);
        $attr = [
            'booking_id'=>$booking_id,
            'booking_sn'=>$ordersn,
            'order_sn'=>$base['ORDER_SN'],
            'booking_sn'=>$base['BOOKING_SN'],
            'uid'=>$params['uid'],
            'created_at'=>$datetime,
            'pay_type'=>$params['pay_type'],
            'other_money'=>$params['other_money'],
            'cash_money'=>$params['cash_money'],
            'deduction_money'=>$params['deduction_money'],
            'expert_uid'=>$params['specialistId'],
            'assistant_uid'=>$params['assistantId'],
        ];
        self::create($attr);
        
        BookingOrder::where('ID',$booking_id)->update(['STATUS'=>'CSD','CONSUME_TIME'=>$datetime,'UPDATE_TIME'=>$datetime]);
        Order::where('ordersn',$ordersn)->update(['status'=>7]);
        return $base;
    }
    
    public function isFillable($key)
    {
        return true;
    }
}
