<?php

namespace App\Http\Controllers\Bounty;

use App\Http\Controllers\Controller;
use App\BountyTask;
use App\User;
use App\Salon;
use App\Hairstylist;
use App\PaymentLog;
use Log;
use Event;
use Excel;

class BountyController extends Controller {

    /**
     * @api {post} /bounty/getList 1.赏金单查询列表
     * @apiName getList
     * @apiGroup  bounty
     *
     * @apiParam {Number} page 可选,页码，默认为1.
     * @apiParam {Number} pageSize 可选,默认为10.
     * @apiParam {String} keyword 可选,搜索关键词.
     * @apiParam {String} keywordType 必选,搜索关键词类型，可取0 赏金单号/1 用户臭美号/2 用户手机号/3 店铺名称.
     * @apiParam {Number} payType 可选,支付方式：1 网银/2 支付宝/3 微信/4 余额/5 红包/6 优惠券/7 积分/8邀请码兑换.
     * @apiParam {Number} isPay 可选,支付状态：1否 2是
     * @apiParam {Number} btStatus 可选,订单状态：1 待抢单，2 待服务，3 已服务，4 已打赏, 5 不打赏, 9 取消
     * @apiParam {Number} refundStatus 可选,退款状态：5申请退款，6退款中，7退款完成, 8拒绝, 9失败
     * @apiParam {String} minPayTime 可选,交易时间左框.
     * @apiParam {String} maxPayTime 可选,交易时间右框.
     * @apiParam {String} minEndTime 可选,退款时间左框.
     * @apiParam {String} maxEndTime 可选,退款时间右框.
     * @apiParam {String} sortKey 可选,排序关键词 "btSn" 赏金单号/ "money" 赏金金额 / "addTime"下单时间.
     * @apiParam {String} sortType 可选,排序 DESC倒序 ASC升序.
     *
     * @apiSuccess {Number} total 总页数.
     * @apiSuccess {Number} perPage 分页大小.
     * @apiSuccess {Number} records 总条数.
     * @apiSuccess {Number} currentPage 当前页面.
     * @apiSuccess {Number} from 起始数.
     * @apiSuccess {Number} to 结束数.
     * @apiSuccess {Number} amount 总金额.
     * @apiSuccess {Number} btId 赏金单Id.
     * @apiSuccess {String} btSn 赏金单号.
     * @apiSuccess {String} tn 三方流水号.
     * @apiSuccess {String} payType 支付方式：1 网银/2 支付宝/3 微信/4 余额/.
     * @apiSuccess {Number} money 赏金金额/退款金额
     * @apiSuccess {String} addTime 下单时间.
     * @apiSuccess {String} endTime 申请时间.
     * @apiSuccess {String} userName 用户臭美号.
     * @apiSuccess {Number} hairStylistMobile 造型师手机号.
     * @apiSuccess {Number} userMobile 用户手机号.
     * @apiSuccess {String} salonName 商铺名称.
     * @apiSuccess {String} refundStatus 退款状态：5申请退款，6退款中，7退款完成, 8拒绝, 9失败
     * @apiSuccess {String} isPay 支付状态：1未支付 2已支付	 
     * @apiSuccess {String} operations 赏金单操作链接.
     * @apiSuccess {String} refund_operations 赏金单退款操作链接.
     * 
     *
     *
     * @apiSuccessExample Success-Response:
     * 	{
     * 	    "result": 1,
     * 	    "data": {
     * 	        "total": 51,
     * 	        "perPage": 10,
     * 	        "records": 510,
     * 	        "currentPage": 1,
     * 	        "from": 1,
     * 	        "to": 10,
     *              "amount":12000,
     * 	        "data": [
     * 	            {
     *                  "btId":
     *                  "btSn" :"33833797391"
     *                  "tn" :"1224362901201506096196702838"
     *                  "payType" :1
     *                  "money" :200
     *                  "addTime" :1434332931
     *                  "hairStylistMobile" :18680370905
     *                  "userMobile" :18680370905
     *                  "salonName" :永琪美容美发（南光店）
     *                  "isPay" :2
     * 	            }
     *              ......
     * 	        ]
     * 	    }
     * 	}
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "参数有误！"
     * 		}
     */
    function getList() {
        $param = $this->param;
        Log::info('Bounty getList param is: ', $param);
        if (isset($param['page']) && !empty($param['page'])) {
            $page = $param['page'];
        } else {
            $page = 1;
        }
        if (isset($param['size']) && !empty($param['size'])) {
            $size = $param['size'];
        } else {
            $size = 10;
        }
        $query = BountyTask::getQueryByParam($param);
        $sortable_keys = ['btSn', 'money', 'addTime'];
        $sortKey = "addTime";
        $sortType = "DESC";
        if (isset($param['sortKey']) && in_array($param['sortKey'], $sortable_keys)) {
            $sortKey = $param['sortKey'];
            $sortType = $param['sortType'];
            if (strtoupper($sortType) != "DESC") {
                $sortType = "ASC";
            }
        }

        $count = BountyTask::getcount($query);

        $bountys = BountyTask::search($query, $page, $size, $sortKey, $sortType);

        $amount = BountyTask::getAmount($bountys);
        $res = [];

        $res["total"] = ceil($count / $size);
        $res["perPage"] = $size;
        $res["correntPage"] = $page;
        $res["records"] = $count;
        $res["amount"] = array("amount" => number_format($amount, 2));
        $res['data'] = $bountys;
        return $this->success($res);
    }

