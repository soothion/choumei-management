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
        
    }
    
    public function isFillable($key)
    {
        return true;
    }
}
