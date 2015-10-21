<?php
/**
 * 用户表
 * @author Vincent
 */
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Redis as Redis;
use Illuminate\Database\Eloquent\SoftDeletes;


const FIRST_KEY = 'recent.first.user';
const REGISTER_KEY = 'recent.register.user';

class User extends  Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'user';
    protected $primaryKey = 'user_id'; 
    protected $fillable = ['username','nickname','password','email','img','add_time','last_time','sex','hair_type','birthday','area','growth','grade','mobilephone','costpwd','companyId'];
    public $timestamps = false;


    public static function getQueryByParam($param=[]){
        $query = Self::leftJoin('company_code','user.companyId','=','company_code.companyId')
        	->leftJoin('recommend_code_user','user.user_id','=','recommend_code_user.user_id')
            ->leftJoin('dividend','dividend.recommend_code','=','recommend_code_user.recommend_code');

        if(!empty($param['username'])){
        	$username = '%'.$param['username'].'%';
        	$query = $query->where('username','like',$username);
        }

        if(!empty($param['mobilephone'])){
        	$query = $query->where('mobilephone','=',$param['mobilephone']);
        }

        if(!empty($param['companyCode'])){
        	$query = $query->where('company_code.code','=',$param['companyCode']);
        }

        if(!empty($param['recommendCode'])){
        	$query = $query->where('recommend_code_user.recommend_code','=',$param['recommendCode']);
        }

        if(!empty($param['sex'])){
        	$query = $query->where('sex','=',$param['sex']);
        }

        if(!empty($param['start_at'])){
        	$start_at = strtotime($param['start_at']);
        	$query = $query->where('user.add_time','>=',$start_at);
        }

        if(!empty($param['end_at'])){
        	$end_at = strtotime($param['end_at'])+3600*24;
        	$query = $query->where('user.add_time','<=',$end_at);
        }

        if(!empty($param['area'])){
        	$area = '%'.$param['area'].'%';
        	$query = $query->where('area','like',$area);
        }

        //排序
    	$sort_key = empty($param['sort_key'])?'user.user_id':$param['sort_key'];
    	$sort_type = empty($param['sort_type'])?'DESC':$param['sort_type'];
        $query = $query->orderBy($sort_key,$sort_type);
 
        return $query;
    }

    public static function getLevel($growth=0){
    	$level = DB::table('user_level')
    		->where('growth','<=',$growth)
    		->orderBy('level','DESC')
    		->pluck('level');
    	return $level;
    }

    public static function getSex($sex=0){
    	$sex = intval($sex);
    	$mapping = [
    		0=>'未知',
    		1=>'女',
    		2=>'男'
    	];
    	if(empty($mapping[$sex]))
    		return '未知';
    	return $mapping[$sex];
    }
    
    public static function getUsersByIds($uids) {
        $users = self::whereIn("user_id", $uids)->get();
        return $users;
    }

    public static function getUserById($uid) {
        $user = Self::getQuery()->where("user_id", "=", $uid)->get();
        if (empty($user)) {
            return [];
        } else {
            return $user[0];
        }
    }

    //获取最近15天每天的用户注册数
    public static function getRecentRegister(){
        $redis = Redis::connection();
        $result = [];
        for ($i=14; $i >= 0; $i--){ 
            //当天的数据实时统计
            if($i==0){
                $day = date('Y-m-d',strtotime('today'));
                $count = Self::getRegisterByDay($day);
                $result[$day] = $count;
            }
            //昨天或以前的数据从redis取   
            else{
                $day = date('Y-m-d',strtotime("- $i day"));
                $count = $redis->hGet(REGISTER_KEY,$day);
                if($count == NULL){
                    $count = Self::getRegisterByDay($day);
                    $redis->hSet(REGISTER_KEY,$day,$count);
                }
                $result[$day] = intval($count);
            }      
        }
        //删除16天前的记录,控制hash不超过16个元素
        $last = date('Y-m-d',strtotime('-16 day'));
        $redis->hDel(REGISTER_KEY,$last);
        return $result;
    }


    //获取最近15天每天的首单用户数
    public static function getRecentFirst(){
        $redis = Redis::connection();
        $result = [];
        for ($i=14; $i >= 0; $i--){ 
            //当天的数据实时统计
            if($i==0){
                $day = date('Y-m-d',strtotime('today'));
                $count = Self::getFirstByDay($day);
                $result[$day] = $count;
            }
            //昨天或以前的数据从redis取   
            else{
                $day = date('Y-m-d',strtotime("- $i day"));
                $count = $redis->hGet(FIRST_KEY,$day);
                if($count == NULL){
                    $count = Self::getFirstByDay($day);
                    $redis->hSet(FIRST_KEY,$day,$count);
                }
                $result[$day] = intval($count);
            }      
        }
        //删除16天前的记录,控制hash不超过15个元素
        $last = date('Y-m-d',strtotime('-16 day'));
        $redis->hDel(FIRST_KEY,$last);
        return $result;
    }

    public static function getFirstByDay($day){
            $current = strtotime($day);
            $next = $current+3600*24;
            $users = Order::whereBetween('use_time',[$current,$next])->lists('user_id');
            $orders = Order::whereIn('user_id',$users)->where('use_time','!=',0)->orderBy('use_time','desc')->groupBy('user_id')->lists('orderid');
            $count = count($orders);
            return $count;
    }

    public static function getRegisterByDay($day){
        $current = strtotime($day);
        $next = $current+3600*24;
        $count = User::withTrashed()->whereBetween('add_time',[$current,$next])->count();
        return $count;
    }



}

