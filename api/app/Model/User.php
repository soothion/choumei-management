<?php
/**
 * 用户表
 * @author Vincent
 */
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class User extends  Model
{
    protected $table = 'user';
    protected $primaryKey = 'user_id'; 
    protected $fillable = ['username','nickname','password','email','img','add_time','last_time','sex','birthday','area','growth','mobilephone','costpwd','companyId'];
    public $timestamps = false;

    public static function getQueryByParam($param=[]){
        $query = Self::leftJoin('company_code','user.companyId','=','company_code.companyId')
        	->leftJoin('recommend_code_user','user.user_id','=','recommend_code_user.user_id');

        if(!empty($param['username'])){
        	$username = '%'.$param['username'].'%';
        	$query = $query->where('username','like',$username);
        }

        if(!empty($param['mobilephone'])){
        	$query = $query->where('mobilephone','=',$param['mobilephone']);
        }

        if(!empty($param['companyCode'])){
        	$query = $query->where('company_code_user.companyCode','=',$param['companyCode']);
        }

        if(!empty($param['recommendCode'])){
        	$query = $query->where('recommend_code_user.recommend_code','=',$param['recommendCode']);
        }

        if(!empty($param['sex'])){
        	$query = $query->where('sex','=',$param['sex']);
        }

        if(!empty($param['start_at'])){
        	$start_at = strtotime($param['start_at']);
        	$query = $query->where('add_time','>=',$start_at);
        }

        if(!empty($param['end_at'])){
        	$end_at = strtotime($param['end_at'])+3600*24;
        	$query = $query->where('add_time','>=',$end_at);
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
    		1=>'男',
    		2=>'女'
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
}

