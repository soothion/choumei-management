<?php
/**
 * 交易后台相关的接口(业务逻辑相关部分)
 *
 */
namespace App;

use App\Exceptions\ApiException;
use App\Exceptions\ERROR;
class TransactionWriteApi
{
    /**
     * 退款单状态  正常
     * @var unknown
     */
    CONST REFUND_STATUS_OF_NORMAL = 1;
    
    /**
     * 退回的路径 退回余额
     * @var unknown
     */
    CONST REFUND_RETYPE_REFUND_TO_BALANCE = 2;
    
    /**
     * 订单状态 未使用
     * @var unknown
     */
    CONST ORDER_STATUS_OF_UNUSED = 2;
    
    /**
     * 订单状态 使用部分
     * @var unknown
     */
    CONST ORDER_STATUS_OF_USED_PART = 3;
    
    /**
     * 订单状态 已使用
     * @var unknown
     */
    CONST ORDER_STATUS_OF_USED = 4;
    
    /**
     * 订单状态 申请退款
     * @var unknown
     */
    CONST ORDER_STATUS_OF_APPLIED = 6;
    
    /**
     * 订单状态 退款完成
     * @var unknown
     */
    CONST ORDER_STATUS_OF_REFUND_COMPLETED = 7;
    
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
     * 臭美券 状态  未使用
     * @var unknown
     */
    CONST TICKET_STATUS_OF_UNUSED = 2;
    
    /**
     * 臭美券 状态  已使用
     * @var unknown
     */
    CONST TICKET_STATUS_OF_USED = 4;
    
    /**
     * 臭美券 状态   申请退款
     * @var unknown
     */
    CONST TICKET_STATUS_OF_APPLIED = 6;
    
    /**
     * 臭美券 状态   退款完成
     * @var unknown
     */
    CONST TICKET_STATUS_OF_REFUND_COMPLETED = 7;
    
    /**
     * 臭美券 状态   退款中
     * @var unknown
     */
    CONST TICKET_STATUS_OF_IN_REFUND = 8;
    
    /**
     * 退回银联
     * @var unknown
     */
    CONST REFUND_TO_UNION  = 1;
    
    /**
     * 退回支付宝
     * @var unknown
     */
    CONST REFUND_TO_ALIPAY  = 2;
    
