<?php
/**
 * 交易后台相关的接口(搜索部分)
 * 
 */
namespace App;

use Illuminate\Pagination\AbstractPaginator;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;
class TransactionSearchApi
{
    /**
     * 订单列表
     * @param array $params
     */
    public static function searchOfOrder($params)
    {
        $bases = self::getConditionOfOrder($params);
        // 页数
        $page = isset($params['page']) ? max(intval($params['page']), 1) : 1;
        $size = isset($params['page_size']) ? max(intval($params['page_size']), 1) : 20;
        AbstractPaginator::currentPageResolver(function () use($page)
        {
            return $page;
        });
        $total_money = self::countOfOrder($params);        
        $res = $bases->paginate($size)->toArray();
        $res['total_money'] = $total_money;
        unset($res['next_page_url']);
        unset($res['prev_page_url']);
        return $res;
    }
    
    /**
     * 臭美券列表
     * @param array $params
     */
    public static function searchOfTicket($params)
    {
        $bases = self::getConditionOfTicket($params);
        // 页数
        $page = isset($params['page']) ? max(intval($params['page']), 1) : 1;
        $size = isset($params['page_size']) ? max(intval($params['page_size']), 1) : 20;
        AbstractPaginator::currentPageResolver(function () use($page)
        {
            return $page;
        });
        $total_money = self::countOfOrder($params);
        $res = $bases->paginate($size)->toArray();
        $res['total_money'] = $total_money;
        unset($res['next_page_url']);
        unset($res['prev_page_url']);
        return $res;
    }
    
    /**
     * 退款单列表
     * @param array $params
     */
    public static function searchOfRefund($params)
    {
    
    }
    
    /**
     * 订单详情
     * @param int $id
     */
    public static function orderDetail($id)
    {
        $base = Order::where('orderid',$id)->select(['ordersn','orderid','priceall','salonid'])->first();
        if(empty($base))
        {
            throw new ApiException("订单 [{$id}] 不存在", ERROR::ORDER_NOT_EXIST);
        }
        $baseArr = $base->toArray();
        $orderItem = OrderItem::where("orderid",$id)->select(['order_item_id','itemname','ordersn'])->first();
        if(empty($orderItem)) //没订单项目
        {
            return ['order'=>$baseArr];
        }
        $orderItemArr = $orderItem->toArray();
        $ticket = OrderTicket::where("order_item_id",$orderItemArr['order_item_id'])->select(['order_ticket_id','ticketno','user_id'])->first();
        if(empty($ticket))//没有臭美券
        {
            return ['order'=>$baseArr];
        }
        $ticketArr = $ticket->toArray();
        $res = self::getDetailInfo($baseArr, $orderItemArr, $ticketArr);
        return $res;
    }
    
    /**
     * 臭美券详情
     * @param int $id
     */
    public static function ticketDetail($id)
    {
    
    }
    
    /**
     * 退款详情
     * @param int $id
     */    
    public static function refundDetail($id)
    {
    
    }
    
    public static function getDetailInfo($order,$orderItem,$ticket)
    {
        if(empty($order) || empty($orderItem) || empty($ticket))
        {
            return null;
        }
        $orderid = $order['orderid'];
        $ordersn = $order['ordersn'];
        $ticketno = $ticket['ticketno'];
        $ticket_id = $ticket['order_ticket_id'];
        $uid = $ticket['user_id'];
        $salon_id = $order['salonid'];
       
        //用户
        $user = User::where("user_id",$uid)->select(['username','mobilephone'])->first()->toArray();   
        //店铺    
        $salon = Salon::where('salonid',$salon_id)->select(['salonname'])->first()->toArray();
        //金额构成
        $fundflows = Fundflow::where("record_no",$ordersn)->where("ticket_no",$ticketno)->where("code_type",2)->get(['pay_type','money'])->toArray();
        //动态
        $trends = OrderTicketTrends::where("ordersn",$ordersn)->where("ticketno",$ticketno)->orderBy("add_time","ASC")->get(['add_time','status','remark'])->toArray();
        //代金券
        $vouchers = VoucherTrend::where("ordersn",$ordersn)->with(["voucher"=>function($q){
            $q->get(['vId','vUseMoney','vUseStart','vUseEnd']);
        }])->orderBy("vAddTime","ASC")->get()->toArray();
        
        //佣金
        $commission = CommissionLog::where('ordersn',$ordersn)->select(['ordersn','amount','rate','grade'])->first()->toArray();

        //用户邀请码
        $recommendCode = RecommendCodeUser::where('user_id',$uid)->select(['recommend_code'])->first()->toArray();
        
        $res = [
            'order'=>$order,
            'item'=>$orderItem,
            'ticket'=>$ticket,
            'user'=>$user,
            'salon'=>$salon,
            'fundflows'=>$fundflows,            
            'vouchers'=>$vouchers,
            'commission'=>$commission,
        ];
        return $res;
    }
    

