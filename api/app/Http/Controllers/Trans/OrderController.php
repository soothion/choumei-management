<?php

namespace App\Http\Controllers\Trans;

use App\Http\Controllers\Controller;
use App\TransactionSearchApi;

class OrderController extends Controller
{
    /**
     * @api {get} /order/index 1.订单列表
     * @apiName index
     * @apiGroup order
     *
     * @apiParam {Number} key  1 订单号  2 臭美券密码 3 用户手机号  4 店铺名称
     * @apiParam {String} keyword  根据key来的关键字
     * @apiParam {String} pay_time_min 下单最小时间 YYYY-MM-DD
     * @apiParam {String} pay_time_max 下单最大时间 YYYY-MM-DD
     * @apiParam {String} pay_type 0 全部 1 银行存款 2账扣支付 3现金  4支付宝 5财付通
     * @apiParam {String} pay_state 0 全部  1 待提交 2待审批 3待付款  4已付款
     * @apiParam {Number} page 可选,页数. (从1开始)
     * @apiParam {Number} page_size 可选,分页大小.(最小1 最大500,默认20)
     * @apiParam {String} sort_key 排序的键 []
     * @apiParam {String} sort_type 排序的方式 ASC正序 DESC倒叙 (默认)
     *
     * @apiSuccess {Number} total 总数据量.
     * @apiSuccess {Number} per_page 分页大小.
     * @apiSuccess {Number} current_page 当前页面.
     * @apiSuccess {Number} last_page 当前页面.
     * @apiSuccess {Number} from 起始数据.
     * @apiSuccess {Number} to 结束数据.
     * @apiSuccess {Number} total_money 当前条件总金额.
     * @apiSuccess {String} orderid 单id
     * @apiSuccess {String} ordersn 订单编号
     * @apiSuccess {String} priceall 交易金额
     * @apiSuccess {String} add_time 下单时间
     * @apiSuccess {String} pay_time 付款时间
     * @apiSuccess {String} user_id 付款人id
     * @apiSuccess {String} ispay 交易状态 1 未付款  2 已付款
     * @apiSuccess {String} user.username 用户臭美号
     * @apiSuccess {String} user.mobilephone 用户手机号
     * @apiSuccess {String} salon.salonname 店铺名称
     * @apiSuccess {String} fundflow.pay_type 支付方式  1 网银 2 支付宝 3 微信 4 余额 5 红包 6 优惠券 7 积分 8邀请码兑换 10易联
     *
     * @apiSuccessExample Success-Response:
     *       {
     *           "result": 1,
     *           "token": "",
     *           "data": {
     *               "total": 66756,
     *               "per_page": 20,
     *               "current_page": 1,
     *               "last_page": 3338,
     *               "from": 1,
     *               "to": 20,
     *               "total_money": "4502452.48",
     *               "data": [
     *                   {
     *                       "orderid": 99078,
     *                       "ordersn": "3386040911815",
     *                       "priceall": "40.00",
     *                       "salonid": 816,
     *                       "add_time": 1433860410,
     *                       "pay_time": 1433860472,
     *                       "user_id": 746840,
     *                       "ispay": 2,
     *                       "user": {
     *                           "user_id": 746840,
     *                           "username": "10744850",
     *                           "mobilephone": "13570859516"
     *                       },
     *                       "salon": {
     *                           "salonid": 816,
     *                           "salonname": "阳光美业美发店"
     *                       },
     *                       "fundflow": [
     *                           {
     *                               "record_no": "3386040911815",
     *                               "pay_type": 4
     *                           },
     *                           {
     *                               "record_no": "3386040911815",
     *                               "pay_type": 10
     *                           }
     *                       ]
     *                   },
     *                   {
     *                       "orderid": 99077,
     *                       "ordersn": "3386036311203",
     *                       "priceall": "83.00",
     *                       "salonid": 554,
     *                       "add_time": 1433860363,
     *                       "pay_time": 1433860540,
     *                       "user_id": 746842,
     *                       "ispay": 2,
     *                       "user": {
     *                           "user_id": 746842,
     *                           "username": "10744852",
     *                           "mobilephone": "18524166621"
     *                       },
     *                       "salon": {
     *                           "salonid": 554,
     *                           "salonname": "BREEZE.Hair发型屋"
     *                       },
     *                       "fundflow": []
     *                   }
     *               ]
     *           }
     *       }
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
            'pay_time_min' => self::T_STRING,
            'pay_time_max' => self::T_STRING,
            'pay_type' => self::T_STRING,
            'pay_state' => self::T_INT,
            'page' => self::T_INT,
            'page_size' => self::T_INT,
            'sort_key' => self::T_STRING,
            'sort_type' => self::T_STRING,
       ]);
       
       $items = TransactionSearchApi::searchOfOrder($params);
       return $this->success($items);
    }

    /**
     * @api {get} /order/show/{id} 2.订单详情
     * @apiName show
     * @apiGroup order
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
        $item = TransactionSearchApi::orderDetail($id);
        return $this->success($item);
    }
    
     /**
     * @api {get} /order/export 3.订单导出
     * @apiName export
     * @apiGroup order
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
