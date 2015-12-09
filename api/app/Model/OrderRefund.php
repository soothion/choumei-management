<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\ERROR;
use Illuminate\Pagination\AbstractPaginator;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;

class OrderRefund extends Model {

    protected $table = 'order_refund';
    protected $primaryKey = 'order_refund_id';
    public $timestamps = false;

    public function salon() {
        return $this->belongsTo(\App\Salon::class, 'salonid', 'salonid');
    }

    public function user() {
        return $this->belongsTo(\App\User::class);
    }

    public function fundflow() {
        return $this->hasMany(\App\Fundflow::class, 'record_no', 'ordersn');
    }

    public function voucher() {
        return $this->belongsTo(\App\Voucher::class, 'ordersn', 'vOrderSn');
    }

    public static function serachMakeupRefundList($param, $page, $size) {
        $fields = [
            'order_refund.order_refund_id',
            'order_refund.ordersn',
            'order_refund.user_id',
            'order_refund.add_time',
            'order_refund.money',
            'order_refund.booking_sn',
            'order_refund.status as refund_status',
            'booking_order.status',
            'booking_order.booker_name',
            'booking_order.booker_phone',
            'fundflow.pay_type',
        ];


        $query = self::select($fields);

        $query = self::getRefundView($query, $param);
        $query = self::whereConditionRefund($query, $param);
        AbstractPaginator::currentPageResolver(function () use($page) {
            return $page;
        });
        $refundList = $query->paginate($size)->toArray();
        unset($refundList['next_page_url']);
        unset($refundList['prev_page_url']);
        return $refundList;
    }

    private static function getRefundView($query, $param) {

        $query->leftJoin('booking_order', 'booking_order.ORDER_SN', '=', 'order_refund.ordersn')
                ->leftJoin('fundflow', 'record_no', '=', 'order_refund.ordersn');
        if (isset($param['key']) && isset($param['keyword']) && ($param['key'] == 3) && !empty($param['keyword'])) {
            $query->leftJoin('recommend_code_user', 'recommend_code_user.user_id', '=', 'order_refund.user_id');
        }

        $query->orderBy('order_refund_id', 'DESC');
        return $query;
    }

    private static function whereConditionRefund($query, $param) {
        //必要条件 退款状态 为可用
        $query->where('order_refund.item_type', '!=', "MF");

        // 按时间搜索
        if (!empty($param['start_time'])) {
            $query->where('order_refund.add_time', '>=', strtotime($param['start_time']."00:00:00"));
        }
        if (!empty($param['end_time'])) {
            $query->where('order_refund.add_time', '<=', strtotime($param['end_time']."23:59:59"));
        }
        //支付方式
        if (isset($param['pay_type']) && $param['pay_type']) {
            $query->where('fundflow.pay_type', $param['pay_type']);
        }
        //付款状态
        if (isset($param['state']) && $param['state']) {
            $state_str = explode(",", $param['state']);
            if (count($state_str) == 3) { //全选
                $query->whereIn('booking_order.STATUS', $state_str)->where("order_refund.status", '!=', 2);
            } elseif (count($state_str) == 1 && $state_str[0] == 'RFE') {  //仅选择了退款失败
                $query->where("order_refund.status", 3);
            } else {
                $query->whereIn('booking_order.STATUS', $state_str)->where("order_refund.status", 1);
            }
        }
        if (isset($param['key']) && isset($param['keyword']) && $param['key'] && !empty($param['keyword'])) {
            switch ($param['key']) {
                case 1:
                    $query->where('booking_order.booker_phone', $param['keyword']);
                    break;
                case 2:
                    // TODO  预约号
                    $query->where('order_refund.booking_sn', $param['keyword']);
                    break;
                case 3:
                    //TODO  推荐码
                    $query->where('recommend_code_user.recommend_code', $param['keyword']);
                    break;
                default :
                    break;
            }
        }
        return $query;
    }

