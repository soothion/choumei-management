<?php

namespace App\Model;

use App\Artificer;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Database\Eloquent\Model;
use App\Model\Present;
use Log;

use Illuminate\Support\Facades\Redis as Redis;

use App\Exceptions\ApiException;
use App\Exceptions\ERROR;

class PresentArticleCode extends Model
{
    protected $table = 'present_article_code';
    protected $primaryKey = 'article_code_id';
    public $timestamps = false;
    
    private static $presentArticleCodeField = array(
            'present_article_code.article_code_id as articleCodeId',
            'present_article_code.present_id as presentId',
            'present_article_code.reservate_sn as reservateSn',
            'present_article_code.ordersn as orderSn',
             'present_article_code.item_id as itemId',
            'present_article_code.code as ticketCode',
            'present_article_code.status as ticketStatus',
            'present_article_code.mobilephone as mobilephone',
            'present_article_code.recommend_code as recommendCode',
            'present_article_code.present_type as presentType',
            'present_article_code.manager_id as managerId',
            'present_article_code.specialist_id as specialistId',
            'present_article_code.assistant_id as assistantId',
            'present_article_code.expire_at as expireTime',
            'present_article_code.use_time as useTime',
            'present_article_code.record_time as recordTime',
            'present_article_code.created_at as createTime',
            'beauty_item.name as itemName',
    );
    public static function getPresentList($mobilephone,$reservateSn,$recommendCode,$ticketCode,$startTime,$endTime,$presentType,$ticketStatus,$page,$pageSize){
        $field = self::$presentArticleCodeField;
        $query = self::select($field)
                ->leftJoin('beauty_item', 'present_article_code.item_id', '=', 'beauty_item.item_id');
        if($mobilephone){
            $query = $query->where('mobilephone','like','%'.$mobilephone.'%');
        }
        if($reservateSn){
            $query = $query->where('reservate_sn','like','%'.$reservateSn.'%');
        }
        if($recommendCode){
            $query = $query->where('recommend_code','like','%'.$recommendCode.'%');
        }
        if($ticketCode){
            $query = $query->where('code','like','%'.$ticketCode.'%');
        }
        if($startTime){
            $query = $query->where('present_article_code.created_at','>=',$startTime);
        }
        if($endTime){
            $query = $query->where('present_article_code.created_at','<=',$endTime);
        }
        if($presentType){
            $query = $query->where('present_type','=',$presentType);
        }
        if($ticketStatus){
            $query = $query->where('status','=',$ticketStatus);
        }
        
        $query = $query->orderBy('present_article_code.created_at','desc');
        //手动设置页数
        AbstractPaginator::currentPageResolver(function() use ($page) {
              return $page;
        });      
        $presentListInfo = $query->paginate($pageSize)->toArray();
        unset($presentListInfo['next_page_url']);
        unset($presentListInfo['prev_page_url']);
        return $presentListInfo;
    }
    
    public static function getPresentListInfoByWhere($where){
        $field1 = self::$presentArticleCodeField;
        $field2 = array('present.name as articleName','present.verify_status as verifyStatus','managers.name as managerName','artificer.name as specialistName', 'artificer.number as specialistNumber');
        $field = array_merge($field1,$field2);
        $query = self::select($field)
                ->leftJoin('beauty_item', 'present_article_code.item_id', '=', 'beauty_item.item_id')
                ->leftJoin('present', 'present_article_code.present_id', '=', 'present.present_id')
                ->leftJoin('managers', 'present_article_code.manager_id', '=', 'managers.id')
                ->leftJoin('artificer','present_article_code.specialist_id','=','artificer.artificer_id');
        $presentListInfoDetail = $query->where($where)->first();
        if($presentListInfoDetail === null){
            return [];
        }else{
            $presentListInfoDetailRes = $presentListInfoDetail->toArray();
            $assistantInfo = Artificer::select('name','number')->where('artificer_id','=',$presentListInfoDetailRes['assistantId'])->first();
            if($assistantInfo === null){
                $presentListInfoDetailRes['assistantName'] = '';
            }else{
                $presentListInfoDetailRes['assistantName'] = $assistantInfo['name'];
                $presentListInfoDetailRes['assistantNumber'] = $assistantInfo['number'];
            }
            return $presentListInfoDetailRes;
        }
    }
    
