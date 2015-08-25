<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis as Redis;
use App\Salon;

class Rebate extends Model {

	protected $table = 'rebate';

	protected $fillable = ['salon_id', 'author', 'sn', 'amount', 'status', 'start_at', 'end_at', 'confirm_at'];



    public function salon(){
        return $this->belongsTo('App\Salon');
    }    

	public function getSn(){
		$redis = Redis::connection();
		$key = 'FY-'.date('ymd');
		if($redis->get($key)==FALSE)
			$redis->setex($key,3600*24,0);
		$sn = $redis->incr($key);
		$sn = str_pad($sn, 5,'0',STR_PAD_LEFT);
		$sn = $key.$sn;
		return $sn;
	}

	public function getName(){
		$redis = Redis::connection();
		$key = 'rebate-'.date('ymd');
		if($redis->get($key)==FALSE)
			$redis->setex($key,3600*24,0);
		$name = $redis->incr($key);
		$name = str_pad($name, 3,'0',STR_PAD_LEFT);
		return 'rebate'.$name;
	}

	public function getSalonid($sn){
		$salon = Salon::where('sn',$sn)->select('salonid')->first();
		if($salon)
			return $salon->salonid;
		return 0;
	}

}
