<?php

namespace App\Http\Controllers\Trans;

use App\Http\Controllers\Controller;

class OrderRefundController extends Controller
{
    /**
     * @api {get} /refund/index 1.退款列表
     * @apiName index
     * @apiGroup refund
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
    public function index()
    {
         
    }
    
    /**
     * @api {get} /refund/show/{id} 2.退款详情
     * @apiName show
     * @apiGroup refund
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
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function show($id)
    {
    
    }
    
    /**
     * @api {get} /refund/export 3.退款导出
     * @apiName export
     * @apiGroup refund
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
     */
    public function export()
    {
        
    }
    
    /**
     * @api {post} /refund/accept 4.退款通过
     * @apiName accept
     * @apiGroup refund
     *
     * @apiSuccess {Number} ids id(多个用','隔开).
     *
     * @apiSuccessExample Success-Response: 
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function accept()
    {
        
    }
    
    /**
     * @api {post} /refund/reject 4.退款拒绝
     * @apiName reject
     * @apiGroup refund
     *
     * @apiSuccess {Number} ids id(多个用','隔开).
     *
     * @apiSuccessExample Success-Response:
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function reject()
    {
        
    }
    
    /**
     * 支付宝的回调
     */
    public function call_back_of_alipay()
    {
        
    }
}
