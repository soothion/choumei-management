<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

use App\Exceptions\ApiException;
use App\Exceptions\ERROR;

class SeedPool extends Model
{
    protected $table = 'seed_pool';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    
    /**
     * 根据条件，获取所需要的赠送券
     */
    public static function getArticleTicketFromPool($limit=1,$orderby = array('ID' => 'desc')){
        $where = array('TYPE' => 'GSN','STATUS' => 'NEW');
        $res =  self::select('SEED as articleTicket')
               ->where($where)
               ->orderby($orderby)
               ->limit($limit)
               ->get()->toArray();
        if(empty($res)){
            throw new ApiException('无法获取定妆赠送活动券');    
        }
        if(count($res) != $limit){
            throw new ApiException('臭美池中的券不够'); 
        }
        return $res;
    }
    

    /***
     * 获取预约订单号，即臭美券号
     */
    public static function getReservateSnFromPool(){
        $where = array('TYPE' => 'TKT','STATUS' => 'NEW');
        $res =  self::select('SEED as reservateSn')
               ->where($where)->first();
        if(empty($res)){
            throw new ApiException('无法获取定妆预约券号');    
        }
        return $res->toArray();
    }
}
