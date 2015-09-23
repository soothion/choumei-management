<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class SalonWorks extends Model {

	protected $table = 'salon_works';
	
	public $timestamps = false;
	
	/*
	 * 分类获取店铺图集
	 * */
	public static function getSalonWorks($salonid,$type)
	{
		if(!$type) return false;
		return self::where(['flags'=>$type,'salonid'=>$salonid])->select('worksid','imgsrc','flags')->orderBy('worksid','desc')->get()->toArray();
	}
	
	public static function saveImgs($salonid,$type,$imgArr) 
	{
		if(!$imgArr || !$salonid || !$type) return false;
		self::where(['flags'=>$type,'salonid'=>$salonid])->delete();
		krsort($imgArr);
		foreach($imgArr as $key=>$val)
		{
			$data = [
						'salonid' => $salonid,
						'imgsrc'  => $val,
						'flags'   => $type,
						'add_time' => time(),
					];
			self::insertGetId($data);
		}
	}

}
