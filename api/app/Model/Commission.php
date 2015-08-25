<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis as Redis;
use App\Salon;

class Commission extends Model {

	protected $table = 'commission';

	protected $fillable = ['id', 'ordersn', 'sn', 'amount', 'created_at', 'updated_at'];

    public function salon(){
        return $this->belongsTo('App\Salon');
    }    
    
	public function getSn(){
		$redis = Redis::connection();
		$key = 'YJ-'.date('ymd');
		if($redis->get($key)==FALSE)
			$redis->setex($key,3600*24,0);
		$sn = $redis->incr($key);
		$sn = str_pad($sn, 5,'0',STR_PAD_LEFT);
		$sn = $key.$sn;
		return $sn;
	}

	public function getSalonid($sn){
		$salon = Salon::where('sn',$sn)->select('salonid')->first();
		if($salon)
			return $salon->salonid;
		return 0;
	}

}
