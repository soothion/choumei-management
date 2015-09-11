<?php

namespace App\Http\Controllers\Trans;

use App\Http\Controllers\Controller;
use App\TransactionSearchApi;

class TicketController extends Controller
{
    /**
     * @api {get} /ticket/index 1.臭美券列表
     * @apiName index
     * @apiGroup ticket
     *
     * @apiParam {Number} key  1 臭美券密码 2 用户手机号  3 店铺名称  4 用户设备号  5 代金券编码  6 活动编码
     * @apiParam {String} keyword  根据key来的关键字
     * @apiParam {String} time_key  1 消费时间  2付款时间
     * @apiParam {String} min_time 最小时间 YYYY-MM-DD
     * @apiParam {String} max_time 最大时间 YYYY-MM-DD
     * @apiParam {String} state 0 全部  2 未消费 4 已消费 6申请退款 7 退款完成  8 退款中 9 退款失败
     * @apiParam {Number} page 可选,页数. (从1开始)
     * @apiParam {Number} page_size 可选,分页大小.(最小1 最大500,默认20)
     * @apiParam {String} sort_key 排序的键 ['id','updated_at'(创建时间,默认),'code'(付款单号),'type'(付款类型),'pay_money'(付款金额),'cost_money'(换算消费额),'day'(付款日期)]
     * @apiParam {String} sort_type 排序的方式 ASC正序 DESC倒叙 (默认)
     *
     * @apiSuccess {Number} total 总数据量.
     * @apiSuccess {Number} per_page 分页大小.
     * @apiSuccess {Number} current_page 当前页面.
     * @apiSuccess {Number} last_page 当前页面.
     * @apiSuccess {Number} from 起始数据.
     * @apiSuccess {Number} to 结束数据.
     * @apiSuccess {String} code 单号
     * @apiSuccess {String} type 付款类型 1 付交易代收款 2 付业务投资款
     * @apiSuccess {String} money 付款金额
     * @apiSuccess {String} pay_type 付款方式   1 银行存款 2账扣支付 3现金  4支付宝 5财付通
     * @apiSuccess {String} require_day 要求付款日期
     * @apiSuccess {String} pay_day 实际付款日期
     * @apiSuccess {String} cycle 回款周期
     * @apiSuccess {String} cycle_day 回款日期
     * @apiSuccess {String} cycle_money 周期回款金额
     * @apiSuccess {String} make_user 制单人信息
     * @apiSuccess {String} confirm_user 审批人信息
     * @apiSuccess {String} cash_user 出纳人信息
     * @apiSuccess {String} salon 店铺信息
     * @apiSuccess {String} state 订单状态  1待提交 2待审批 3:待付款 4:已付款
     * @apiSuccess {String} confirm_at 审批日期
     *
     * @apiSuccessExample Success-Response:
     *       {
     *          "result": 1,
     *          "data": {
     *              "total": 1,
     *              "per_page": 10,
     *              "current_page": 1,
     *              "last_page": 1,
     *              "from": 1,
     *              "to": 1,
     *              "data": [
     *               {
     *                   "id": 2,
     *                   "code": "FTZ-150814190145001",
     *                   "type": 2,
     *                   "salon_id": 1,
     *                   "merchant_id": 2,
     *                   "money": "333.66",
     *                   "pay_type": 1,
     *                   "require_day": "2015-08-14",
     *                   "pay_day": "0000-00-00",
     *                   "cycle": 30,
     *                   "cycle_day": 1,
     *                   "cycle_money": "100.00",
     *                   "make_uid": 1,
     *                   "cash_uid": 0,
     *                   "prepay_bill_code": "",
     *                   "receive_bill_code": "",
     *                   "state": 2,
     *                   "created_at": "2015-08-14 19:01:45",
     *                   "confirm_uid": 0,
     *                   "confirm_at": "0000-00-00",
     *                   "updated_at": "2015-08-14 19:01:45",
     *                   "make_user": {
     *                       "id": 1,
     *                       "name": "这是用户名Admin"
     *                   },
     *                   "confirm_user": null,
     *                   "cash_user": null,
     *                   "salon": {
     *                       "salonid": 1,
     *                       "salonname": "嘉美专业烫染",
     *                       "sn": "SZ0320001"
     *                   }
     *               },
     *               {
     *                   "id": 2,
     *                   "code": "FTZ-150814190145001",
     *                   "type": 2,
     *                   "salon_id": 1,
     *                   "merchant_id": 2,
     *                   "money": "333.66",
     *                   "pay_type": 1,
     *                   "require_day": "2015-08-14",
     *                   "pay_day": "0000-00-00",
     *                   "cycle": 30,
     *                   "cycle_day": 1,
     *                   "cycle_money": "100.00",
     *                   "make_uid": 1,
     *                   "cash_uid": 0,
     *                   "prepay_bill_code": "",
     *                   "receive_bill_code": "",
     *                   "state": 2,
     *                   "created_at": "2015-08-14 19:01:45",
     *                   "confirm_uid": 0,
     *                   "confirm_at": "0000-00-00",
     *                   "updated_at": "2015-08-14 19:01:45",
     *                   "make_user": {
     *                       "id": 1,
     *                       "name": "这是用户名Admin"
     *                   },
     *                   "confirm_user": null,
     *                   "cash_user": null,
     *                   "salon": {
     *                       "salonid": 1,
     *                       "salonname": "嘉美专业烫染",
     *                       "sn": "SZ0320001"
     *                   }
     *               }
     *           ]
     *       }
     *      }
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function index()
    {
        $params = $this->parameters([
            'key' => self::T_INT,
            'keyword' => self::T_STRING,
            'min_time' => self::T_STRING,
            'max_time' => self::T_STRING,
            'state' => self::T_INT,
            'time_key' => self::T_INT,
            'page' => self::T_INT,
            'page_size' => self::T_INT,
            'sort_key' => self::T_STRING,
            'sort_type' => self::T_STRING,
        ]);
        $items = TransactionSearchApi::searchOfTicket($params);
        return $this->success($items);
    }
    
    /**
     * @api {get} /ticket/show/{id} 2.臭美券详情
     * @apiName detail
     * @apiGroup ticket
     *
     * @apiSuccess {Number} total 总数据量.
     * @apiSuccess {Number} per_page 分页大小.
     * @apiSuccess {Number} current_page 当前页面.
     * @apiSuccess {Number} last_page 当前页面.
     * @apiSuccess {Number} from 起始数据.
     * @apiSuccess {Number} to 结束数据.
     * @apiSuccess {String} code 单号
     * @apiSuccess {String} type 付款类型 1 付交易代收款 2 付业务投资款
     * @apiSuccess {String} money 付款金额
     * @apiSuccess {String} pay_type 付款方式   1 银行存款 2账扣支付 3现金  4支付宝 5财付通
     * @apiSuccess {String} require_day 要求付款日期
     * @apiSuccess {String} pay_day 实际付款日期
     * @apiSuccess {String} cycle 回款周期
     * @apiSuccess {String} cycle_day 回款日期
     * @apiSuccess {String} cycle_money 周期回款金额
     * @apiSuccess {String} make_user 制单人信息
     * @apiSuccess {String} confirm_user 审批人信息
     * @apiSuccess {String} cash_user 出纳人信息
     * @apiSuccess {String} salon 店铺信息
     * @apiSuccess {String} state 订单状态  1待提交 2待审批 3:待付款 4:已付款
     * @apiSuccess {String} confirm_at 审批日期
     *
     * @apiSuccessExample Success-Response:
     *       {
     *          "result": 1,
     *          "data": {
     *              "total": 1,
     *              "per_page": 10,
     *              "current_page": 1,
     *              "last_page": 1,
     *              "from": 1,
     *              "to": 1,
     *              "data": [
     *               {
     *                   "id": 2,
     *                   "code": "FTZ-150814190145001",
     *                   "type": 2,
     *                   "salon_id": 1,
     *                   "merchant_id": 2,
     *                   "money": "333.66",
     *                   "pay_type": 1,
     *                   "require_day": "2015-08-14",
     *                   "pay_day": "0000-00-00",
     *                   "cycle": 30,
     *                   "cycle_day": 1,
     *                   "cycle_money": "100.00",
     *                   "make_uid": 1,
     *                   "cash_uid": 0,
     *                   "prepay_bill_code": "",
     *                   "receive_bill_code": "",
     *                   "state": 2,
     *                   "created_at": "2015-08-14 19:01:45",
     *                   "confirm_uid": 0,
     *                   "confirm_at": "0000-00-00",
     *                   "updated_at": "2015-08-14 19:01:45",
     *                   "make_user": {
     *                       "id": 1,
     *                       "name": "这是用户名Admin"
     *                   },
     *                   "confirm_user": null,
     *                   "cash_user": null,
     *                   "salon": {
     *                       "salonid": 1,
     *                       "salonname": "嘉美专业烫染",
     *                       "sn": "SZ0320001"
     *                   }
     *               },
     *               {
     *                   "id": 2,
     *                   "code": "FTZ-150814190145001",
     *                   "type": 2,
     *                   "salon_id": 1,
     *                   "merchant_id": 2,
     *                   "money": "333.66",
     *                   "pay_type": 1,
     *                   "require_day": "2015-08-14",
     *                   "pay_day": "0000-00-00",
     *                   "cycle": 30,
     *                   "cycle_day": 1,
     *                   "cycle_money": "100.00",
     *                   "make_uid": 1,
     *                   "cash_uid": 0,
     *                   "prepay_bill_code": "",
     *                   "receive_bill_code": "",
     *                   "state": 2,
     *                   "created_at": "2015-08-14 19:01:45",
     *                   "confirm_uid": 0,
     *                   "confirm_at": "0000-00-00",
     *                   "updated_at": "2015-08-14 19:01:45",
     *                   "make_user": {
     *                       "id": 1,
     *                       "name": "这是用户名Admin"
     *                   },
     *                   "confirm_user": null,
     *                   "cash_user": null,
     *                   "salon": {
     *                       "salonid": 1,
     *                       "salonname": "嘉美专业烫染",
     *                       "sn": "SZ0320001"
     *                   }
     *               }
     *           ]
     *       }
     *      }
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function show($id)
    {
        $id = intval($id);
        $item = TransactionSearchApi::ticketDetail($id);
        return $this->success($item);
    }
    
    /**
     * @api {get} /ticket/export 3.臭美券导出
     * @apiName export
     * @apiGroup ticket
     *
     * @apiParam {Number} key  1 店铺搜索  2 店铺编号
     * @apiParam {String} keyword  根据key来的关键字
     * @apiParam {String} pay_time_min 付款最小时间 YYYY-MM-DD
     * @apiParam {String} pay_time_max 付款最大时间 YYYY-MM-DD
     * @apiParam {String} type 0 全部 1 付交易代收款 2 付业务投资款
     * @apiParam {String} pay_type 0 全部 1 银行存款 2账扣支付 3现金  4支付宝 5财付通
     * @apiParam {String} state 0 全部 1 待提交 2待审批 3待付款  4已付款
     * @apiParam {Number} page 可选,页数. (从1开始)
     * @apiParam {Number} page_size 可选,分页大小.(最小1 最大500,默认20)
     * @apiParam {String} sort_key 排序的键 ['id','updated_at'(创建时间,默认),'code'(付款单号),'type'(付款类型),'pay_money'(付款金额),'cost_money'(换算消费额),'day'(付款日期)]
     * @apiParam {String} sort_type 排序的方式 ASC正序 DESC倒叙 (默认)
     *
     * @apiSuccess {Number} total 总数据量.
     * @apiSuccess {Number} per_page 分页大小.
     * @apiSuccess {Number} current_page 当前页面.
     * @apiSuccess {Number} last_page 当前页面.
     * @apiSuccess {Number} from 起始数据.
     * @apiSuccess {Number} to 结束数据.
     * @apiSuccess {String} code 单号
     * @apiSuccess {String} type 付款类型 1 付交易代收款 2 付业务投资款
     * @apiSuccess {String} money 付款金额
     * @apiSuccess {String} pay_type 付款方式   1 银行存款 2账扣支付 3现金  4支付宝 5财付通
     * @apiSuccess {String} require_day 要求付款日期
     * @apiSuccess {String} pay_day 实际付款日期
     * @apiSuccess {String} cycle 回款周期
     * @apiSuccess {String} cycle_day 回款日期
     * @apiSuccess {String} cycle_money 周期回款金额
     * @apiSuccess {String} make_user 制单人信息
     * @apiSuccess {String} confirm_user 审批人信息
     * @apiSuccess {String} cash_user 出纳人信息
     * @apiSuccess {String} salon 店铺信息
     * @apiSuccess {String} state 订单状态  1待提交 2待审批 3:待付款 4:已付款
     * @apiSuccess {String} confirm_at 审批日期
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function export()
    {
    
    }
}
