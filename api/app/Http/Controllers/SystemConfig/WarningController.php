<?php

namespace App\Http\Controllers\SystemConfig;

use App\Http\Controllers\Controller;
use Event;
use DB;
Use PDO;
use App\Warning;
use App\RequestLog;
use App\Blacklist;

class WarningController extends Controller {

    /**
     * @api {post} /warning/index 1.预警查询列表
     * @apiName index
     * @apiGroup warning
     *
     * @apiParam {Number} page 可选,页码，默认为1.
     * @apiParam {Number} page_size 可选,默认为20.
     * @apiParam {String} keyword 可选,搜索关键词.
     * @apiParam {Number} keywordType 必选,搜索关键词类型，可取0 用户手机号/1 设备号/3 微信OpenId.
     * @apiParam {Number} orderNum 购买单量最小值
     * @apiParam {String} minTime 提交最小时间 YYYY-MM-DD
     * @apiParam {String} maxTime 提交最大时间 YYYY-MM-DD
     *
     * @apiSuccess {Number} total 总数据量.
     * @apiSuccess {Number} per_page 分页大小.
     * @apiSuccess {Number} current_page 当前页面.
     * @apiSuccess {Number} last_page 当前页面.
     * @apiSuccess {Number} from 起始数据.
     * @apiSuccess {Number} to 结束数据.
     * @apiSuccess {Number} userId 用户id.
     * @apiSuccess {String} userMobile 用户手机号.
     * @apiSuccess {String} device 设备号.
     * @apiSuccess {String} openId 微信openId.
     * @apiSuccess {Number} loginNum 登录次数
     * @apiSuccess {Number} payNum 支付次数
     * @apiSuccess {Number} orderNum 购买单量
     * @apiSuccess {Number} blacklistStatus 黑名单状态
     *
     * @apiSuccessExample Success-Response:
     *      {
     *          "result": 1,
     *          "token": "",
     *          "data":{
     *          "total": 17,
     *          "per_page": 20,
     *          "current_page": 1,
     *           "last_page": 1,
     *          "next_page_url": null,
     *          "prev_page_url": null,
     *          "from": 1,
     *          "to": 17,
     *          "data":[
     *              {
     *              "payNum": 32,
     *              "orderNum": 124,
     *              "maxOrderTime": 1447828338,
     *              "userMobile": "18026995465",
     *              "userId": 720005,
     *              "loginNum": 4,
     *              "blacklistStatus": 0
     *              }..]
     *      }
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "未授权访问"
     * 		}
     */
    public function index() {
        $param = $this->param;

        if (!isset($param['keywordType'])) {
            throw new ApiException('缺少类型！', 1);
        }
        if (isset($param['page']) && !empty($param['page'])) {
            $page = $param['page'];
        } else {
            $page = 1;
        }
        if (isset($param['page_size']) && !empty($param['page_size'])) {
            $size = $param['page_size'];
        } else {
            $size = 20;
        }
        DB::connection()->setFetchMode(PDO::FETCH_ASSOC);
        $nums = Warning::searchOrder($param, $page, $size);
        switch ($param ["keywordType"]) {
            case "0" : // 用户手机号

                foreach ($nums["data"] as $key => $num) {

                    $nums["data"][$key]["loginNum"] = RequestLog::getLoginNumbyUserId($num["userId"]);
                    $nums["data"][$key]["blacklistStatus"] = Blacklist::getStatusbyUserMobile($num["userMobile"]);
                }
                break;
            case "1" : // 设备号
                foreach ($nums["data"] as $key => $num) {
                    $nums["data"][$key]["loginNum"] = RequestLog::getLoginNumbyDevice($num["device"]);
                    $nums["data"][$key]["blacklistStatus"] = Blacklist::getStatusbyUserDevice($num["device"]);
                }
                break;
            case "2" ://openId
                foreach ($nums["data"] as $key => $num) {
                    $nums["data"][$key]["loginNum"] = RequestLog::getLoginNumbyOpenId($num["openId"]);
                    $nums["data"][$key]["blacklistStatus"] = Blacklist::getStatusbyOpenId($num["openId"]);
                }
                break;
        }
        return $this->success($nums);
    }