    public static  function getConditionOfOrder($params)
    {
        $salon_fields = [
            'salonid',
            'salonname'
        ];
        $fundflow_fields = [
            'record_no',
            'pay_type',
        ];
        $user_fields = [
            'user_id',
            'username',
            'mobilephone',
        ];
        $base_fields = [
            'orderid',
            'ordersn',
            'priceall',
            'salonid',
            'add_time',
            'pay_time',
            'user_id',
            'ispay',
        ];
        
        $order_by_fields = [
            'orderid',
            'ordersn',
            'priceall',
            'add_time',
            'pay_time',
            'ispay',
        ];
        
        $orderBase = Order::select($base_fields);
        
        self::makeWhereOfOrder($orderBase, $params);
        
        $orderBase->with([
            'user' => function ($q) use($user_fields)
            {
                $q->get($user_fields);
            }
        ]);
        
        $orderBase->with([
            'salon' => function ($q) use($salon_fields)
            {
                $q->get($salon_fields);
            }
        ]);
        
        $orderBase->with([
            'fundflow' => function ($q) use($fundflow_fields)
            {
                $q->get($fundflow_fields);
            }
        ]);
        
        
        // 排序
        if (isset($params['sort_key']) && in_array($params['sort_key'], $order_by_fields)) {
            $order = $params['sort_key'];
        } else {
            $order = "orderid";
        }
        
        if (isset($params['sort_type']) && strtoupper($params['sort_type']) == "ASC") {
            $order_by = "ASC";
        } else {
            $order_by = "DESC";
        }
        
        return $orderBase->orderBy($order, $order_by);
    }
    
    public static  function getConditionOfTicket($params)
    {

        $salon_fields = [
            'salonid',
            'salonname'
        ];
        $fundflow_fields = [
            'record_no',
            'pay_type',
        ];
        $user_fields = [
            'user_id',
            'username',
            'mobilephone',
        ];
        $base_fields = [
            'order_ticket_id',
            'order_item_id',
            'ticketno',
            'salonid',
            'add_time',
            'pay_time',
            'user_id',
            'ispay',
        ];
        
        $order_by_fields = [
            'orderid',
            'ordersn',
            'priceall',
            'add_time',
            'pay_time',
            'ispay',
        ];
        
    }
    
    public static  function getConditionOfRefund($params)
    {
    
    }
    
    public static function countOfOrder($params)
    {
        $orderBase = Order::selectRaw("SUM(`priceall`) as `priceall`");
        self::makeWhereOfOrder($orderBase, $params);
        $order = $orderBase->first();
        return $order->priceall;
    }
    
    public static function makeWhereOfOrder(&$orderBase,$params)
    {
        // 按时间搜索
        if (isset($params['pay_time_min']) && !empty($params['pay_time_min']) && preg_match("/^\d{4}\-\d{2}\-\d{2}$/", trim($params['pay_time_min']))) {
            $orderBase->where('day', ">=", strtotime(trim($params['pay_time_min'])));
        }
        if (isset($params['pay_time_max']) && !empty($params['pay_time_min']) && preg_match("/^\d{4}\-\d{2}\-\d{2}$/", trim($params['pay_time_max']))) {
            $orderBase->where('day', "<=", strtotime(trim($params['pay_time_max'])) + 86399 );
        }
        
        //支付方式
        if(isset($params['pay_type']) && !empty($params['pay_type']))
        {
            $pay_types = explode(",", $params['pay_type']);
            $pay_types = array_map("intval", $pay_types);
            $pay_type_str = implode(",", $pay_types);
            if(!empty($pay_type_str))
            {
                $orderBase->whereRaw('`ordersn` IN (select `record_no` from cm_fundflow where `code_type` = 2 and `pay_type` IN ({$pay_type_str}) )');
            }
        }
        
        // 付款状态
        if(isset($params['pay_state']) && !empty($params['pay_state']))
        {
            $orderBase->where('ispay', $params['pay_state']);
        }
        
        // 关键字搜索
        if (isset($params['key']) && ! empty($params['key']) && isset($params['keyword']) && ! empty($params['keyword'])) {
            $key = intval($params['key']);
            $keyword = '%' . str_replace([
                "%",
                "_"
            ], [
                "\\%",
                "\\_"
            ], $params['keyword']) . "%";
            if ($key == 1) //订单号
            {
                $orderBase->where("ordersn",'like',$keyword);
            }
            elseif ($key == 2) //用户臭美号
            {
                $orderBase->whereRaw("`user_id` IN (SELECT `user_id` FROM `cm_user` WHERE `username` LIKE '{$keyword}')");
            }
            elseif ($key == 3) //用户手机号
            {
                $orderBase->whereRaw("user_id in (SELECT `user_id` FROM `cm_user` WHERE `mobilephone` LIKE '{$keyword}')");
            }
            elseif ($key == 4) //店铺名
            {
                $orderBase->whereRaw("`salonid` IN (SELECT `salonid` FROM `cm_salon` WHERE `salonname` LIKE '{$keyword}')");
            }
        }        
    }
}