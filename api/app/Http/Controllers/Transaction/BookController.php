<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\TransactionSearchApi;
use App\Mapping;
use Event;
use App\BookingOrder;

class BookController extends Controller
{
    /**
     * @api {get} /book/index 1.预约单列表
     * @apiName index
     * @apiGroup book
     *
     * @apiParam {Number} key  1手机号  2预约号 3推荐码
     * @apiParam {String} keyword  根据key来的关键字
     * @apiParam {String} min_time 预约时间 YYYY-MM-DD
     * @apiParam {String} max_time 预约时间 YYYY-MM-DD
     * @apiParam {String} pay_type 0 全部 2 支付宝 3 微信 10易联
     * @apiParam {String} status 0 全部  NEW - 未支付 PYD - 已支付  CSD - 已消费  RFN - 申请退款  RFD - 已退款
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
     * @apiSuccess {String} order 预约单
     * @apiSuccess {String} order.ID 预约单ID
     * @apiSuccess {String} order.ORDER_SN 订单号
     * @apiSuccess {String} order.BOOKING_SN 预约号
     * @apiSuccess {String} order.BOOKING_DATE 预约日期
     * @apiSuccess {String} order.UPDATED_BOOKING_DATE 修改后的预约日期
     * @apiSuccess {String} order.QUANTITY 数量
     * @apiSuccess {String} order.AMOUNT 订单金额
     * @apiSuccess {String} order.PAYABLE 应付金额
     * @apiSuccess {String} order.BOOKER_NAME 预约人姓名
     * @apiSuccess {String} order.BOOKER_PHONE 预约人电话
     * @apiSuccess {String} order.BOOKER_NAME 预约人姓名
     * @apiSuccess {String} order.STATUS 订单状态 NEW - 未支付,PYD - 已支付,CSD - 已消费,RFN - 申请退款,RFD - 已退款
     * @apiSuccess {String} order.TOUCHED_UP 是否已补妆 Y:是 N(空):否
     * @apiSuccess {String} order.PAIED_TIME 支付时间
     * @apiSuccess {String} order.CONSUME_TIME 消费时间
     * @apiSuccess {String} order.CREATE_TIME 预约时间
     * @apiSuccess {String} order.UPDATE_TIME 最近修改时间
     * @apiSuccess {String} order_item 项目信息
     * @apiSuccess {String} order_item.ITEM_NAME 项目名称
     *
     * @apiSuccessExample Success-Response:
     *       {
     *         "result": 1,
     *         "token": "",
     *         "data": {
     *           "total": 1,
     *           "per_page": 20,
     *           "current_page": 1,
     *           "last_page": 1,
     *           "from": 1,
     *           "to": 1,
     *           "data": [
     *             {
     *               "ID": 1,
     *               "ORDER_SN": "3891556931672",
     *               "BOOKING_SN": "sad2323232",
     *               "USER_ID": 1,
     *               "BOOKING_DATE": "2015-12-07",
     *               "UPDATED_BOOKING_DATE": null,
     *               "QUANTITY": 0,
     *               "AMOUNT": "0.00",
     *               "PAYABLE": "0.00",
     *               "BOOKER_NAME": "",
     *               "BOOKER_PHONE": null,
     *               "STATUS": "RFN",
     *               "TOUCHED_UP": null,
     *               "INSTRUCTIONS": null,
     *               "PAIED_TIME": "2015-10-12 00:00:00",
     *               "CONSUME_TIME": null,
     *               "CREATE_TIME": "2015-12-01 17:18:23",
     *               "UPDATE_TIME": "0000-00-00 00:00:00",
     *               "record_no": "3891556931672",
     *               "pay_type": 2,
     *               "booking_order_item": [
     *                 {
     *                   "ORDER_SN": "3891556931672",
     *                   "ITEM_NAME": "测试时"
     *                 },
     *                 {
     *                   "ORDER_SN": "3891556931672",
     *                   "ITEM_NAME": "韩式提拉"
     *                 }
     *               ]
     *             }
     *           ]
     *         }
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
            'min_time_' => self::T_STRING,
            'max_time' => self::T_STRING,
            'pay_type' => self::T_INT,
            'pay_state' => self::T_STRING,
            'page' => self::T_INT,
            'page_size' => self::T_INT,
            'sort_key' => self::T_STRING,
            'sort_type' => self::T_STRING,
       ]);
       
       $items = BookingOrder::search($params);
       return $this->success($items);
    }

    /**
     * @api {get} /book/show/{id} 2.预约单详情
     * @apiName show
     * @apiGroup book
     *
     * @apiSuccess {String} order 预约单
     * @apiSuccess {String} order.ID 预约单ID
     * @apiSuccess {String} order.ORDER_SN 订单号
     * @apiSuccess {String} order.BOOKING_SN 预约号
     * @apiSuccess {String} order.BOOKING_DATE 预约日期
     * @apiSuccess {String} order.UPDATED_BOOKING_DATE 修改后的预约日期
     * @apiSuccess {String} order.QUANTITY 数量
     * @apiSuccess {String} order.AMOUNT 订单金额
     * @apiSuccess {String} order.PAYABLE 应付金额
     * @apiSuccess {String} order.BOOKER_NAME 预约人姓名
     * @apiSuccess {String} order.BOOKER_PHONE 预约人电话
     * @apiSuccess {String} order.BOOKER_NAME 预约人姓名
     * @apiSuccess {String} order.STATUS 订单状态 NEW - 未支付,PYD - 已支付,CSD - 已消费,RFN - 申请退款,RFD - 已退款
     * @apiSuccess {String} order.TOUCHED_UP 是否已补妆 Y:是 N(空):否
     * @apiSuccess {String} order.PAIED_TIME 支付时间
     * @apiSuccess {String} order.CONSUME_TIME 消费时间
     * @apiSuccess {String} order.CREATE_TIME 预约时间
     * @apiSuccess {String} order.UPDATE_TIME 最近修改时间
     * @apiSuccess {String} order_item 项目信息
     * @apiSuccess {String} order_item.ITEM_NAME 项目名称
     * @apiSuccess {String} fundflow 金额构成
     * @apiSuccess {String} fundflow.pay_type 支付方式  1 网银 2 支付宝 3 微信 4 余额 5 红包 6 优惠券 7 积分 8邀请码兑换 10易联
     * @apiSuccess {String} paymentlog 流水信息
     * @apiSuccess {String} paymentlog.tn 第三方流水号
     * @apiSuccess {String} booking_bill 发票信息
     * @apiSuccess {String} booking_bill.created_at 开发票时间
     * @apiSuccess {String} booking_bill.manager 开发票操作人信息
     * @apiSuccess {String} booking_cash 收银信息
     * @apiSuccess {String} booking_cash.pay_type 支付方式1:微信2:支付宝3:POS机,4:现金,5:微信+现金6:支付宝+现金7:POS机+现金
     * @apiSuccess {String} booking_cash.other_money 除现金外的其他支付金额
     * @apiSuccess {String} booking_cash.cash_money 现金金额
     * @apiSuccess {String} booking_cash.deduction_money 现金金额
     * @apiSuccess {String} booking_cash.created_at 收银时间
     * @apiSuccess {String} booking_cash.manager 操作人信息
     * @apiSuccess {String} booking_cash.expert 专家信息
     * @apiSuccess {String} booking_cash.assistant 助理信息
     * @apiSuccess {String} booking_receive 接待信息
     * @apiSuccess {String} booking_receive.update_booking_date 接待信息
     * @apiSuccess {String} booking_receive.arrive_at 到店时间
     * @apiSuccess {String} booking_receive.created_at 接待时间
     * @apiSuccess {String} booking_receive.manager 接待人信息
     * @apiSuccess {String} booking_salon_refund 退款信息
     * @apiSuccess {String} booking_salon_refund.back_to 退款方式1:微信2:支付宝3:银联,4:现金'
     * @apiSuccess {String} booking_salon_refund.money 退款金额
     * @apiSuccess {String} booking_salon_refund.remark 退款说明
     * @apiSuccess {String} booking_salon_refund.created_at 退款时间
     * @apiSuccess {String} booking_salon_refund.manager 退款人信息     
     *
     *       {
     *         "result": 1,
     *         "token": "",
     *         "data": {
     *           "order": {
     *             "ID": 1,
     *             "ORDER_SN": "3891556931672",
     *             "BOOKING_SN": "sad2323232",
     *             "USER_ID": 1,
     *             "BOOKING_DATE": "2015-12-07",
     *             "UPDATED_BOOKING_DATE": null,
     *             "QUANTITY": 0,
     *             "AMOUNT": "0.00",
     *             "PAYABLE": "0.00",
     *             "BOOKER_NAME": "",
     *             "BOOKER_PHONE": null,
     *             "STATUS": "RFN",
     *             "TOUCHED_UP": null,
     *             "INSTRUCTIONS": null,
     *             "PAIED_TIME": "2015-10-12 00:00:00",
     *             "CONSUME_TIME": null,
     *             "CREATE_TIME": "2015-12-01 17:18:23",
     *             "UPDATE_TIME": "0000-00-00 00:00:00",
     *             "user": {
     *               "user_id": 1,
     *               "nickname": "小康",
     *               "sex": 2
     *             }
     *           },
     *           "order_item": [
     *             {
     *               "ORDER_SN": "3891556931672",
     *               "ITEM_ID": 111,
     *               "ITEM_NAME": "测试时"
     *             },
     *             {
     *               "ORDER_SN": "3891556931672",
     *               "ITEM_ID": 2,
     *               "ITEM_NAME": "韩式提拉"
     *             }
     *           ],
     *           "fundflow": [
     *             {
     *               "record_no": "3891556931672",
     *               "pay_type": 2
     *             }
     *           ],
     *           "payment_log": {
     *             "ordersn": "3891556931672",
     *             "tn": "1002360799201508070568495032",
     *             "amount": "1.00"
     *           },
     *           "booking_bill": {
     *             "id": 1,
     *             "booking_id": 1,
     *             "booking_sn": "sad2323232",
     *             "order_sn": "3891556931672",
     *             "created_at": "2015-12-01 00:00:00",
     *             "uid": 1,
     *             "manager": {
     *               "id": 1,
     *               "name": "超级管理员"
     *             }
     *           },
     *           "booking_cash": {
     *             "id": 1,
     *             "booking_id": 1,
     *             "booking_sn": "fasdfasdf",
     *             "order_sn": "fasdfasdfasdf",
     *             "booking_cash": 1,
     *             "other_money": "10.00",
     *             "cash_money": "20.00",
     *             "deduction_money": "30.00",
     *             "expert_uid": 1,
     *             "assistant_uid": 1,
     *             "created_at": "2015-12-02 00:00:00",
     *             "uid": 1,
     *             "manager": {
     *               "id": 1,
     *               "name": "超级管理员"
     *             },
     *             "expert": {
     *               "artificer_id": 1,
     *               "name": "XIAOd",
     *               "number": "6"
     *             },
     *             "assistant": {
     *               "artificer_id": 1,
     *               "name": "XIAOd",
     *               "number": "6"
     *             }
     *           },
     *           "booking_receive": {
     *             "id": 1,
     *             "booking_id": 1,
     *             "booking_sn": "fasdfa",
     *             "order_sn": "fasdfasdf",
     *             "update_booking_date": "2015-12-03",
     *             "money": "369.00",
     *             "remark": "fasdfasdfasdfafasdf",
     *             "arrive_at": "2015-12-01 00:00:00",
     *             "created_at": "2015-12-10 00:00:00",
     *             "uid": 1,
     *             "manager": {
     *               "id": 1,
     *               "name": "超级管理员"
     *             }
     *           },
     *           "booking_salon_refund": {
     *             "id": 1,
     *             "booking_id": 1,
     *             "uid": 1,
     *             "booking_sn": "2334",
     *             "order_sn": "fasdfadfasdf",
     *             "back_to": 1,
     *             "money": "2323.00",
     *             "remark": "fasdfasdfas",
     *             "created_at": "2015-12-03 00:00:00",
     *             "manager": {
     *               "id": 1,
     *               "name": "超级管理员"
     *             }
     *           }
     *         }
     *       }
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
        $item = BookingOrder::detail($id);
        return $this->success($item);
    }
    
    /**
     * @api {get} /book/receive/{id} 3.预约单--接待
     * @apiName receive
     * @apiGroup book
     * 
     * @apiSuccessExample Success-Response:
     *       {
     *       }
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function receive($id)
    {
        return $this->success(['id'=>$id]);
    }
    
    /**
     * @api {get} /book/cash/{id} 4.预约单--收银
     * @apiName cash
     * @apiGroup book
     *
     * @apiSuccessExample Success-Response:
     *       {
     *       }
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function cash($id)
    {
        return $this->success(['id'=>$id]);
    }
    
    /**
     * @api {get} /book/bill/{id} 5.预约单--开发票
     * @apiName bill
     * @apiGroup book
     *
     * @apiSuccessExample Success-Response:
     *       {
     *       }
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function bill($id)
    {
        return $this->success(['id'=>$id]);
    }
    
    /**
     * @api {get} /book/relatively/{id} 6.预约单--补色
     * @apiName relatively
     * @apiGroup book
     *
     * @apiSuccessExample Success-Response:
     *       {
     *       }
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function relatively($id)
    {
        return $this->success(['id'=>$id]);
    }
    
    /**
     * @api {get} /book/refund/{id} 7.预约单--退款
     * @apiName refund
     * @apiGroup book
     *
     * @apiSuccessExample Success-Response:
     *       {
     *       }
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function refund($id)
    {
        return $this->success(['id'=>$id]);
    }
    
    
}