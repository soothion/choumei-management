<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
Use PDO;
Use URL;
use Illuminate\Pagination\AbstractPaginator;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;

class Warning extends Model {

    protected $table = 'order';
    protected $primaryKey = 'orderid';
    public $timestamps = false;

    public static function searchOrder($input, $page, $size) {
        $query = Self::getQuery();
        // 是否有输入关键字搜索 
        $val = '';
        if (!empty($input["keyword"])) {
            $val = $input ["keyword"];
            $val = addslashes($val);
            $val = str_replace(['_', '%'], ['\_', '\%'], $val);
        }
        if (!isset($input['orderNum']) || $input['orderNum'] < 5) {
            $orderNum = 5;
        } else {
            $orderNum = $input['orderNum'];
        }
        switch ($input ["keywordType"]) {

            case "0" : // 用户手机号		
                $fields = [DB::raw("COUNT(DISTINCT shopcartsn) as payNum"), DB::raw("COUNT(cm_order.user_id) as orderNum"), DB::raw("MAX(cm_order.add_time)as maxOrderTime"), "user.mobilephone as userMobile", "order.user_id as userId"];
                $query->select($fields)->join('user', 'user.user_id', '=', 'order.user_id')->groupBy('order.user_id')->having(DB::raw("COUNT(cm_order.user_id)"), '>', $orderNum);
                if (!empty($val)) {
                    $query->where('user.mobilephone', 'like', '%' . $val . '%');
                }

                break;
            case "2" : // openId

                $fields = [DB::raw("COUNT(DISTINCT shopcartsn) as payNum"), DB::raw("COUNT(cm_request_log.OPENID) as orderNum"), DB::raw("MAX(cm_order.add_time)as maxOrderTime"), "request_log.OPENID as openId"];
                $query->select($fields)->join('request_log', 'request_log.ORDER_SN', '=', 'order.ordersn')->groupBy('request_log.OPENID')->having(DB::raw("COUNT(cm_request_log.OPENID)"), '>', $orderNum);
                if (!empty($val)) {
                    $query->where('request_log.OPENID', 'like', '%' . $val . '%');
                }

                break;
            case "1" ://设备号
                $fields = [DB::raw("COUNT(DISTINCT shopcartsn) as payNum"), DB::raw("COUNT(cm_request_log.DEVICE_UUID) as orderNum"), DB::raw("MAX(cm_order.add_time)as maxOrderTime"), "request_log.DEVICE_UUID as device"];
                $query->select($fields)->join('request_log', 'request_log.ORDER_SN', '=', 'order.ordersn')->groupBy('request_log.DEVICE_UUID')->having(DB::raw("COUNT(cm_request_log.DEVICE_UUID)"), '>', $orderNum);
                if (!empty($val)) {
                    $query->where('request_log.DEVICE_UUID', 'like', '%' . $val . '%');
                }
                break;
            default:
                throw new ApiException('不支持其他类似搜索！', 1);
        }

        //时间范围
        if (!empty($input["minTime"])) {
            $minTime = strtotime($input["minTime"]);
            if ($minTime) {
                $query->where('order.add_time', '>=', $minTime);
            }
        }
        if (!empty($input["maxTime"])) {
            $maxTime = strtotime($input["maxTime"]);
            if ($maxTime) {
                $maxTime += 86399;
                $query->where('order.add_time', '<=', $maxTime);
            }
        }

        //手动设置页数
        AbstractPaginator::currentPageResolver(function() use ($page) {
            return $page;
        });
        $nums = $query->where('order.ispay', '=', 2)->where('order.actuallyPay', '>', 0)->orderBy(DB::raw("MAX(cm_order.add_time)"), "DESC")->paginate($size)->toArray();
        return $nums;
    }

    public static function format_export_data($datas, $keywordType) {
        $res = [];
        foreach ($datas as $key => $data) {
            switch ($keywordType) {
                case "0" : // 用户手机号				
                    $keyword = isset($data['userMobile']) ? ' ' . $data['userMobile'] : '';
                    break;
                case "1" : // 设备号
                    $keyword = isset($data['device']) ? ' ' . $data['device'] : '';
                    break;
                case "2" ://openid
                    $keyword = isset($data['openId']) ? ' ' . $data['openId'] : '';
                    break;
                default:
                    throw new ApiException('预警查询无此类别！', 1);
            }
            $loginNum = isset($data['loginNum']) ? $data['loginNum'] : '';
            $payNum = isset($data['payNum']) ? $data['payNum'] : '';
            $orderNum = isset($data['orderNum']) ? $data['orderNum'] : '';
            $res[] = [
                $key+1,
                $keyword,
                $loginNum,
                $payNum,
                $orderNum,
            ];
        }
        return $res;
    }

}
