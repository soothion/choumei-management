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
        $receive = BookingReceive::where('booking_id',$booking_id)->first();
        if(empty($receive))
        {
            BookingReceive::receive($booking_id, ['uid'=>$params['uid']],true);
        }    
        $ordersn = $base['ORDER_SN'];
        $time = time();
        $datetime = date("Y-m-d H:i:s",$time);
        $attr = [
            'booking_id'=>$booking_id,
            'booking_sn'=>$ordersn,
            'order_sn'=>$base['ORDER_SN'],
            'booking_sn'=>$base['ORDER_SN'],
            'uid'=>$params['uid'],
            'created_at'=>$datetime,
            'pay_type'=>$params['pay_type'],
            'other_money'=>$params['other_money'],
            'cash_money'=>$params['cash_money'],
            'deduction_money'=>$params['deduction_money'],
            'expert_uid'=>$params['expert_uid'],
            'assistant_uid'=>$params['assistant_uid'],
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