    /**
     * @api {post} /bounty/detail 2.赏金单详情
     * @apiName detail
     * @apiGroup  bounty
     *
     * @apiParam {Number} no 必选.	 
     *
     * @apiSuccess {Number} btId 赏金单Id.
     * @apiSuccess {String} btSn 赏金单号.
     * @apiSuccess {String} userName 用户臭美号.
     * @apiSuccess {String} userMobile 用户手机号.
     * @apiSuccess {String} needsStr 任务需求.
     * @apiSuccess {String} remark 我的需求.
     * @apiSuccess {String} selectType 选择发型师类型.
     * @apiSuccess {String} payType 支付方式：1 网银/2 支付宝/3 微信/4 余额/.
     * @apiSuccess {Number} money 赏金金额
     * @apiSuccess {String} isPay 是否支付.
     * @apiSuccess {String} addTime 下单时间.
     * @apiSuccess {String} payTime 支付时间.
     * @apiSuccess {String} tn 三方流水号.   
     * @apiSuccess {String} endTime 取消时间/完成时间.
     * @apiSuccess {String} salonName 商铺名称.
     * @apiSuccess {String} district 服务区域.
     * @apiSuccess {String} bountyType 店铺类型.
     * @apiSuccess {String} grade 造型师类型.
     * @apiSuccess {String} stylistName 造型师帐号.
     * @apiSuccess {Number} hairStylistMobile 造型师手机号.
     * @apiSuccess {String} btStatus 订单状态.
     * 
     *
     *
     * @apiSuccessExample Success-Response:
     *      {
     *           "result": 1,
     *           "token": "",
     *           "data":{
     *                      "btId": 1903,
     *                      "btSn": "3468005511776",
     *                      "needsStr": "洗剪吹",
     *                      "remark": "默默默默默默哦默默",
     *                      "selectType": "自己选",
     *                      "payType": "微信",
     *                      "money": 230,
     *                      "isPay": "已支付",
     *                      "addTime": "2015-06-19 10:14:15",
     *                      "payTime": "2015-06-19 10:14:00",
     *                      "endTime": "2015-06-19 10:15:06",
     *                      "btStatus": "不打赏",
     *                      "userName": "10306986",
     *                      "userMobile": 1111111111,
     *                      "tn": "1224362901201506196029751743",
     *                      "salonName": "choumeitest_salon",
     *                      "bountyType": "",
     *                      "district": "全城全区",
     *                      "grade": "美发大师",
     *                      "stylistName": "欧阳",
     *                      "hairStylistMobile": "17875827821"
     *                 }
     *       }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "message": "参数有误！"
     * 		}
     */
    function detail() {
        $param = $this->param;
        Log::info('Bounty detail param is: ', $param);
        if (empty($param['no'])) {
            return $this->error("没有id传递！");
        }
        $id = $param['no'];
        $detail = BountyTask::detail($id);
        if (!$detail) {
            return $this->error("找不到赏金单！");
        }
        return $this->success($detail);
    }

