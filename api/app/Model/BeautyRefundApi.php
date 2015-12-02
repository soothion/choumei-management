<?php

namespace App;

use App\Exceptions\ApiException;
use App\Exceptions\ERROR;

class BeautyRefundApi {

    /**
     * 退款单状态  正常
     * @var unknown
     */
    CONST REFUND_STATUS_OF_NORMAL = 1;

    /**
     * 退款单状态  取消
     * @var unknown
     */
    CONST REFUND_STATUS_OF_CANCLE = 2;

    /**
     * 退款失败
     * @var unknown
     */
    CONST ORDER_STATUS_REFUND_FAILED = 9;

    /**
     * 订单状态 退款中
     * @var unknown
     */
    CONST ORDER_STATUS_OF_IN_REFUND = 10;

    /**
     * 退回银联
     * @var unknown
     */
    CONST REFUND_TO_UNION = 1;

    /**
     * 退回支付宝
     * @var unknown
     */
    CONST REFUND_TO_ALIPAY = 2;

    /**
     * 退回微信
     * @var unknown
     */
    CONST REFUND_TO_WX = 3;

    /**
     * 退回余额
     * @var unknown
     */
    CONST REFUND_TO_BALANCE = 4;

    /**
     * 退回红包
     * @var unknown
     */
    CONST REFUND_TO_HONGBAO = 5;

    /**
     * 退回优惠券
     * @var unknown
     */
    CONST REFUND_TO_YOUHUI = 6;

    /**
     * 退回易联
     * @var unknown
     */
    CONST REFUND_TO_YILIAN = 10;

    /**
     * 流水类型  消费
     * @var unknown
     */
    CONST REFUND_CODE_TYPE_OF_CUSTOM = 2;

    /**
     * 流水中的退款状态 未退款
     */
    CONST FUNDFLOW_REFUND_STATE_OF_UNCOMPLETED = 1;

    /**
     * 流水中的退款状态 已退款
     */
    CONST FUNDFLOW_REFUND_STATE_OF_COMPLETED = 2;

    /*
     * 拒绝退款
     */

    public static function reject($ids) {
        $refundsArr = OrderRefund::whereIn('order_refund_id', $ids)->where('status', self::REFUND_STATUS_OF_NORMAL)->where('item_type', '!=', 'MF')->get(['ordersn', 'booking_sn'])->toArray();
        if (empty($refundsArr)) {
            throw new ApiException("未找到退款单信息", ERROR::REFUND_FLOW_LOST);
        }
        $bookOrdersns = array_column($refundsArr, "booking_sn");
        $ordersns = array_column($refundsArr, "ordersn");
        $bookOrders = BookingOrder::whereIn('ORDER_SN', $ordersns)->where('STATUS', 'RFN')->count();
        if ($bookOrders != count($bookOrdersns)) {
            throw new ApiException("部分退款的预约单状态不正确", ERROR::REFUND_STATE_WRONG);
        }
        $bookingUp = BookingOrder::whereIn('ORDER_SN', $ordersns)->where('STATUS', 'RFN')->update(['STATUS' => 'PYD']);
        $refundUp = OrderRefund::whereIn('booking_sn', $bookOrdersns)->where('status', self::REFUND_STATUS_OF_NORMAL)->where('item_type', '!=', 'MF')->update(['status' => self::REFUND_STATUS_OF_CANCLE]);
        if ($bookingUp !== false && $refundUp !== false) {
            return [];
        } else {
            throw new ApiException("操作失败",ERROR::REFUND_FLOW_LOST);
        }
    }
    
    
    /*
     * 确认退款
     */
    
    public static function accpet($ids){
        return true;
    }

}
