<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\SalonItem;
use Illuminate\Support\Facades\Log;
class ItemShelvesSeeder extends Seeder
{
    /**
     * 每分钟执行一次 上下架项目
     * Run the database seeds.
     *
     * @return void
     * 
     * php artisan db:seed --class=ItemShelvesSeeder  
     */
    public function run()
    {
    	$time = time();
    	$added = SalonItem::where(['status'=>2])->where('timingAdded','<=',$time)->where('timingAdded','!=',0)->whereRaw("(timingShelves > '{$time}' or timingShelves =0 )")->select(['itemid'])->get()->toArray();

    	if($added)
    	{
    		SalonItem::where(['status'=>2])->where('timingAdded','<=',$time)->where('timingAdded','!=',0)->whereRaw("(timingShelves > '{$time}' or timingShelves =0 )")->update(['status'=>1,'up_time'=>$time]);
    		Log::info('定时上架: '.json_encode($added));
    	}
    		
    	$shelves = SalonItem::where(['status'=>1])->where('timingShelves','<=',$time)->where('timingShelves','!=',0)->select(['itemid'])->get()->toArray();
    	if($shelves)
    	{
    		SalonItem::where(['status'=>1])->where('timingShelves','<=',$time)->where('timingShelves','!=',0)->update(['status'=>2]);
    		Log::info('定时下架: '.json_encode($shelves));
    	}
    	Log::info('定时上下架执行记录 ');
    	echo 'ok';
    }
 
}