    /**
     * @api {post} /bounty/refundDetail 3.赏金退款单详情
     * @apiName refundDetail
     * @apiGroup  bounty
     *
     * @apiParam {Number} no 必选,赏金单号.	 
     *
     * @apiSuccess {Number} btId 赏金单Id.
     * @apiSuccess {String} btSn 赏金单号.
     * @apiSuccess {String} btStatus 赏金单状态.
     * @apiSuccess {String} tn 三方流水号.
     * @apiSuccess {String} endTime 申请时间. 
     * @apiSuccess {String} userName 用户臭美号. 
     * @apiSuccess {String} userMobile 用户手机号.
     * @apiSuccess {String} payType 支付方式：1 网银/2 支付宝/3 微信/4 余额/.
     * @apiSuccess {Number} money 赏金金额/退款金额
     * @apiSuccess {String} refundType 退款方式.
     * @apiSuccess {String} refundStatus 退款状态.
     * 
     *
     *
     * @apiSuccessExample Success-Response:
     * 	{
     *      "result": 1,
     *      "token": "",
     *      "data":{
     *              "btID": 1903,
     *              "btSn": "3468005511776",
     *              "btStatus": "不打赏",
     *              "endTime": "2015-06-19 10:15:06",
     *              "payType": "微信",
     *              "money": 230,
     *              "refundStatus": "申请退款",
     *              "tn": "1224362901201506196029751743",
     *              "userName": "10306986",
     *              "userMobile": 1111111111,
     *              "refundType": "原路返还"
     *          }
     *  }
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "参数有误！"
     * 		}
     */
    function refundDetail() {
        $param = $this->param;
        Log::info('Bounty refundDetail param is: ', $param);
        if (empty($param['no'])) {
            return $this->error("没有Sn传递！");
        }
        $id = $param['no'];
        $detail = BountyTask::refundDetail($id);
        if (!$detail) {
            return $this->error("找不到赏金单！");
        }
        return $this->success($detail);
    }

    /**
     * @api {post} /bounty/accept 4.赏金单退款通过
     * @apiName accept
     * @apiGroup  bounty
     *
     * @apiParam {Array} ids 必选,赏金单Id数列.	      * 
     * @apiSuccess {String} log 退款信息.
     *
     * @apiSuccessExample Success-Response:
     * 	{
     * 	    "result": 1,
     * 	    "data": {
     * 	        "log": "退款成功!",
     * 	        ]
     * 	    }
     * 	}
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "退款失败！"
     * 		}
     */
    function accept() {
        
    }

    /**
     * @api {post} /bounty/reject 5.赏金单退款拒绝
     * @apiName reject
     * @apiGroup  bounty
     *
     * @apiParam {Array} ids 必选,赏金单Id数列.	      * 
     * @apiSuccess {String} log 退款信息.
     *
     * @apiSuccessExample Success-Response:
     * 	{
     * 	    "result": 1,
     * 	    "data": {
     * 	        "log": "执行成功!",
     * 	        ]
     * 	    }
     * 	}
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "拒绝原因不能为空！"
     * 		}
     */
    function reject() {
        
    }

    /**
     * @api {get} /bouty/exportBounty 6.导出赏金单列表
     * @apiName exportBounty
     * @apiGroup bounty
     *
     * @apiParam {String} keyword 可选,搜索关键词.
     * @apiParam {String} keywordType 必选,搜索关键词类型，可取"btSn","userName","mobile","salonName".
     * @apiParam {Number} payType 可选,支付方式：1 网银/2 支付宝/3 微信/4 余额/5 红包/6 优惠券/7 积分/8邀请码兑换.
     * @apiParam {Number} isPay 可选,支付状态：1否 2是
     * @apiParam {Number} btStatus 可选,订单状态：1 待抢单，2 待服务，3 已服务，4 已打赏, 5 不打赏, 9 取消
     * @apiParam {Number} refundStatus 可选,退款状态：5申请退款，6退款中，7退款完成, 8拒绝, 9失败
     * @apiParam {String} minTime 可选,交易时间左框.
     * @apiParam {String} maxTime 可选,交易时间右框.
     * @apiParam {String} sortKey 可选,排序关键词 "btSn" 赏金单号/ "money" 赏金金额 / "addTime"下单时间.
     * @apiParam {String} sortType 可选,排序 DESC倒序 ASC升序.
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "未授权访问"
     * 		}
     */
    public function exportBounty() {
        $param = $this->param;
        Log::info('Bounty getList param is: ', $param);
        $query = BountyTask::getQueryByParam($param);
        $sortable_keys = ['btSn', 'money', 'addTime'];
        $sortKey = "addTime";
        $sortType = "DESC";
        if (isset($param['sortKey']) && in_array($param['sortKey'], $sortable_keys)) {
            $sortKey = $param['sortKey'];
            $sortType = $param['sortType'];
            if (strtoupper($sortType) != "DESC") {
                $sortType = "ASC";
            }
        }
        $bountys = BountyTask::search($query, 1, -1, $sortKey, $sortType);
        $header = ['赏金单号', '三方流水号', '支付方式', '下单时间', '造型师手机号', '用户手机号', '店铺名称', '支付状态'];
        Event::fire('bounty.export');
        $this->export_xls("赏金单" . date("Ymd"), $header, self::format_exportBounty_data($bountys));
    }

