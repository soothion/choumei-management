<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\OrderRefund;
use Illuminate\Pagination\AbstractPaginator;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;
use App\BeautyRefundApi;

class BeautyRefundController extends Controller {

    /**
     * @api {get} /beautyrefund/index 1.定妆单退款列表
     * @apiName index
     * @apiGroup beautyrefund
     *
     * @apiParam {Number} key  1手机号 2预约号 3推荐码
     * @apiParam {String} keyword  根据key来的关键字
     * @apiParam {String} start_time 开始时间 YYYY-MM-DD
     * @apiParam {String} end_time 结束时间 YYYY-MM-DD
     * @apiParam {String} pay_type 0 全部  2 支付宝 3 微信 7 积分 10 易联支付
     * @apiParam {String} state 0 全部  7已退款 10退款中  9退款失败 (多个用','隔开)
     * @apiParam {Number} page 可选,页数. (从1开始)
     * @apiParam {Number} page_size 可选,分页大小.(最小1 最大500,默认20)
     *
     * @apiSuccess {Number} total 总数据量.
     * @apiSuccess {Number} per_page 分页大小.
     * @apiSuccess {Number} current_page 当前页面.
     * @apiSuccess {Number} last_page 当前页面.
     * @apiSuccess {Number} from 起始数据.
     * @apiSuccess {Number} to 结束数据.
     * @apiSuccess {String} order_refund_id 退款单id
     * @apiSuccess {String} add_time 申请退款时间
     * @apiSuccess {String} ordersn 订单编号
     * @apiSuccess {String} booking_sn 预约号
     * @apiSuccess {String} money 退款金额
     * @apiSuccess {String} retype  退款方式 1原路返还 2退回余额
     * @apiSuccess {String} status 退款状态  6待审核,7退款完成,10退款中
     * @apiSuccess {String} user_id 付款人id
     * @apiSuccess {String} username 用户姓名
     * @apiSuccess {String} mobilephone 用户手机号
     * @apiSuccess {String} item_name 预约项目名称
     * @apiSuccess {String} pay_type 支付方式  1 网银 2 支付宝 3 微信 4 余额  7 积分  10易联
     *
     * @apiSuccessExample Success-Response:
     *  {    
     *   "result": 1,
     *   "token": "",
     *   "data": {
     *      "total": 1,
     *      "per_page": 10,
     *      "current_page": 1,
     *      "last_page": 1,
     *      "from": 1,
     *      "to": 1,
     *      "data": [
     *          {
     *              "order_refund_id": 19,
     *              "ordersn": "3891556931672",
     *              "user_id": 306818,
     *              "add_time": "2015-01-02 16:11:47",
     *              "money": "290.00",
     *              "booking_sn": "",
     *              "status": "PYD",
     *              "item_name": "测试时",
     *              "pay_type": 2,
     *              "username": "10306724",
     *              "mobilephone": "18681442252"
     *          }
     *      ]
     *  }
     *  } 
     *        
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "未授权访问"
     * 		}
     */
    public function index() {
        $param = $this->parameters([
            'key' => self::T_INT, // 1手机号 2预约号  3推荐码
            'keyword' => self::T_STRING,
            'start_time' => self::T_STRING,
            'end_time' => self::T_STRING,
            'pay_type' => self::T_INT,
            'state' => self::T_STRING,
            'page' => self::T_INT, //state 0 全部  6待审核 7已退款 10退款中  9拒绝退款 (多个用','隔开)
            'page_size' => self::T_INT,
        ]);
        $param['startTime'] = isset($param['start_time']) ? $param['start_time'] : '';
        $param['end_time'] = isset($param['end_time']) ? $param['end_time'] : '';
        $page = isset($param['page']) ? max(intval($param['page']), 1) : 1;
        $size = isset($param['page_size']) ? max(intval($param['page_size']), 1) : 20;
//        print_r($param);exit;
        $refundList = OrderRefund::serachMakeupRefundList($param, $page, $size);
        $data = [];
        foreach ($refundList['data'] as &$value) {
            $value['add_time'] = date("Y-m-d H:i:s", $value['add_time']);
        }
        return $this->success($refundList);
    }

