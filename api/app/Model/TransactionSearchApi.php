<?php
/**
 * 交易后台相关的接口(搜索部分)
 * 
 */
namespace App;

use Illuminate\Pagination\AbstractPaginator;
class TransactionSearchApi
{
    /**
     * 订单列表
     * @param array $params
     */
    public static function searchOfOrder($params)
    {
        $bases = self::getConditionOfOrder($params);
        //页数
        $page = isset($options['page'])?max(intval($params['page']),1):1;
        $size = isset($options['page_size'])?max(intval($params['page_size']),1):20;
        AbstractPaginator::currentPageResolver(function() use ($page) {
            return $page;
        });
        
            $res =  $bases->paginate($size)->toArray();
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
    

    public static  function getConditionOfOrder($params)
    {
        $salon_fields = [
            'salonid',
            'salonname'
        ];
        $merchant_fields = [
            'id',
            'name'
        ];
        $user_fields = [
            'user_id',
            'username',
            'mobilephone',
        ];
        $prepay_fields = [
            'id',
            'created_at',
            'merchant_id',
            'salon_id',
            'code',
            'type',
            'uid',
            'pay_money',
            'pay_type',
            //'cost_money',
            'day',
            'pay_day',
            'state'
        ];
        $order_by_fields = [
            'id',
            'created_at',
            'code',
            'type',
            'pay_money',
            'pay_type',
            'day'
        ];
        
        $prepay = PrepayBill::where('state', '<>', PrepayBill::STATE_OF_PREVIEW)->select($prepay_fields);
        
        // 关键字搜索
        if (isset($options['key']) && ! empty($options['key']) && isset($options['keyword']) && ! empty($options['keyword'])) {
            $key = intval($options['key']);
            $keyword = '%' . str_replace([
                "%",
                "_"
            ], [
                "\\%",
                "\\_"
            ], $options['keyword']) . "%";
            if ($key == 1) {
                $prepay->whereRaw("salon_id in (SELECT `salonid` FROM `cm_salon` WHERE `salonname` LIKE '{$keyword}')");
            } elseif ($key == 2) {
                $prepay->whereRaw("merchant_id in (SELECT `id` FROM `cm_merchant` WHERE `name` LIKE '{$keyword}')");
            } elseif ($key == 3) {
                $prepay->whereRaw("salon_id in (SELECT `salonid` FROM `cm_salon` WHERE `sn` LIKE '{$keyword}')");
            }
        }
        
        $prepay->with([
            'user' => function ($q) use($user_fields)
            {
                $q->get($user_fields);
            }
        ]);
        
        $prepay->with([
            'salon' => function ($q) use($salon_fields)
            {
                $q->get($salon_fields);
            }
        ]);
        
        $prepay->with([
            'merchant' => function ($q) use($merchant_fields)
            {
                $q->get($merchant_fields);
            }
        ]);
        
        // 按时间搜索
        if (isset($options['pay_time_min']) && preg_match("/^\d{4}\-\d{2}\-\d{2}$/", trim($options['pay_time_min']))) {
            $prepay->where('day', ">=", trim($options['pay_time_min']));
        }
        if (isset($options['pay_time_max']) && preg_match("/^\d{4}\-\d{2}\-\d{2}$/", trim($options['pay_time_max']))) {
            $prepay->where('day', "<=", trim($options['pay_time_max']));
        }
        
        // 排序
        if (isset($options['sort_key']) && in_array($options['sort_key'], $order_by_fields)) {
            $order = $options['sort_key'];
        } else {
            $order = "created_at";
        }
        
        if (isset($options['sort_type']) && strtoupper($options['sort_type']) == "ASC") {
            $order_by = "ASC";
        } else {
            $order_by = "DESC";
        }
        
        return $prepay->orderBy($order, $order_by);
    }
    
    public static  function getConditionOfTicket($params)
    {
    
    }
    
    public static  function getConditionOfRefund($params)
    {
    
    }
}