    /**
     * 根据券id，获取是否可验证状态
     */
    public static function getPresentTicketCanUseStatusByWhere($where){
        $field = array(
            'present.verify_status as verifyStatus',
            'departments.title as departmentName',
            'present_article_code.expire_at as expireTime',
            'present_article_code.status as ticketStatus',
        );
        $query = self::select($field)
                ->leftJoin('present', 'present.present_id', '=', 'present_article_code.present_id')
                ->leftJoin('departments','present.department_id', '=', 'departments.id');
        $presentTicketCanUseStatusInfo = $query->where($where)->first();
        if($presentTicketCanUseStatusInfo === null){
            throw new ApiException('赠送券信息不存在');  
        }else{
            $res = $presentTicketCanUseStatusInfo->toArray();
            if($res['ticketStatus'] == 1){
                throw new ApiException('赠送券已使用');  
            }elseif($res['ticketStatus'] == 3 || time() > strtotime ($res['expireTime'])){
                throw new ApiException('赠送券已过期');  
            }elseif($res['verifyStatus'] == 2){
                throw new ApiException('赠送券已停用，请联系'.$res['departmentName']); 
            }else{
                return 1;
            }
        }
    }
    
    /**
     * 记录验证券信息
     * 活动使用数 +1 同时 修改券状态
     */
    public static function recordVerifyTicketInfo($managerId,$articleCodeId,$useTime,$specialistId,$assistantId){
        $data['status'] = 1;
        $data['manager_id'] = $managerId;
        $data['specialist_id'] = $specialistId;
        $data['assistant_id'] = $assistantId;
        $data['use_time'] = $useTime;
        $data['record_time'] = date('Y-m-d H:i:s',time());
        $data['updated_at'] = time();
        $updateRes = self::where('article_code_id','=',$articleCodeId)->update($data);
        if($updateRes === false){
            throw new ApiException('使用赠送券失败'); 
        }else{
            //活动使用数 +1
            $presentRes = self::select('present_id')->where('article_code_id','=',$articleCodeId)->first();
            if($presentRes === null){
                Log::info('找不到该活动:', $articleCodeId);
                throw new ApiException('验证完毕，但线上活动找不到该活动');
            }else{
                $res= $presentRes->toArray();
                $presentId = $res['present_id'];
            }
            if($presentId){
                $res = Present::where('present_id','=',$presentId)
                    ->increment('present.use_num',1);
                if(!$res){
                    //记录错误日志，不进行异常处理
                    Log::info('活动赠送券使用后，使用数增加失败:', $articleCodeId);
                }
            }
            
        }
        return 1;
        
    }
    
    /**
     * 获取券相关信息
     */
    public static function getArticleTicketInfo($presentId,$page,$pageSize){
        $field = array(
            'beauty_item.name as itemName',
            'present_article_code.code as TicketCode',
            'present.start_at as startTime',
            'present.end_at as endTime',
            'present_article_code.status as ticketStatus',
        );
        $query = self::select($field)
                ->where('present_article_code.present_id','=',$presentId)
                ->leftJoin('present', 'present.present_id', '=', 'present_article_code.present_id')
                ->leftJoin('beauty_item', 'present.item_id', '=', 'beauty_item.item_id')
                ->orderBy('present_article_code.created_at','desc');
        //手动设置页数
        AbstractPaginator::currentPageResolver(function() use ($page) {
              return $page;
        });      
        $articleTicketInfo = $query->paginate($pageSize)->toArray();
        unset($articleTicketInfo['next_page_url']);
        unset($articleTicketInfo['prev_page_url']);
        return $articleTicketInfo;
        
    }
    
    /**
     * 获取当前活动所有券相关信息
     */
    public static function getAllArticleTicketInfoForExport($presentId){
        $field = array(
            'beauty_item.name as itemName',
            'present_article_code.code as ticketCode',
            'present.start_at as startTime',
            'present.end_at as endTime',
            'present_article_code.status as ticketStatus',
        );
        $res = self::select($field)
                ->where('present_article_code.present_id','=',$presentId)
                ->leftJoin('present', 'present.present_id', '=', 'present_article_code.present_id')
                ->leftJoin('beauty_item', 'present.item_id', '=', 'beauty_item.item_id')
                ->orderBy('present_article_code.created_at','desc')
                ->get() ->toArray();
        return $res;

    }
    
    // 获取系统内部订单号
    public static function getOrderSn($p = 'DZ'){
        $pre = $p.date('ymd');
        $redis = Redis::connection();
        $key = 'OSN-'.date('ymd');
        if($redis->get($key)==FALSE)
            $redis->setex($key,3600*24,0);
        $sn = $redis->incr($key);
        $sn = str_pad($sn, 5,'0',STR_PAD_LEFT);
        $sn = $pre.$sn;
        return $sn;
    }
}