    /**
     * 退回支付宝
     * @var unknown
     */
    CONST REFUND_TO_WX  = 3;
    
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
     * 退款通过
     * 
     * @param array $ids            
     */
    public static function accpet($ids)
    {   
        $refunds = self::checkRefundStatus($ids);
        $ordersns = array_column($refunds, "ordersn");
        $refund_indexes = Utils::column_to_key("ordersn",$refunds);
        unset($refunds);
        
        $fundflow = Fundflow::where('code_type',self::REFUND_CODE_TYPE_OF_CUSTOM)->whereIn('record_no',$ordersns)->select(['ticket_no','record_no'])->get();
        if(empty($fundflow))
        {
            throw new ApiException("找不到退款的支付流水信息",ERROR::REFUND_FLOW_LOST);
        }        
        $fundflowArr = $fundflow->toArray();        
        unset($fundflow);
        
        $ticket_nos = array_column($fundflowArr, "ticket_no");
        $ticket_nos = array_filter($ticket_nos);       
        if (count($ticket_nos) < 1) 
        {
            throw new ApiException("找不到退款的流水中的臭美券信息",ERROR::REFUND_FLOW_LOST);
        }                
        $tickets = OrderTicket::whereIn('ticketno',$ticket_nos)->get();        
        if(empty($tickets))
        {
            throw new ApiException("找不到退款单对应的臭美券",ERROR::REFUND_TICKET_LOST);
        }        
        $ticketArr = $tickets->toArray();
        unset($tickets);
        if(!self::checkArrayColumns($ticketArr, [self::TICKET_STATUS_OF_UNUSED,self::TICKET_STATUS_OF_APPLIED,self::TICKET_STATUS_OF_IN_REFUND], "status"))
        {
            throw new ApiException("退款单对应的臭美券状态不正确",ERROR::REFUND_TICKET_STATE_WRONG);
        }
        
        $payments = PaymentLog::whereIn('ordersn',$ordersns)->select(['ordersn','tn','device','batch_no','alipay_updated'])->get();
        
        if(empty($payments))
        {
            throw new ApiException("退款单找不到对应的payment log 信息",ERROR::REFUND_PAYMENT_LOG_LOST);
        }
        
        $paymentArr = $payments->toArray();
                
        $payment_indexes = Utils::column_to_key("ordersn", $paymentArr);
        
        $refund_items = self::getRefundItems($fundflowArr,$payment_indexes,$refund_indexes);
        
        // 状态修改为退款中
        self::modifOrderStatusInRefund($ordersns);
        
        foreach($refund_items as $type => $items)
        {
            if(count($items)<1)
            {
                continue;
            }            
            $call_name = "refundOf".ucfirst($type);
            if(!method_exists(self, $call_name))
            {
                throw new ApiException("unknown refund type of {$type}",ERROR::UNKNOWN_ERROR);
            }
            $res = call_user_func_array(["self",$call_name], [$items]);
        }
        

        
        // 网银退款
        if (count($union_items) > 0) {
            $url = C('UNIONPAY_REFUND_URL');
            foreach ($union_items as $union_item) {
                $user_id = $union_item['user_id'];
                $money = $union_item['money'];
                $ordersn = $union_item['ordersn'];
                $tn = $union_item['tn'];
                $output['info'] .= "订单号：{$ordersn}, 退款：{$money}, 退款方式：银联\n";
                $bank_result = $this->curlRefund($ordersn, $user_id, $money, $tn, $url);
                simple_log(date("Y-m-d H:i:s") . $bank_result . "\n", "UNIONPAY_order_refund");
                $output['info'] .= $bank_result . "\n";
            }
        }
        // 微信退款
        if (count($wechat_items) > 0) {
            foreach ($wechat_items as $wechat_item) {
                $user_id = $wechat_item['user_id'];
                $money = $wechat_item['money'];
                $ordersn = $wechat_item['ordersn'];
                $tn = $wechat_item['tn'];
                $url = $wechat_item['url'];
                $wechat_result = $this->curlRefund($ordersn, $user_id, $money, $tn, $url);
                simple_log(date("Y-m-d H:i:s") . $wechat_result . "\n", "WECHAT_order_refund");
                $output['info'] .= $wechat_result . "\n";
            }
        }
        // 支付宝的退款处理
        if (count($alipay_items) > 0) {
            $notify_url = C("ALIPAY_REFUND_NOTIFY_URL");
            $batch_no = AlipaySimple::getRandomBatchNo();
            
            // 写入退款批次号
            self::UpdateAlipayBatchNo($batch_no, $alipay_items);
            // 支付宝的表单提交
            $output['alipay_form'] = AlipaySimple::refund([
                'notify_url' => $notify_url,
                'batch_no' => $batch_no,
                'detail_data' => $alipay_items
            ]);
        }
        
        // 余额
        if (count($yue_items) > 0) {
            foreach ($yue_items as $item) {
                $ordersn = $item['record_no'];
                M("user")->where("user_id='{$item["user_id"]}'")->setInc("money", $item["money"]);
                // 写入流水
                $this->addFundflow($item);
                $output['info'] .= "订单号：{$ordersn}, 退款：{$item["money"]}, 退款方式：余额\n";
            }
            // 改为已完成状态
            $yue_ordersn_list = Utils::get_column_array("record_no", $yue_items);
            self::modifOrderStatusRefundCompleted($yue_ordersn_list, "余额退款完成");
            // 销量减一
            self::modiSoldNumMinus($yue_ordersn_list);
        }
        // 红包
        if (count($hongbao_items) > 0) {
            foreach ($hongbao_items as $item) {
                $ordersn = $item['record_no'];
                M("packet_count")->where("user_id='{$item["user_id"]}'")->setInc("packetmoney", $item["money"]);
                // 写入流水
                $this->addFundflow($item);
                $output['info'] .= "订单号：{$ordersn}, 退款：{$item["money"]}, 退款方式：红包\n";
            }
            // 改为已完成状态
            $hongbao_ordersn_list = Utils::get_column_array("record_no", $hongbao_items);
            self::modifOrderStatusRefundCompleted($hongbao_ordersn_list, "红包退款完成");
            // 销量减一
            self::modiSoldNumMinus($hongbao_ordersn_list);
        }
        // 优惠券
        if (count($youhuicode_items) > 0) {
            foreach ($youhuicode_items as $item) {
                $ordersn = $item['record_no'];
                M("user")->where("user_id='{$item["user_id"]}'")->setInc("couponmoney", $item["money"]);
                // 写入流水
                $this->addFundflow($item);
                $output['info'] .= "订单号：{$ordersn}, 退款：{$item["money"]}, 退款方式：优惠码\n";
            }
            // 改为已完成状态
            $youhuicode_ordersn_list = Utils::get_column_array("record_no", $youhuicode_items);
            self::modifOrderStatusRefundCompleted($youhuicode_ordersn_list, "优惠券退款完成");
            
            // 销量减一
            self::modiSoldNumMinus($youhuicode_ordersn_list);
        }
        // 易联
        if (count($yilian_items) > 0) {
            foreach ($yilian_items as $yilian_item) {
                $data['user_id'] = $yilian_item['user_id'];
                $data['amount'] = $yilian_item['money'];
                $data['ordersn'] = $yilian_item['ordersn'];
                $data['tn'] = $yilian_item['tn'];
                $notify_url = C("YILIAN_REFUND_NOTIFY_URL");
                
                $argc = array();
                $argc['body'] = $data;
                $argc['to'] = 'refund';
                $argc['type'] = 'Payeco';
                
                $argStr = json_encode($argc);
                $param['code'] = $argStr;
                
                $yilian_result = $this->curlPostRefund($param, $notify_url);
                simple_log(date("Y-m-d H:i:s") . $yilian_result . "\n", "YILIAN_order_refund");
                // $output['info'] .= $yilian_result."\n";
                $resDecode = json_decode($yilian_result, true);
                if ($resDecode['result'] == 1) {
                    $output['info'] .= $yilian_item['ordersn'] . " 退款成功\n";
                } else {
                    $output['info'] .= $yilian_item['ordersn'] . " 退款失败\n";
                }
            }
        }
        return true;
    }