    /**
     * @api {get} /beautyrefund/show/:id 2.定妆单退款列表
     * @apiName show
     * @apiGroup beautyrefund
     *
     * @apiSuccess {String} ordersn 订单号
     * @apiSuccess {String} booking_sn 预约号
     * @apiSuccess {String} booker_phone  手机号
     * @apiSuccess {String} booker_name 姓名
     * @apiSuccess {String} item_name 预约项目
     * @apiSuccess {String} price  项目价格
     * @apiSuccess {String} booking_date 预约日期
     * @apiSuccess {String} recommend_code 推荐码 TODO
     * @apiSuccess {String} status 订单状态  NEW - 未支付,PYD - 已支付,CSD - 已消费,RFN - 申请退款,RFD - 已退款' TODO
     * @apiSuccess {String} pay_type 支付方式
     * @apiSuccess {String} payment_sn   流水号
     * @apiSuccess {String} payable 支付金额
     * @apiSuccess {String} paied_time 支付时间
     * @apiSuccess {String} rereason 退款原因  
     * @apiSuccess {String} rereason 退款说明
     * @apiSuccess {Number} money   退款金额
     * @apiSuccess {String} add_time 发起退款时间
     * @apiSuccess {String} add_time 退款时间
     * @apiSuccess {String} opt_time 审批时间
     * @apiSuccess {String} opt_user  审批人
     *
     * @apiSuccessExample Success-Response:
     * {
     *  "result": 1,
     *   "token": "",
     * "data": {
     *     "ordersn": "3891556931672",
     *      "booking_sn": "",
     *      "booker_phone": null,
     *      "booker_name": "",
     *      "item_name": "测试时",
     *      "price": "100.00",
     *      "booking_date": null,
     *      "status": "RFN",
     *       "pay_type": 2,
     *       "payment_sn": "3891556931672Z1438915583",
     *       "payable": "0.00",
     *       "paied_time": null,
     *       "rereason": "2",
     *       "money": "290.00",
     *       "add_time": "2015-01-02 16:11:47",
     *       "opt_time": 0,
     *       "opt_user": ""
     *   }
     * }
     * 
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "未授权访问"
     * 		}
     */
    public function show($id) {
        $detail = OrderRefund::makeupRefundDetail($id);
        return $this->success($detail);
    }

    /**
     * @api {post} /beautyrefund/reject 3.退款拒绝
     * @apiName reject
     * @apiGroup beautyrefund
     *
     * @apiSuccess {Number} ids id(多个用','隔开).
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "未授权访问"
     * 		}
     */
    public function reject() {
        $param = $this->parameters([
            'ids' => self::T_STRING,
                ], true);
        $ids = explode(",", $param['ids']);
        $ids = array_map("intval", $ids);
        if (count($ids) < 1) {
            throw new ApiException("ids参数不能为空", ERROR::PARAMS_LOST);
        }
        $res = BeautyRefundApi::reject($ids);
        return $this->success($res);
    }

    /**
     * @api {post} /beautyrefund/accept 4.退款通过
     * @apiName accept
     * @apiGroup beautyrefund
     *
     * @apiParam {Number} ids id(多个用','隔开).
     * 
     * @apiSuccess {String} alipay 支付宝
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
     * 		{
     * 		    "result": 0,
     * 		    "msg": "未授权访问"
     * 		}
     */
    public function accept() {
        $params = $this->parameters(['ids' => self::T_STRING], true);
        $ids = explode(",", $params['ids']);
        $ids = array_map("intval", $ids);
        if (count($ids) < 1) {
            throw new ApiException("ids 参数不能为空", ERROR::PARAMS_LOST);
        }
        $info = BeautyRefundApi::accpet($ids);
        return $this->success();
    }

}