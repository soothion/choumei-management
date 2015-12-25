<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\TransactionSearchApi;
use App\Mapping;
use Event;
use App\BookingOrder;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;
use App\BookingSalonRefund;
use App\BeautyMakeup;
use App\BookingBill;
use App\BookingCash;
use App\BookingReceive;
use App\RecommendCodeUser;
use App\User;
use App\Http\Controllers\Powder\PowderArticlesController;
use App\Order;
use App\Dividend;
use App\Utils;
use App\BeautyRefundApi;

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
     * @apiParam {String} status 0 全部  NEW - 未支付  PYD - 已支付  (未消费)CSD - 已消费  RFN - 申请退款(退款中)  RFD - 已退款  Y已补妆 退款失败FAILD
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
     * @apiSuccess {String} order.pay_type 支付方式:1 网银/2 支付宝/3 微信/4 余额/5 红包/6 优惠券/7 积分/8邀请码兑换 /9 现金券/10 易联支付
     * @apiSuccess {String} order.recommend_code 推荐码
     * @apiSuccess {String} order.beauty_makeup_id 是否已补妆 为空时为否 
     * @apiSuccess {String} order.refund_status 为3时为退款失败
     * @apiSuccess {String} booking_order_item 预约项目信息
     * @apiSuccess {String} booking_order_item.ITEM_NAME 项目名称
     * @apiSuccess {String} beauty_order_item 实际项目信息
     * @apiSuccess {String} beauty_order_item.item_name 项目名称
     * @apiSuccess {String} beauty_order_item.norm_name 项目规格名称
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
     *               "beauty_makeup_id":null,
     *               "refund_status":1,
     *               "recommend_code":null,
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
     *               "beauty_order_item": [
     *                 {
     *                   "order_sn": "3891556931672",
     *                   "item_name": "测试时"
     *                 },
     *                 {
     *                   "order_sn": "3891556931672",
     *                   "item_name": "韩式提拉"
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
            'min_time' => self::T_STRING,
            'max_time' => self::T_STRING,
            'pay_type' => self::T_INT,
            'status' => self::T_STRING,
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
     * @apiSuccess {String} order.UPDATED_BOOKING_DATE 修改后的预约日期 / 客服调整的预约日期
     * @apiSuccess {String} order.QUANTITY 数量
     * @apiSuccess {String} order.AMOUNT 订单金额
     * @apiSuccess {String} order.PAYABLE 应付金额(已支付)
     * @apiSuccess {String} order.BOOKER_NAME 预约人姓名
     * @apiSuccess {String} order.BOOKER_SEX 预约人性别
     * @apiSuccess {String} order.BOOKER_PHONE 预约人电话
     * @apiSuccess {String} order.BOOKER_NAME 预约人姓名
     * @apiSuccess {String} order.STATUS 订单状态 NEW - 未支付,PYD - 已支付,CSD - 已消费,RFN - 申请退款,RFD - 已退款  RFD-OFL 线下已退款 RFE退款失败
     * @apiSuccess {String} order.PAIED_TIME 支付时间
     * @apiSuccess {String} order.CONSUME_TIME 消费时间
     * @apiSuccess {String} order.CREATE_TIME 预约时间
     * @apiSuccess {String} order.UPDATE_TIME 最近修改时间
     * @apiSuccess {String} order.BOOKING_DESC 预约时间  DEF-未选择，MORNING - 上午，AFTERNOON下午
     * @apiSuccess {Object} order.manager 客服信息
     * @apiSuccess {Object} help_info 代预约信息
     * @apiSuccess {String} help_info.from 渠道
     * @apiSuccess {String} help_info.recommend_code 推荐码
     * @apiSuccess {String} help_info.mobilephone 手机号
     * @apiSuccess {String} order.item_amount 项目总价
     * @apiSuccess {String} order_item 预约项目信息
     * @apiSuccess {String} order_item.ID 项目ID
     * @apiSuccess {String} order_item.ITEM_NAME 项目名称
     * @apiSuccess {String} order_item.AMOUNT 预约金总额
     * @apiSuccess {String} order_item.PAYABLE 应付总额
     * @apiSuccess {String} beauty_order_item 实做项目信息
     * @apiSuccess {String} beauty_order_item.item_id 项目ID
     * @apiSuccess {String} beauty_order_item.item_name 项目名称
     * @apiSuccess {String} beauty_order_item.amout 总额
     * @apiSuccess {String} beauty_order_item.to_pay_amount 应付总额
     * @apiSuccess {String} beauty_order_item.norm_id 项目规格ID
     * @apiSuccess {String} beauty_order_item.norm_name 项目规格名称
     * @apiSuccess {String} fundflow 金额构成
     * @apiSuccess {String} fundflow.pay_type 支付方式  1 网银 2 支付宝 3 微信 4 余额 5 红包 6 优惠券 7 积分 8邀请码兑换 10易联
     * @apiSuccess {String} paymentlog 流水信息
     * @apiSuccess {String} paymentlog.tn 第三方流水号
     * @apiSuccess {String} recommend 推荐信息
     * @apiSuccess {String} recommend.recommend_code 推荐码
     * @apiSuccess {String} makeup 补妆信息
     * @apiSuccess {String} makeup.remark 说明
     * @apiSuccess {String} makeup.work_at 补妆时间
     * @apiSuccess {String} makeup.created_at 操作时间
     * @apiSuccess {String} makeup.manager 操作人信息
     * @apiSuccess {String} makeup.expert 专家信息
     * @apiSuccess {String} makeup.assistant 助理信息
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
     * @apiSuccess {String} booking_receive.update_booking_date 实际接待日期
     * @apiSuccess {String} booking_receive.remark 沟通记录
     * @apiSuccess {String} booking_receive.arrive_at 到店时间
     * @apiSuccess {String} booking_receive.created_at 接待时间
     * @apiSuccess {String} booking_receive.state 接待状态 0:失效,1正常
     * @apiSuccess {String} booking_receive.manager 接待人信息
     * @apiSuccess {String} booking_salon_refund 退款信息(特殊退款)
     * @apiSuccess {String} booking_salon_refund.back_to 退款方式1:微信2:支付宝3:银联,4:现金
     * @apiSuccess {String} booking_salon_refund.money 退款金额
     * @apiSuccess {String} booking_salon_refund.remark 退款说明
     * @apiSuccess {String} booking_salon_refund.created_at 退款时间
     * @apiSuccess {String} booking_salon_refund.manager 退款人信息     
     * @apiSuccess {String} order_refund 退款信息(客服申请的退款)
     * @apiSuccess {String} order_refund.add_time 申请退款时间
     * @apiSuccess {String} order_refund.opt_time 退款审批时间
     * @apiSuccess {String} order_refund.manager  审批人信息    
     * 
     * @apiSuccessExample Success-Response:
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
     *             "UPDATED_BOOKING_DATE": "2015-12-03",
     *             "QUANTITY": 0,
     *             "AMOUNT": "100.00",
     *             "PAYABLE": "100.00",
     *             "BOOKER_NAME": "预约人",
     *             "BOOKER_SEX": "F",
     *             "BOOKER_PHONE": "18611112222",
     *             "STATUS": "RFN",
     *             "INSTRUCTIONS": "1",
     *             "PAIED_TIME": "2015-10-12 00:00:00",
     *             "CONSUME_TIME": "2015-12-03 16:15:32",
     *             "CREATE_TIME": "2015-12-01 17:18:23",
     *             "UPDATE_TIME": "2015-12-03 16:23:01",
     *             "item_amount": 120
     *           },
     *           "help_info":{
     *              "from":"choumei_test",
     *              "recommend_code":"134578",
     *              "mobilephone":"13456789451",
     *           }
     *           "order_item": [
     *             {
     *               "ORDER_SN": "3891556931672",
     *               "ITEM_ID": 111,
     *               "ITEM_NAME": "测试时",
     *               "AMOUNT": "0.00",
     *               "PAYABLE": "0.00"
     *             },
     *             {
     *               "ORDER_SN": "3891556931672",
     *               "ITEM_ID": 2,
     *               "ITEM_NAME": "韩式提拉",
     *               "AMOUNT": "0.00",
     *               "PAYABLE": "0.00"
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
     *           "recommend": {
     *              "recommend_code":"123456789",
     *           },
     *           "beauty_order_item": [
     *             {
     *               "order_sn": "3891556931672",
     *               "item_id": 3,
     *               "item_name": "韩式无痛水光针",
     *               "amount": "850.00",
     *               "to_pay_amount": "120.00",
     *               "norm_id":0,
     *               "norm_name":"",
     *             }
     *           ],
     *           "makeup": {
     *             "id": 1,
     *             "booking_id": 1,
     *             "booking_sn": "fasdfasdf",
     *             "order_sn": "fasdfasdf",
     *             "expert_uid": 18,
     *             "assistant_uid": 17,
     *             "work_at": "2015-12-06",
     *             "remark": "fasdfadfasdfasdfasdfasdfasdfasdf",
     *             "uid": 1,
     *             "created_at": "2015-12-03 00:00:00",
     *             "manager": {
     *               "id": 1,
     *               "name": "超级管理员"
     *             },
     *             "expert": {
     *               "artificer_id": 18,
     *               "name": "ddfdsfs",
     *               "number": "M222"
     *             },
     *             "assistant": {
     *               "artificer_id": 17,
     *               "name": "fsfsffff",
     *               "number": "M982"
     *             }
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
     *             "pay_type": 1,
     *             "other_money": "10.00",
     *             "cash_money": "20.00",
     *             "deduction_money": "30.00",
     *             "expert_uid": 17,
     *             "assistant_uid": 18,
     *             "created_at": "2015-12-02 00:00:00",
     *             "uid": 1,
     *             "manager": {
     *               "id": 1,
     *               "name": "超级管理员"
     *             },
     *             "expert": {
     *               "artificer_id": 17,
     *               "name": "fsfsffff",
     *               "number": "M982"
     *             },
     *             "assistant": {
     *               "artificer_id": 18,
     *               "name": "ddfdsfs",
     *               "number": "M222"
     *             }
     *           },
     *           "booking_receive": {
     *             "id": 1,
     *             "booking_id": 1,
     *             "booking_sn": "sad23232321",
     *             "order_sn": "fasdfasdf",
     *             "update_booking_date": "2015-12-03",
     *             "remark": "fasdfasdfasdfafasdf",
     *             "arrive_at": "2015-12-01 00:00:00",
     *             "created_at": "2015-12-10 00:00:00",
     *             "state":1,
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
     *           },
     *           "order_refund": {
     *             "ordersn": "3891556931672",
     *             "user_id": 720005,
     *             "money": "1.00",
     *             "opt_user_id": 0,
     *             "rereason": "2",
     *             "add_time": 1438926590,
     *             "opt_time": 0,
     *             "status": 2,
     *             "booking_sn": "",
     *             "item_type": "MF",
     *             "manager": null
     *           }
     *         }
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
     * @api {get} /book/create 3.预约单--新增代客预约单
     * @apiName create
     * @apiGroup book
     *
     * @apiParam {string}  phone 手机
     * @apiParam {string}  name 姓名
     * @apiParam {string}  sex 1.男 2女
     * @apiParam {string}  item_ids 预约项目(多个用','隔开)
     * @apiParam {string}  date 预约日期 YYYY-MM-DD
     * @apiParam {string}  recomment_code 推荐码
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
    public function create()
    {
        $params = $this->parameters([
            'phone'=>self::T_STRING,
            'name'=>self::T_STRING,
            'sex'=>self::T_INT,
            'item_ids'=>self::T_STRING,
            'date'=>self::T_STRING,
            'recomment_code'=>self::T_STRING,
        ]);
        $params['item_ids'] = explode(",", $params['item_ids']);
        if( count($params['item_ids'])<1)
        {
            throw new ApiException("预约项目不能为空!",ERROR::PARAMETER_ERROR);
        }
        //for test
        //$params['manager_uid'] = 1;
        $params['manager_uid'] = $this->user->id;
        $book = BookingOrder::book($params);
        
        //Event::fire('booking.create',"预约号".$book['BOOKING_SN']." "."订单号".$book['ORDER_SN']);
        return $this->success($book);
    }
    
    /**
     * @api {get} /book/receive/{id} 4.预约单--接待
     * @apiName receive
     * @apiGroup book
     * 
     * @apiParam {Number} arrive_at 到店时间   YYYY-MM-DD
     * @apiParam {String} update_booking_date 修改预约时间 YYYY-MM-DD
     * @apiParam {String} remark 沟通记录
     * @apiParam {String} item_ids 预约项目修改  多个id用','隔开 {item_id1}[_{norm_id1}[_{norm_id2}]],{item_id2}[_{norm_id3}[_{norm_id4}]]...
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
        $params = $this->parameters([
            'arrive_at' => self::T_STRING,
            'update_booking_date' => self::T_STRING,
            'remark' => self::T_STRING,
            'item_ids' => self::T_STRING,
       ],true);
       $params['item_ids'] = explode(",", $params['item_ids']);
       $params['uid'] = $this->user->id;
       $book = BookingReceive::receive($id,$params);
       Event::fire('booking.cash',"预约号".$book['BOOKING_SN']." "."订单号".$book['ORDER_SN']);
       return $this->success(['id'=>$id]);
    }
    
    /**
     * @api {get} /book/cash/{id} 5.预约单--收银
     * @apiName cash
     * @apiGroup book
     *
     * @apiParam {Number} pay_type 支付方式1:微信2:支付宝3:POS机,4:现金,5:微信+现金6:支付宝+现金7:POS机+现金
     * @apiParam {Number} other_money 其他方式的支付金额
     * @apiParam {Number} cash_money 现金金额
     * @apiParam {Number} deduction_money 抵扣金额
     * @apiParam {Number} specialistId 专家id
     * @apiParam {Number} assistantId 助理id
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
        $params = $this->parameters([
            'pay_type' => self::T_INT,
            'other_money' => self::T_FLOAT,
            'cash_money' => self::T_FLOAT,
            'deduction_money' => self::T_FLOAT,
            'specialistId'=> self::T_INT,
            'assistantId'=> self::T_INT,
        ]);
       // $params['uid'] = 1;
        $params['uid'] = $this->user->id;        
        $book = BookingCash::cash($id,$params);
        $custom_uid = $book['USER_ID'];
        $first_book = BookingOrder::where("USER_ID",$custom_uid)->whereIn("STATUS",["CSD","RFD-OFL"])->orderBy("CONSUME_TIME","ASC")->first();
        $is_first = false;
        if(! empty($first_book) && $first_book->USER_ID == $custom_uid)
        {
            $is_first = true;
        }
        $first_time = BookingOrder::where("USER_ID",$custom_uid)->orderBy("CREATE_TIME","ASC")->first();
        //self::givePresent($custom_uid,true);
        self::make_recommend($custom_uid,$book['RECOMMENDER']);
        try{
            self::givePresent($custom_uid,$book['ORDER_SN'],$is_first,$first_time->CREATE_TIME);
        }
        catch (\Exception $e)
        {
            Utils::log("present", date("Y-m-d H:i:s").$e->getMessage()."\n");
        }
        Event::fire('booking.cash',"预约号".$book['BOOKING_SN']." "."订单号".$book['ORDER_SN']);
        return $this->success(['id'=>$id]);
    }
    
    /**
     * @api {get} /book/bill/{id} 6.预约单--开发票
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
        $base = BookingOrder::where("ID",$id)->first();
        if(empty($base))
        {
            throw new ApiException("定妆单[{$id}]不存在或者已经被删除", ERROR::ORDER_NOT_EXIST);
        }
        $state = $base->STATUS;
        if($state != "CSD")
        {
            throw new ApiException("定妆单[{$id}]状态不正确", ERROR::ORDER_STATUS_WRONG);
        }       
        $bill = BookingBill::where('booking_id',$id)->first();
        if(!empty($bill))
        {
            throw new ApiException("定妆单[{$id}]已经开过发票", ERROR::ORDER_STATUS_WRONG);
        }
        BookingBill::create([
        'booking_id'=>$id,
        'uid'=>$this->user->id,
        'booking_sn'=>$base->BOOKING_SN,
        'order_sn'=>$base->ORDER_SN,
        'created_at'=>date("Y-m-d H:i:s"),
        ]);
        //Event::fire('booking.bill',"预约号".$base->BOOKING_SN." "."订单号".$base->ORDER_SN);
        return $this->success(['id'=>$id]);
    }
    
    /**
     * @api {get} /book/relatively/{id} 7.预约单--补色
     * @apiName relatively
     * @apiGroup book
     *
     * @apiParam {Number} specialistId 专家id
     * @apiParam {Number} assistantId 助理id
     * @apiParam {String} remark 说明
     * @apiParam {String} work_at 补色日期 YYYY-MM-DD

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
        $params = $this->parameters([
            'remark' => self::T_STRING,
            'work_at' => self::T_STRING,
            'specialistId'=> self::T_INT,
            'assistantId'=> self::T_INT,
        ]);
        $base = BookingOrder::where("ID",$id)->first();
        if(empty($base))
        {
            throw new ApiException("定妆单[{$id}]不存在或者已经被删除", ERROR::ORDER_NOT_EXIST);
        }
        $state = $base->STATUS;
        if($state != "CSD")
        {
            throw new ApiException("定妆单[{$id}]状态不正确", ERROR::ORDER_STATUS_WRONG);
        }       
        $makeup = BeautyMakeup::where('booking_id',$id)->first();
        if(!empty($makeup))
        {
            throw new ApiException("定妆单[{$id}]已经存在补妆信息", ERROR::ORDER_STATUS_WRONG);
        }
        BeautyMakeup::create([
            'booking_id'=>$id,
            'uid'=>$this->user->id,
            'booking_sn'=>$base->BOOKING_SN,
            'order_sn'=>$base->ORDER_SN,
            'work_at'=>date("Y-m-d",strtotime($params['work_at'])),
            'remark'=>$params['remark'],
            'expert_uid'=>$params['specialistId'],
            'assistant_uid'=>$params['assistantId'],
            'created_at'=>date("Y-m-d H:i:s"),
        ]);
        Event::fire('booking.relatively',"预约号".$base->BOOKING_SN." "."订单号".$base->ORDER_SN);
        return $this->success(['id'=>$id]);
    }
    
    /**
     * @api {get} /book/refund/{id} 8.预约单--退款
     * @apiName refund
     * @apiGroup book
     * 
     * @apiParam {Number} back_to 退款方式1:微信2:支付宝3:银联,4:现金
     * @apiParam {Number} money 金额
     * @apiParam {String} remark 说明
     *
     * @apiSuccess {Object} alipay 支付宝
     * @apiSuccess {String} wx 微信
     * @apiSuccess {String} balance 余额
     * @apiSuccess {String} yilian 易联
     *
     * @apiSuccessExample Success-Response:
     *     {
     *        "result": 1,
     *        "token": "",
     *        "data": {
     *            "alipay": {
     *                "form_args": {
     *                    "_input_charset": "utf-8",
     *                    "batch_no": "20150921153317",
     *                    "batch_num": "1",
     *                    "detail_data": "2015091600001000780065371963^25.00^买多了/买错了",
     *                    "notify_url": "http://192.168.13.46:9140/refund/call_back_of_alipay",
     *                    "partner": "2088701753684258",
     *                    "refund_date": "2015-09-21 15:33:17",
     *                    "seller_email": "zfb@choumei.cn",
     *                    "service": "refund_fastpay_by_platform_pwd",
     *                    "sign": "b2eb81f50f8de1b04a86e1fddb260f6e",
     *                    "sign_type": "MD5"
     *                }
     *            },
     *            "wx":{
     *              "info":"退款成功"
     *            },
     *            "balance":{
     *              "info":"退款成功"
     *            },
     *            "yilian":{
     *              "info":"退款失败<br> ordersn:xxxxx tn:xxxxx"
     *            }
     *        }
     *    }
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
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
        $params = $this->parameters([
            'remark' => self::T_STRING,
            'money' => self::T_FLOAT,
            'back_to' => self::T_INT,
        ]);
        $base = BookingOrder::where("ID",$id)->first();
        if(empty($base))
        {
            throw new ApiException("定妆单[{$id}]不存在或者已经被删除", ERROR::ORDER_NOT_EXIST);
        }
        $state = $base->STATUS;
        if($state != "CSD")
        {
            throw new ApiException("定妆单[{$id}]状态不正确,不允许退款", ERROR::ORDER_STATUS_WRONG);
        }
        $refund = BookingSalonRefund::where('id',$id)->first();
        if(!empty($refund))
        {
            throw new ApiException("定妆单[{$id}]已经退款,不允许重复退款", ERROR::ORDER_STATUS_WRONG);
        }
        
        $cash = BookingCash::where('booking_id',$id)->first();
        
        if(empty($cash))
        {
            throw new ApiException("定妆单[{$id}]状态不正确,找不到收银信息,不允许退款", ERROR::ORDER_STATUS_WRONG);
        }
        
        $max_return  = bcadd($cash->other_money, $cash->cash_money,2);
        if($params['money'] > $max_return)
        {
            throw new ApiException("定妆单[{$id}] 最大允许退款 {$max_return}. 退款金额错误，请查询", ERROR::PARAMETER_ERROR);
        }        
        
        $time = time();
        $datetime = date("Y-m-d H:i:s",$time);
        $attr = [
            'booking_id'=>$id,
            'uid'=>$this->user->id,
            'booking_sn'=>$base->BOOKING_SN,
            'order_sn'=>$base->ORDER_SN,
            'back_to'=>$params['back_to'],
            'money'=>$params['money'],
            'remark'=>$params['remark'],
            'created_at'=>$datetime,
        ];
        BookingSalonRefund::create($attr);
        $res = null;
        if($base->MANAGER_UID == 0)
        {
            $res = BeautyRefundApi::accpetByBookingSn([$base->BOOKING_SN], $this->user->id);
        }
        
        BookingOrder::where('ID',$id)->update(['STATUS'=>'RFD-OFL','UPDATE_TIME'=>$datetime]);
        Order::where('ordersn',$base->BOOKING_SN)->update(['status'=>4,'use_time'=>$time]); 
         
        Event::fire('booking.refund',"预约号".$base->BOOKING_SN." "."订单号".$base->ORDER_SN);
        
        return $this->success($res);
    }
    
    
    public static function make_recommend($uid,$recommend_code)
    {
        if(empty($recommend_code))
        {
            return;
        }
        $recommend = RecommendCodeUser::where('user_id',$uid)->whereIn("type",[2,3,4])->first();
        if(!empty($recommend))
        {
            return;
        }
        $attr = [
            'user_id'=>$uid,
            'recommend_code'=>$recommend_code,
            'add_time'=>time(),
        ];
        if(strlen($recommend_code) >= 11)
        {
            $attr['type'] = '3';
        }
        else
        {
            $type = '2';
            $dividend = Dividend::where('recommend_code',$recommend_code)->first();
            if(!empty($dividend))
            {
                if($dividend->activity == 1)
                {
                    $type = '4';
                }
            }
            $attr['type'] = $type;
        }
        RecommendCodeUser::create($attr);
    }
    
    public static function givePresent($customer_uid,$ordersn,$is_first = false,$first_consume_time = 0)
    {
        $recommends = RecommendCodeUser::where('user_id',$customer_uid)->whereIn("type",[2,3])->get(['recommend_code','type','add_time'])->toArray(); 
        
        if(count($recommends)!=1)
        {
            return ;
        }
        
        $recommend = $recommends[0];
        
        $send_uid = null;
        
        if($recommend['type'] == 2)//店铺推荐 需求修改 不送
        {
            
//             if($recommend['add_time'] <= strtotime($first_consume_time))
//             {
//                 $send_uid = $customer_uid;
//             }
        }
        else 
        {
            if($is_first)
            {
                 $recommend_users = User::where('mobilephone',$recommend['recommend_code'])->get(['user_id'])->toArray();
                 if(count($recommend_users)>0)
                 {
                     $send_uid = $recommend_users[0]['user_id'];
                 }
            }
        }
        
        if(!empty($send_uid))
        {
            Utils::log("present",date("Y-m-d H:i:s")." ordersn:{$ordersn} uid:{$send_uid} \n","send_present");
            PowderArticlesController::addReservateSnAfterConsume($ordersn,$send_uid);
        }
    }
}