    /**
     * 退款拒绝
     * 
     * @param array $ids            
     */
    public static function reject($ids, $remark)
    {
        
    }

    /**
     * 退款的回调
     */
    public static function callBackOfAlipay()
    {
        
    }
    
    /**
     * 将订单标记为退款中状态
     * @param array $ordersns
     * @return boolean
     */
    public static function modifOrderStatusInRefund($ordersns)
    {
        return true;
    }
    
    /**
     * 将订单改为已退款状态
     * @param array $ordersns
     */
    public static function modifOrderStatusRefundCompleted($ordersns)
    {
        
    }
    
    /**
     * 减少销量
     * @param array $ordersns
     */
    public static function modiSoldNumMinus($ordersns)
    {
        
    }
    
    /**
     * 添加退款流水
     * @param unknown $ordersns
     */
    public static function addRefundFundflow($ordersns)
    {
        
    }

    /**
     * 检查允许的值
     * @param array $items
     * @param array $allowValues
     * @param string|int $column_name
     * @return bool
     */
    public static function checkArrayColumns($items,$allowValues,$column_name)
    {
        $items = array_column($items, $column_name);
        foreach ($items as $item)
        {
            if(!in_array($item,$allowValues))
            {
                return false;
            }
        }
        return true;
    }
    
    /**
     * @param array $ids
     * @throws ApiException
     * @return array
     */
    private static function checkRefundStatus($ids)
    {
        $count = count($ids);
        if ($count < 1) {
            throw new ApiException( "退款id不能为空", ERROR::PARAMS_LOST);
        }
        $refunds = OrderRefund::whereIn('order_refund_id',$ids)->where('status',self::REFUND_STATUS_OF_NORMAL)->select(['ordersn','retype'])->get();
        if(empty($refunds))
        {
            throw new ApiException( "找不到相关的退款单信息", ERROR::REFUND_STATE_WRONG);
        }
        $refundArr =  $refunds->toArray();
        unset($refunds);
        if(count($refundArr) !== $count)
        {
            throw new ApiException( "您选的部分订单不存在退款信息", ERROR::REFUND_STATE_WRONG);
        }
        $ordersns = array_column($refundArr, "ordersn");
        
        $order = Order::whereIn("ordersn",$ordersns)->whereIn("status",[self::ORDER_STATUS_OF_UNUSED,self::ORDER_STATUS_OF_APPLIED,8,self::ORDER_STATUS_REFUND_FAILED,self::ORDER_STATUS_OF_IN_REFUND])->selectRaw("count(*) as num")->first();
        if(empty($order))
        {
            throw new ApiException( "找不到相关的订单信息", ERROR::REFUND_STATE_WRONG);
        }
        if(intval($order->num) !== $count)
        {
            throw new ApiException( "关联的订单状态不正确", ERROR::REFUND_STATE_WRONG);
        }
        unset($order);
        return $refundArr;
    }
    