    public static function makeupRefundDetail($order_refund_id) {
        if (!$order_refund_id) {
            throw new ApiException('找不到退款单信息', ERROR::REFUND_FLOW_LOST);
        }
        $fields = [
            //order_refund
            'order_refund.ordersn',
            'order_refund.user_id',
            'order_refund.money',
            'order_refund.opt_user_id',
            'order_refund.rereason',
            'order_refund.add_time',
            'order_refund.opt_time',
            'order_refund.status',
            'order_refund.booking_sn',
            'order_refund.item_type',
            'order_refund.rereason',
            'order_refund.other_rereason',
        ];
        //booking_order
        $booking_order_fields = [
            'booking_order.booking_date',
            'booking_order.status',
            'booking_order.paied_time',
            'booking_order.booker_name',
            'booking_order.booker_phone',
            'booking_order.booking_date',
            'booking_order.payable',
            'booking_order.paied_time',
            'booking_order.booker_sex',
        ];
        //booking_order_item
        $booking_order_item_fields = [
            'booking_order_item.item_name',
            'booking_order_item.price',
        ];
        // fundflow
        $fundflow_fields = [
            'fundflow.pay_type'
        ];
        // payment_log
        $payment_log_fields = [
            'payment_log.payment_sn',
        ];
        $refund = self::select($fields)->where('order_refund_id', $order_refund_id)->where('item_type', '!=', 'MF')->first();
        if (empty($refund)) {
            throw new ApiException("退款单 [{$order_refund_id}] 不存在", ERROR::REFUND_NOT_EXIST);
        }
        $bookingOrder = BookingOrder::select($booking_order_fields)->where('order_sn', $refund->ordersn)->first();
        $bookingOrderItem = BookingOrderItem::select($booking_order_item_fields)->where('order_sn', $refund->ordersn)->get()->toArray();
        $fundflow = Fundflow::select($fundflow_fields)->where('record_no', $refund->ordersn)->first();
        $paymentLog = PaymentLog::select($payment_log_fields)->where('ordersn', $refund->ordersn)->first();
        $optUser = !empty($refund->opt_user_id) ? Manager::find($refund->opt_user_id)->name : '';
        //推荐码
        $recommendInfo = RecommendCodeUser::where('user_id', $refund->user_id)->whereIn('type', ['2', '3'])->first();
        // 接待信息
        $bookingReceive = BookingReceive::where('booking_sn', $refund->booking_sn)->first();
        $receive = [
            'arrive_at' => '',
            'update_booking_date' => '',
            'remark' => '',
            'receiver' => '',
            'create_at' => ''
        ];
        if (!empty($bookingReceive)) {
            $receive['update_booking_date'] = $bookingReceive->update_booking_date;
            $receive['remark'] = $bookingReceive->remark;
            $receive['receiver'] = !empty($bookingReceive->uid) ? Manager::where('id', $bookingReceive->uid)->first()->name : ''; //TODO 查询具体的人姓名
            $receive['create_at'] = (string) $bookingReceive->created_at; //TODO 查询具体的人姓名
            $receive['arrive_at'] = $bookingReceive->arrive_at;
        }
        //如果在退款中   查询是否是退款失败
        if ($bookingOrder->status == 'RFN' && $refund->status == 3) {
            $bookingOrder->status = 'RFE';
        }
        $reason = implode(',', Mapping::getBeautyRefundRereasonNames(explode(',', $refund->rereason)));
        $reason = !empty($reason) ? $reason . "," . $refund->other_rereason : $refund->other_rereason;
        $res = [
            'ordersn' => $refund->ordersn,
            'booking_sn' => $refund->booking_sn,
            'booker_phone' => $bookingOrder->booker_phone,
            'booker_name' => $bookingOrder->booker_name,
            'booking_order_item' => $bookingOrderItem,
//            'item_name' => $bookingOrderItem->item_name,
//            'price' => $bookingOrderItem->price,
            'booking_date' => $bookingOrder->booking_date,
            'status' => $bookingOrder->status,
            'booker_sex' => $bookingOrder->booker_sex,
            // 预约金支付信息
            'pay_type' => $fundflow->pay_type,
            'payment_sn' => $paymentLog->payment_sn, //流水号
            'payable' => $bookingOrder->payable, //流水号
            'paied_time' => $bookingOrder->paied_time, //流水号
            'pay_status' => 1, //支付状态 
            // 退款信息
            'refund_from' => 1, //用户
            'rereason' => $reason,
            'refund_desc' => $reason,
            'refund_type' => $fundflow->pay_type,
            'money' => $refund->money,
            'add_time' => date("Y-m-d H:i:s", $refund->add_time),
            'opt_time' => !empty($refund->opt_time) ? date("Y-m-d H:i:s", $refund->opt_time) : '', //操作时间
            'complete_time' => !empty($refund->opt_time) ? date("Y-m-d H:i:s", $refund->opt_time) : '', //操作时间
            'opt_user' => $optUser, //审批人
            'recommend_code' => !empty($recommendInfo) ? $recommendInfo->recommend_code : '',
            'receive' => $receive
        ];
        return $res;
    }

}
