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
     * @apiSuccess {Number} all_amount 当前条件总应付金额.
     * @apiSuccess {Number} paied_amount 当前条件实付金额.
     * @apiSuccess {String} order_ticket_id 臭美券id
     * @apiSuccess {String} ticketno 臭美券密码
     * @apiSuccess {String} add_time 付款时间
     * @apiSuccess {String} use_time 使用时间
     * @apiSuccess {String} ordersn 订单编号
     * @apiSuccess {String} priceall_ori 应付金额
     * @apiSuccess {String} actuallyPay 实付金额
     * @apiSuccess {String} user_id 付款人id
     * @apiSuccess {String} status 状态  2未使用，4使用完成，6申请退款，7退款完成,8退款中
     * @apiSuccess {String} shopcartsn 购物车号
     * @apiSuccess {String} user 用户信息
     * @apiSuccess {String} user.username 用户臭美号
     * @apiSuccess {String} user.mobilephone 用户手机号
     * @apiSuccess {String} salon 店铺信息
     * @apiSuccess {String} salon.salonname 店铺名称
     * @apiSuccess {String} fundflow 支付信息
     * @apiSuccess {String} fundflow.pay_type 支付方式  1 网银 2 支付宝 3 微信 4 余额 5 红包 6 优惠券 7 积分 8邀请码兑换 10易联
     * @apiSuccess {String} voucher 佣金信息
     * @apiSuccess {String} voucher.vcSn 活动编号
     * @apiSuccess {String} voucher.vSn 代金券编号
     *
     * @apiSuccessExample Success-Response:
     *       {
     *           "result": 1,
     *           "token": "",
     *           "data": {
     *               "total": 108327,
     *               "per_page": 20,
     *               "current_page": 1,
     *               "last_page": 5417,
     *               "from": 1,
     *               "to": 20,
     *               "data": [
     *                   {
     *                       "order_ticket_id": 108900,
     *                       "ticketno": "24610518",
     *                       "add_time": 1441963005,
     *                       "use_time": 0,
     *                       "user_id": 707716,
     *                       "status": 2,
     *                       "ordersn": "4196296911121",
     *                       "salonid": 669,
     *                       "priceall_ori": "20.00",
     *                       "actuallyPay": "0.00",
     *                       "shopcartsn": "",
     *                       "user": {
     *                           "user_id": 707716,
     *                           "username": "10705726",
     *                           "mobilephone": "18576617068"
     *                       },
     *                       "salon": {
     *                           "salonid": 669,
     *                           "salonname": "苏苏美发"
     *                       },
     *                       "fundflow": [
     *                           {
     *                               "record_no": "4196296911121",
     *                               "pay_type": 9
     *                           }
     *                       ],
     *                       "voucher": {
     *                           "vOrderSn": "4196296911121",
     *                           "vcSn": "cm164288",
     *                           "vSn": "CM41678592782"
     *                       }
     *                   },
     *                   {
     *                       "order_ticket_id": 108899,
     *                       "ticketno": "31789371",
     *                       "add_time": 1441961991,
     *                       "use_time": 0,
     *                       "user_id": 707716,
     *                       "status": 2,
     *                       "ordersn": "4196193611328",
     *                       "salonid": 7,
     *                       "priceall_ori": "148.00",
     *                       "actuallyPay": "49.00",
     *                       "shopcartsn": "",
     *                       "user": {
     *                           "user_id": 707716,
     *                           "username": "10705726",
     *                           "mobilephone": "18576617068"
     *                       },
     *                       "salon": {
     *                           "salonid": 7,
     *                           "salonname": "丝凡达护肤造型会所（麒麟店）"
     *                       },
     *                       "fundflow": [
     *                           {
     *                               "record_no": "4196193611328",
     *                               "pay_type": 9
     *                           },
     *                           {
     *                               "record_no": "4196193611328",
     *                               "pay_type": 4
     *                           }
     *                       ],
     *                       "voucher": {
     *                           "vOrderSn": "4196193611328",
     *                           "vcSn": "cm964309",
     *                           "vSn": "CM41520035796"
     *                       }
     *                   }
     *               ],
     *               "all_amount": "11240369.00",
     *               "paied_amount": "42359.01"
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
     * @apiSuccess {String} ticket 臭美券信息
     * @apiSuccess {String} ticket.ticketno 臭美券密码
     * @apiSuccess {String} paymentlog 流水信息
     * @apiSuccess {String} paymentlog.tn 第三方流水号
     * @apiSuccess {String} item 项目信息
     * @apiSuccess {String} item.itemname 项目名称
     * @apiSuccess {String} salon 店铺信息
     * @apiSuccess {String} salon.salonname 店铺名称
     * @apiSuccess {String} user 用户信息
     * @apiSuccess {String} user.username 用户臭美号
     * @apiSuccess {String} user.mobilephone 用户手机号
     * @apiSuccess {String} order 订单信息
     * @apiSuccess {String} order.ordersn 订单编号
     * @apiSuccess {String} order.shopcartsn 购物车序号 
     * @apiSuccess {String} order.priceall 订单金额
     * @apiSuccess {String} order.actuallyPay 实付金额
     * @apiSuccess {String} fundflow 金额构成
     * @apiSuccess {String} fundflow.pay_type 支付方式  1 网银 2 支付宝 3 微信 4 余额 5 红包 6 优惠券 7 积分 8邀请码兑换 10易联
     * @apiSuccess {String} fundflow.money 支付金额
     * @apiSuccess {String} trends 臭美券动态 
     * @apiSuccess {String} trends.add_time 臭美券动态.时间  
     * @apiSuccess {String} trends.status 臭美券动态.行为   [2未使用，4使用完成，6申请退款，7退款完成，8退款拒绝,10退款中]
     * @apiSuccess {String} trends.remark  臭美券动态.行为 备注信息,为空时显示 上面status 对应的信息
     * @apiSuccess {String} vouchers 代金券动态  
     * @apiSuccess {String} commission 佣金信息
     * @apiSuccess {String} recommend_code店铺优惠码
     * @apiSuccess {String} platform 设备信息
     *
     * @apiSuccessExample Success-Response:
     *       {
     *           "result": 1,
     *           "token": "",
     *           "data": {
     *               "order": {
     *                   "ordersn": "4187664711988",
     *                   "orderid": 708851,
     *                   "priceall": "1.00",
     *                   "salonid": 84,
     *                   "actuallyPay": "1.00",
     *                   "shopcartsn": ""
     *               },
     *               "item": {
     *                   "order_item_id": 150256,
     *                   "itemname": "柠檬去味吹发变身柠檬女神",
     *                   "ordersn": "4187664711988"
     *               },
     *               "ticket": {
     *                   "order_ticket_id": 108898,
     *                   "ticketno": "17170134",
     *                   "user_id": 306669
     *               },
     *               "user": {
     *                   "username": "10306576",
     *                   "mobilephone": "18319019483"
     *               },
     *               "salon": {
     *                   "salonname": "苏格护肤造型生活馆（2店）"
     *               },
     *               "paymentlog": {
     *                    "ordersn": "4187664711988",
     *                    "tn": "1224362901341509107433258086"
     *               },
     *               "fundflows": [
     *                   {
     *                       "pay_type": 10,
     *                       "money": "1.00"
     *                   }
     *               ],
     *               "trends": [
     *                   {
     *                       "add_time": 1441876684,
     *                       "status": 2,
     *                       "remark": "未使用"
     *                   }
     *               ],
     *               "vouchers": [],
     *               "commission": null,
     *               "recommend_code": null
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