    /**
     * @api {post} /warning/export 2.预警查询列表导出
     * @apiName export
     * @apiGroup warning
     *
     * @apiParam {Number} page 可选,页码，默认为1.
     * @apiParam {Number} page_size 可选,默认为20.
     * @apiParam {String} keyword 可选,搜索关键词.
     * @apiParam {Number} keywordType 必选,搜索关键词类型，可取0 用户手机号/1 设备号/3 微信OpenId.
     * @apiParam {String} minTime 提交最小时间 YYYY-MM-DD
     * @apiParam {String} maxTime 提交最大时间 YYYY-MM-DD
     *
     * @apiErrorExample Error-Response:
     * {
     * "result": 0,
     * "msg": "未授权访问"
     * }
     */
    public function export() {
        $param = $this->param;

        if (!isset($param['keywordType'])) {
            throw new ApiException('缺少类型！', 1);
        }
        if (isset($param['page']) && !empty($param['page'])) {
            $page = $param['page'];
        } else {
            $page = 1;
        }
        if (isset($param['page_size']) && !empty($param['page_size'])) {
            $size = $param['page_size'];
        } else {
            $size = 20;
        }
        DB::connection()->setFetchMode(PDO::FETCH_ASSOC);
        $nums = Warning::searchOrder($param, $page, $size);
        switch ($param ["keywordType"]) {
            case "0" : // 用户手机号

                foreach ($nums["data"] as $key => $num) {

                    $nums["data"][$key]["loginNum"] = RequestLog::getLoginNumbyUserId($num["userId"]);
                    $nums["data"][$key]["blacklistStatus"] = Blacklist::getStatusbyUserMobile($num["userMobile"]);
                }
                break;
            case "1" : // 设备号
                foreach ($num["data"] as $key => $nums) {
                    $nums["data"][$key]["loginNum"] = RequestLog::getLoginNumbyDevice($num["device"]);
                    $nums["data"][$key]["blacklistStatus"] = Blacklist::getStatusbyUserDevice($num["device"]);
                }
                break;
            case "2" ://openId
                foreach ($num["data"] as $key => $nums) {
                    $nums["data"][$key]["loginNum"] = RequestLog::getLoginNumbyOpenId($num["openId"]);
                    $nums["data"][$key]["blacklistStatus"] = Blacklist::getStatusbyOpenId($num["openId"]);
                }
                break;
        }
        $keywordName = "";

        switch ($param ["keywordType"]) {
            case "0" : // 用户手机号				
                $keywordName = "手机号";
                break;
            case "1" : // 设备号
                $keywordName = "设备号";
                break;
            case "2" ://openid
                $keywordName = "微信OpenId";
                break;
            default:
                throw new ApiException('黑名单无此类别！', 1);
        }

        $header = [
            '序号',
            $keywordName,
            '合计登录次数',
            '合计支付次数',
            '合计购买单量',
        ];
        $res = Warning::format_export_data($nums["data"], $param['keywordType']);
        if (!empty($res)) {
            Event::fire("warning.export");
        }
        @ini_set('memory_limit', '256M');
        $this->export_xls("黑名单列表" . date("Ymd"), $header, $res);
    }

    /**
     * @api {post} /warning/block 3.移入黑名单
     * @apiName block
     * @apiGroup  warning
     *
     * @apiParam {Number} keywordType 必选,搜索关键词类型，可取0 用户手机号/1 设备号/3 微信OpenId.
     * @apiParam {Number} mobilephone	   
     * @apiParam {Number} device_uuid	 
     * @apiParam {Number} openid	 
     * 
     *  
     * @apiSuccess {String} msg 移入信息
     *
     * @apiSuccessExample Success-Response:
     *      {
     *          result": 1,
     *          "token": "",
     *          "data":{
     *          "msg": "成功移入黑名单！"
     *      }
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    result": 0,
     *          "code": 1,
     *          "msg": "移入黑名单失败",
     *          "token": ""
     * 		}
     */
    function block() {
        $param = $this->param;
        if (!isset($param['keywordType'])) {
            throw new ApiException('缺少类型！', 1);
        }
        $blacklistStatus = 0;
        switch ($param ["keywordType"]) {
            case "0" : // 用户手机号
                $blacklistStatus = Blacklist::getStatusbyUserMobile($param["mobilephone"]);
                if ($blacklistStatus) {
                    throw new ApiException('黑名单已存在！', 1);
                }
                $result = Blacklist::insert(array('mobilephone' => $param["mobilephone"]));

                break;
            case "1" : // 设备号

                $blacklistStatus = Blacklist::getStatusbyUserDevice($param["device_uuid"]);
                if ($blacklistStatus) {
                    throw new ApiException('黑名单已存在！', 1);
                }
                $result = Blacklist::insert(array('device_uuid' => $param["device_uuid"]));

                break;
            case "2" ://openId

                $blacklistStatus = Blacklist::getStatusbyOpenId($param["openid"]);
                if ($blacklistStatus) {
                    throw new ApiException('黑名单已存在！', 1);
                }
                $result = Blacklist::insert(array('openid' => $param["openid"]));

                break;
        }

        if ($result) {
            $res['msg'] = "成功移入黑名单！";
            return $this->success($res);
        }
        throw new ApiException('移入黑名单失败！', 1);
    }

}