    /**
     * 
     * @param array $fundflows
     * @param array $paymentlogs
     * @param array $refunds
     * @return array 
     */
    private static function getRefundItems($fundflows,$paymentlogs,$refunds)
    {
        $wx_refund_url_of_web = env("WX_REFUND_URL_OF＿WEB",null);
        $wx_refund_url_of_sdk = env("WX_REFUND_URL_OF＿SDK",null);
        if(empty($wx_refund_url_of_web) || empty($wx_refund_url_of_sdk))
        {
            throw new ApiException("获取配置信息 [WX_REFUND_URL_OF＿WEB] 或者 [WX_REFUND_URL_OF＿SDK] 出错",ERROR::CONFIG_LOST);
        }
        $res = [
            'union'=>[],
            'alipay'=>[],
            'wx'=>[],
            'balance'=>[],
            'hongbao'=>[],
            'youhui'=>[],
            'yilian'=>[],
        ];
        foreach ($fundflows as $flow) {
            $ordersn = $flow['record_no'];
            if(!isset($refunds[$ordersn]) || !isset($paymentlogs[$ordersn]))
            {
                throw new ApiException("退款的关键信息不全",ERROR::REFUND_LOST_PRIMARY_INFO);
            }
            $retype = intval($refunds[$ordersn]['retype']);
            
            if(self::REFUND_RETYPE_REFUND_TO_BALANCE === $retype)
            {
                $pay_type = self::REFUND_TO_BALANCE;
            }
            else
            {
                $pay_type = intval($flow['pay_type']);
            }
            
            $user_id = $flow['user_id'];
            $money = $flow['money'];
            $reason = Mapping::getfFundflowRereason($flow['rereason']);        
           
            $device = null;
            $tn = "";
            $batch_no = "";
            $alipay_updated = 0;
            
            if (isset($paymentlogs[$ordersn])) {
                $tn = $paymentlogs[$ordersn]['tn'];
                $device = $paymentlogs[$ordersn]['device'];
                $batch_no = $paymentlogs[$ordersn]['batch_no'];
                $alipay_updated = intval($paymentlogs[$ordersn]['alipay_updated']);
            }
        
            switch ($pay_type) {
                case self::REFUND_TO_UNION:
                    if (empty($tn)) 
                    {
                        throw  new ApiException("ordersn '{$ordersn}' can not find tn",ERROR::REFUND_CANT_FIND_TN);                   
                    }               
                    $res['union'][] = [
                        'ordersn' => $ordersn,
                        'user_id' => $user_id,
                        'money' => $money,
                        'tn' => $tn
                    ];
                    break;
                case self::REFUND_TO_ALIPAY:
                    if (empty($tn)) {
                        throw  new ApiException("ordersn '{$ordersn}' can not find tn",ERROR::REFUND_CANT_FIND_TN);
                    }
                    if (! empty($batch_no) && (time() - $alipay_updated) < 7200) {
                        throw  new ApiException("ordersn '{$ordersn}' 已于" . date("Y-m-d H:i:s", $alipay_updated) . "开始退款.请不要请求太频繁",ERROR::UNKNOWN_ERROR);
                    }                    
                    $res['alipay'][]= [
                        'tn' => $tn,
                        "money" => $money,
                        "reason" => $reason,
                        "ordersn" => $ordersn
                    ];
                    break;
                case self::REFUND_TO_WX: 
                    if (empty($tn)) {
                        throw  new ApiException("ordersn '{$ordersn}' can not find tn",ERROR::REFUND_CANT_FIND_TN);
                    }
                    $url = $device == 2?$wx_refund_url_of_web:$wx_refund_url_of_sdk;                    
                    $res['wx'][] = [
                        'tn' => $tn,
                        "money" => $money,
                        "ordersn" => $ordersn,
                        'user_id' => $user_id,
                        'url' => $url
                    ];
                    break;
                case self::REFUND_TO_BALANCE: 
                   $res['balance'][] = [
                        "ordersn" => $ordersn,
                    ];
                    break;
                case self::REFUND_TO_HONGBAO: 
                    $res['hongbao'][] = [
                        "ordersn" => $ordersn,
                    ];
                    break;
                case self::REFUND_TO_YOUHUI:
                    $res['youhui'][] = [
                        "ordersn" => $ordersn,
                    ];
                    break;
                case self::REFUND_TO_YILIAN:
                    if (empty($tn)) {
                        throw  new ApiException("ordersn '{$ordersn}' can not find tn",ERROR::REFUND_CANT_FIND_TN);
                    }
                    $fund_ordersn_list[] = $ordersn;
                    $res['yilian'][] = [
                        'tn' => $tn,
                        "money" => $money,
                        "ordersn" => $ordersn,
                        'user_id' => $user_id
                    ];
                    break;
            }
        }
        return $res;
    }
    
