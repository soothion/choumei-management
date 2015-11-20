<?php

namespace App\Http\Controllers\SystemConfig;

use App\Http\Controllers\Controller;
use Event;
use DB;
Use PDO;
use App\Warning;

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
     * @apiSuccess {Number} accountNum 注册帐号个数.
     * @apiSuccess {Number} loginNum 登录次数
     * @apiSuccess {Number} payNum 支付次数
     * @apiSuccess {Number} orderNum 购买单量
     *
     * @apiSuccessExample Success-Response:
     *       {
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "未授权访问"
     * 		}
     */
    public function index() {
        
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
       
    }
    
    /**
     * @api {post} /warning/block 3.移入黑名单
     * @apiName block
     * @apiGroup  warning
     *
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

    }

}