    /**
     * @api {get} /bouty/exportRefund 7.导出赏金单退款列表
     * @apiName exportRefund
     * @apiGroup bounty
     *
     * @apiParam {String} keyword 可选,搜索关键词.
     * @apiParam {String} keywordType 必选,搜索关键词类型，可取"btSn","userName","mobile","salonName".
     * @apiParam {Number} payType 可选,支付方式：1 网银/2 支付宝/3 微信/4 余额/5 红包/6 优惠券/7 积分/8邀请码兑换.
     * @apiParam {Number} isPay 可选,支付状态：1否 2是
     * @apiParam {Number} btStatus 可选,订单状态：1 待抢单，2 待服务，3 已服务，4 已打赏, 5 不打赏, 9 取消
     * @apiParam {Number} refundStatus 可选,退款状态：5申请退款，6退款中，7退款完成, 8拒绝, 9失败
     * @apiParam {String} minTime 可选,交易时间左框.
     * @apiParam {String} maxTime 可选,交易时间右框.
     * @apiParam {String} sortKey 可选,排序关键词 "btSn" 赏金单号/ "money" 赏金金额 / "addTime"下单时间.
     * @apiParam {String} sortType 可选,排序 DESC倒序 ASC升序.
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "未授权访问"
     * 		}
     */
    public function exportRefund() {
        $param = $this->param;
        Log::info('Bounty getList param is: ', $param);
        $query = BountyTask::getQueryByParam($param);
        $sortable_keys = ['btSn', 'money', 'addTime'];
        $sortKey = "addTime";
        $sortType = "DESC";
        if (isset($param['sortKey']) && in_array($param['sortKey'], $sortable_keys)) {
            $sortKey = $param['sortKey'];
            $sortType = $param['sortType'];
            if (strtoupper($sortType) != "DESC") {
                $sortType = "ASC";
            }
        }
        $bountys = BountyTask::search($query, 1, -1, $sortKey, $sortType);
        $header = ['赏金单号', '支付方式', '退款金额', '申请时间', '用户臭美号', '用户手机号', '店铺名称', '退款状态'];
        Event::fire('bountyRefund.export');
        $this->export_xls("赏金退款单" . date("Ymd"), $header, self::format_exportRefund_data($bountys));
    }

    protected static function format_exportBounty_data($datas) {
        $res = [];
        foreach ($datas as $data) {
            $btSn = isset($data['btSn']) ? $data['btSn'] : '';
            $tn = isset($data['tn']) ? $data['tn'] : '';
            $payType = isset($data['payType']) ? $data['payType'] : '';
            $addTime = isset($data['addTime']) ? $data['addTime'] : '';
            $hairStylistMobile = isset($data['hairStylistMobile']) ? $data['hairStylistMobile'] : '';
            $userMobile = isset($data['userMobile']) ? $data['userMobile'] : '';
            $salonName = isset($data['salonName']) ? $data['salonName'] : '';
            $isPay = isset($data['isPay']) ? $data['isPay'] : '';
            $res[] = [
                $btSn,
                $tn,
                $payType,
                $addTime,
                $hairStylistMobile,
                $userMobile,
                $salonName,
                $isPay,
            ];
        }
        return $res;
    }

    protected static function format_exportRefund_data($datas) {
        $res = [];
        foreach ($datas as $data) {
            $btSn = isset($data['btSn']) ? $data['btSn'] : '';
            $payType = isset($data['payType']) ? $data['payType'] : '';
            $money = isset($data['money']) ? $data['money'] : '';
            $endTime = isset($data['endTime']) ? $data['endTime'] : '';
            $userName = isset($data['userName']) ? $data['userName'] : '';
            $userMobile = isset($data['userMobile']) ? $data['userMobile'] : '';
            $salonName = isset($data['salonName']) ? $data['salonName'] : '';
            $refundStatus = isset($data['refundStatus']) ? $data['refundStatus'] : '';
            $res[] = [
                $btSn,
                $payType,
                $money,
                $endTime,
                $userName,
                $userMobile,
                $salonName,
                $refundStatus,
            ];
        }
        return $res;
    }

}