    /**
     * 网银退款
     */
    private static function refundOfUnion()
    {
        $url = env("UNIONPAY_REFUND_URL",null);
        if(empty($url))
        {
            throw new ApiException("获取配置信息 [UNIONPAY_REFUND_URL] 出错",ERROR::CONFIG_LOST);
        }
        $args = func_get_args();
        if(count($args)<1)
        {
            throw new ApiException("必要参数为空",ERROR::UNKNOWN_ERROR);
        }
        $items = $args[0];
        foreach ($items as $item) {
            $user_id = $item['user_id'];
            $money = $item['money'];
            $ordersn = $item['ordersn'];
            $tn = $item['tn'];
            $output['info'] .= "订单号：{$ordersn}, 退款：{$money}, 退款方式：银联\n";
            $bank_result = $this->curlRefund($ordersn, $user_id, $money, $tn, $url);
            simple_log(date("Y-m-d H:i:s") . $bank_result . "\n", "UNIONPAY_order_refund");
            $output['info'] .= $bank_result . "\n";
        }
    }
    
    /**
     * 支付宝退款
     */
    private static function refundOfAlipay()
    {
        
    }
    
    /**
     * 微信退款
     */
    private static function refundOfWx()
    {
        
    }
    
    /**
     * 余额退款
     */
    private static function refundOfBalance()
    {
        
    }
    
    /**
     * 红包退款
     */
    private static function refundOfHongbao()
    {
        
    }
    
    /**
     * 优惠码
     */
    private static function refundOfYouhui()
    {
        
    }
    
    /**
     * 
     */
    private static function refundOfYilian()
    {
        
    }    
    
    /**
     * 为远程调用数据生成token
     * @param array $data
     */
    private static function makeSignForPost(&$data)
    {
        $data['sign'] = self::encryptionSign($data['ordersn'], $data['user_id'], $data['money'], $data['tn']);
    }
   
    /**
     * 加密key
     * @param unknown $ordersn
     * @param unknown $user_id
     * @param unknown $money
     * @param unknown $tn
     * @return string
     */
    private static function encryptionSign($ordersn, $user_id, $money, $tn)
	{
		return md5(md5($ordersn.$user_id.$money).$tn.'choumei.cn');	
	}
}

?>