<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
Use PDO;

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
        $res['manager'] = self::getManager($res['uid']);
        $res['expert'] = self::getExpert($res['expert_uid']);
        $res['assistant'] = self::getAssistant($res['assistant_uid']);
        return $res;
    }
    
    public static function getManager($uid)
    {
        $base = Manager::where('id',$uid)->first(['id','name']);
        if(empty($base))
        {
            return null;
        }
        return $base->toArray();
    }
    
    public static function getExpert($uid)
    {
        $base = Artificer::where('artificer_id',$uid)->first(['artificer_id','name','number']);
        if(empty($base))
        {
            return null;
        }
        return $base->toArray();
    }
    
    public static function getAssistant($uid)
    {
        $base = Artificer::where('artificer_id',$uid)->first(['artificer_id','name','number']);
        if(empty($base))
        {
            return null;
        }
        return $base->toArray();
    }
    
    /**
     * 收银
     * @param int $booking_id
     * @param array $params
     */
    public static function cash($booking_id,$params)
    {
        
    }
    
    public function isFillable($key)
    {
        return true;
    }
}
