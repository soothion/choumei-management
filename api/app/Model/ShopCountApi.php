<?php
/**
 * 商铺结算相关的功能
 */
namespace App;

use App\ShopCount;
use App\InsteadReceive;
use App\PrepayBill;
use Illuminate\Pagination\AbstractPaginator;

class ShopCountApi
{
    /**
     * 统计已消费的信息
     */
    public static function countAlreadyCost()
    {
        
    }
    
    /**
     * 已消费的订单结算
     * @param array $options
     */
    public static function countOrder($options)
    {
        
    }

    /**
     * 结算赏金单
     * @param array $options
     */
    public static function countBounty($options)
    {
        
    }
    
    /**
     * 收到用户的钱
     * @param array $options
     */
    public static function receiveMoney($options)
    {
        
    }
    
    /**
     * 付给商户钱
     * @param array $option
     */
    public static function payMoney($options)
    {
        if(isset($options['merchant_id'])
            && isset($options['salon_id'])
            && isset($options['type'])
            && isset($options['pay_money'])
            && isset($options['cost_money']))
        {
           $model = ShopCount::firstOrNew(['salon_id'=>$options['salon_id']]);
           return $ret;
        }
        return false;
    }
    
    /**
     * 搜索预付款信息
     * @param array $options
     */
    public static function searchPrepay($options)
    {
        $salon_fields = ['salonid','salonname'];
        $merchant_fields = ['id','name'];
        $user_fields = ['id','name'];
        $prepay_fields = ['id','created_at','merchant_id','salon_id','code','type','uid','pay_money','cost_money','day'];
        $order_by_fields = ['id','created_at','code','type','pay_money','cost_money','day'];

        $prepay = PrepayBill::where('state','<>',PrepayBill::STATE_OF_PREVIEW)->select($prepay_fields);
        
        //关键字搜索
        $salon_condition = null;
        $merchant_condition = null;
        if(isset($options['key']) && !empty($options['key']) && isset($options['keyword']) && !empty($options['keyword']))
        {
            $key = intval($options['key']);
            $keyword = "%".str_replace(["%","_"], ["\\%","\\_"], $options['keyword'])."%";
            if($key == 1)
            {
                $salon_condition = ['key'=>'salonname','opera'=>'like','value'=>$keyword];
            }
            elseif ($key == 2)
            {
                $merchant_condition = ['key'=>'name','opera'=>'like','value'=>$keyword];
            }
        }
        
        $prepay->with([
            'user' => function ($q) use($user_fields)
            {
                $q->lists($user_fields[0],$user_fields[1]);
            }
        ]);
        
        $prepay->with([
            'salon' => function ($q) use($salon_condition,$salon_fields)
            {
                if (empty($salon_condition)) {
                    $q->lists($salon_fields[0],$salon_fields[1]);
                } else {
                    $q->where($salon_condition['key'], $salon_condition['opera'], $salon_condition['value'])
                        ->lists($salon_fields[0],$salon_fields[1]);
                }
            }
        ]);
        
        $prepay->with([
            'merchant' => function ($q) use($merchant_condition,$merchant_fields)
            {
                if (empty($merchant_condition)) {
                    $q->lists($merchant_fields[0],$merchant_fields[1]);
                } else {
                    $q->where($merchant_condition['key'], $merchant_condition['opera'], $merchant_condition['value'])
                        ->lists($merchant_fields[0],$merchant_fields[1]);
                }
            }
        ]);
        
        //按时间搜索
        if(isset($options['pay_time_min']) && preg_match("/^\d{4}\-\d{2}\-\d{2}$/", trim($options['pay_time_min'])))
        {
            $prepay->where('day',">=",trim($options['pay_time_min']));
        }
        if(isset($options['pay_time_max']) && preg_match("/^\d{4}\-\d{2}\-\d{2}$/", trim($options['pay_time_max'])))
        {
            $prepay->where('day',"<=",trim($options['pay_time_max']));
        }
        
        //页数
        $page = isset($options['page'])?max(intval($options['page']),1):1;
        $size = isset($options['size'])?max(intval($options['size']),1):10;
        AbstractPaginator::currentPageResolver(function() use ($page) {
            return $page;
        });
        
        //排序
        if(isset($options['sort_key']) && in_array($options['sort_key'], $order_by_fields))
        {
            $order = $options['sort_key'];
        }
        else
        {
            $order = "created_at";
        }
        
        if(isset($options['sort_type']) && $options['sort_type'] == 1)
        {
            $order_by = "ASC";
        }
        else
        {
            $order_by = "DESC";
        }
        
        $res =  $prepay->orderBy($order,$order_by)->paginate($size)->toArray();
        unset($res['next_page_url']);
        unset($res['prev_page_url']);
        return $res;
    }
    
    /**
     * 搜索代收单信息
     * @param array $option
     */
    public static function searchInsteadReceive($options)
    {
        $salon_fields = ['salonid','salonname'];
        $merchant_fields = ['id','name'];
        $instead_receive_fields = ['id','created_at','merchant_id','salon_id','code','type','money','day'];
        $order_by_fields = ['id','created_at','code','type','money','day'];
        
        $instead_receive = InsteadReceive::select($instead_receive_fields);
        
        //关键字搜索
        $salon_condition = null;
        $merchant_condition = null;
        if(isset($options['key']) && !empty($options['key']) && isset($options['keyword']) && !empty($options['keyword']))
        {
            $key = intval($options['key']);
            $keyword = "%".str_replace(["%","_"], ["\\%","\\_"], $options['keyword'])."%";
            if($key == 1)
            {
                $salon_condition = ['key'=>'salonname','opera'=>'like','value'=>$keyword];
            }
            elseif ($key == 2)
            {
                $merchant_condition = ['key'=>'name','opera'=>'like','value'=>$keyword];
            }
        }
        
        $instead_receive->with([
            'salon' => function ($q) use($salon_condition,$salon_fields)
            {
                if (empty($salon_condition)) {
                    $q->lists($salon_fields[0],$salon_fields[1]);
                } else {
                    $q->where($salon_condition['key'], $salon_condition['opera'], $salon_condition['value'])
                    ->lists($salon_fields[0],$salon_fields[1]);
                }
            }
        ]);
        
        $instead_receive->with([
            'merchant' => function ($q) use($merchant_condition,$merchant_fields)
            {
                if (empty($merchant_condition)) {
                    $q->lists($merchant_fields[0],$merchant_fields[1]);
                } else {
                    $q->where($merchant_condition['key'], $merchant_condition['opera'], $merchant_condition['value'])
                    ->lists($merchant_fields[0],$merchant_fields[1]);
                }
            }
        ]);
        
        //按时间搜索
        if(isset($options['pay_time_min']) && preg_match("/^\d{4}\-\d{2}\-\d{2}$/", trim($options['pay_time_min'])))
        {
            $instead_receive->where('day',">=",trim($options['pay_time_min']));
        }
        if(isset($options['pay_time_max']) && preg_match("/^\d{4}\-\d{2}\-\d{2}$/", trim($options['pay_time_max'])))
        {
            $instead_receive->where('day',"<=",trim($options['pay_time_max']));
        }
        
        //页数
        $page = isset($options['page'])?max(intval($options['page']),1):1;
        $size = isset($options['size'])?max(intval($options['size']),1):10;
        AbstractPaginator::currentPageResolver(function() use ($page) {
            return $page;
        });
        
            //排序
            if(isset($options['sort_key']) && in_array($options['sort_key'], $order_by_fields))
            {
                $order = $options['sort_key'];
            }
            else
            {
                $order = "created_at";
            }
        
            if(isset($options['sort_type']) && $options['sort_type'] == 1)
            {
                $order_by = "ASC";
            }
            else
            {
                $order_by = "DESC";
            }
        
            $res =  $instead_receive->orderBy($order,$order_by)->paginate($size)->toArray();
            unset($res['next_page_url']);
            unset($res['prev_page_url']);
            return $res;
    }
    
    /**
     * 搜索商铺往来结算信息
     * @param array $option
     */
    public static function searchShopCount($options)
    {     
//        $salon_fields = ['salonid','salonname','shopType'];
        $merchant_fields = ['id','name'];
        $shop_count_fields = ['id','created_at','merchant_id','salon_id','salon_name','salon_type','pay_money','cost_money','spend_money','balance_money','invest_money','invest_return_money','invest_balance_money','borrow_money','borrow_return_money','borrow_balance_money'];
        $order_by_fields = ['id','created_at','salon_name','salon_type','pay_money','cost_money','spend_money','balance_money','invest_money','invest_return_money','invest_balance_money','borrow_money','borrow_return_money','borrow_balance_money'];
        
        $shop_count = ShopCount::select($shop_count_fields);
        
        //关键字搜索
        $salon_condition = null;
        $merchant_condition = null;
        if(isset($options['key']) && !empty($options['key']) && isset($options['keyword']) && !empty($options['keyword']))
        {
            $key = intval($options['key']);
            $keyword = "%".str_replace(["%","_"], ["\\%","\\_"], $options['keyword'])."%";
            if($key == 1)
            {
                $salon_condition = ['key'=>'salonname','opera'=>'like','value'=>$keyword];
            }
            elseif ($key == 2)
            {
                $merchant_condition = ['key'=>'name','opera'=>'like','value'=>$keyword];
            }
        }

// 获取统计时的店铺信息  不动态获取        
//         $shop_count->with([
//             'salon' => function ($q) use($salon_condition,$salon_fields)
//             {
//                 if (empty($salon_condition)) {
//                     $q->lists($salon_fields[0],$salon_fields[1]);
//                 } else {
//                     $q->where($salon_condition['key'], $salon_condition['opera'], $salon_condition['value'])
//                     ->lists($salon_fields[0],$salon_fields[1]);
//                 }
//             }
//         ]);
        
        $shop_count->with([
            'merchant' => function ($q) use($merchant_condition,$merchant_fields)
            {
                if (empty($merchant_condition)) {
                    $q->lists($merchant_fields[0],$merchant_fields[1]);
                } else {
                    $q->where($merchant_condition['key'], $merchant_condition['opera'], $merchant_condition['value'])
                    ->lists($merchant_fields[0],$merchant_fields[1]);
                }
            }
        ]);
        
        //按时间搜索
        if(isset($options['pay_time_min']) && preg_match("/^\d{4}\-\d{2}\-\d{2}$/", trim($options['pay_time_min'])))
        {
            $shop_count->where('day',">=",trim($options['pay_time_min']));
        }
        if(isset($options['pay_time_max']) && preg_match("/^\d{4}\-\d{2}\-\d{2}$/", trim($options['pay_time_max'])))
        {
            $shop_count->where('day',"<=",trim($options['pay_time_max']));
        }
        
        //页数
        $page = isset($options['page'])?max(intval($options['page']),1):1;
        $size = isset($options['size'])?max(intval($options['size']),1):10;
        AbstractPaginator::currentPageResolver(function() use ($page) {
            return $page;
        });
        
            //排序
            if(isset($options['sort_key']) && in_array($options['sort_key'], $order_by_fields))
            {
                $order = $options['sort_key'];
            }
            else
            {
                $order = "created_at";
            }
        
            if(isset($options['sort_type']) && $options['sort_type'] == 1)
            {
                $order_by = "ASC";
            }
            else
            {
                $order_by = "DESC";
            }
        
            $res =  $shop_count->orderBy($order,$order_by)->paginate($size)->toArray();
            unset($res['next_page_url']);
            unset($res['prev_page_url']);
            return $res;
    }

    
    /**
     * 生成一个预览状态的预付单
     */
    public static function makePreviewPrepay($options)
    {
        if(isset($options['merchant_id']) 
            && isset($options['salon_id'])
            && isset($options['type'])
            && isset($options['uid'])
            && isset($options['pay_money'])
            && isset($options['cost_money'])
            && isset($options['day']))
        {
            $code = PrepayBill::getNewCode($options['type']);
            $options['code'] = $code;
            $id = PrepayBill::insertGetId($options);
            return $id;
        }
        return false;
    }
    
    /**
     * 生成一个预付单
     */
    public static function makePrepay($options)
    {
        if(isset($options['merchant_id'])
            && isset($options['salon_id'])
            && isset($options['type'])
            && isset($options['uid'])
            && isset($options['pay_money'])
            && isset($options['cost_money'])
            && isset($options['day']))
        {
            $code = PrepayBill::getNewCode($options['type']);
            $options['code'] = $code;
            $options['state'] = PrepayBill::STATE_OF_COMPLETED;
            $id = PrepayBill::insertGetId($options);
            self::payMoney($options);
            return $id;
        }
        return false;
    }
    
    /**
     * 更新一个预付单
     */
    public static function updatePrepay($id,$options)
    {
        #@todo 
        return null;
    }
    
    /**
     * 预付款详情信息
     * @param int $id
     */
    public static function prepayDetail($id)
    {
        $salon_fields = ['salonid','salonname'];
        $merchant_fields = ['id','name'];
        $user_fields = ['id','name'];
        $prepay_fields = ['id','created_at','merchant_id','salon_id','code','type','uid','pay_money','cost_money','day'];
        
        $prepay = PrepayBill::where('id',$id);
        $prepay->with([
            'user' => function ($q) use($user_fields)
            {
                $q->lists($user_fields[0],$user_fields[1]);
            }
        ]);
        
        $prepay->with([
            'salon' => function ($q) use($salon_fields)
            {
              $q->lists($salon_fields[0],$salon_fields[1]);
            }
        ]);
        
        $prepay->with([
            'merchant' => function ($q) use($merchant_fields)
            {
              $q->lists($merchant_fields[0],$merchant_fields[1]);
            }
        ]);
        
        return $prepay->first($prepay_fields);
    }
    
    /**
     * 代收单详情信息
     * @param int $id
     */
    public static function insteadReceiveDetail($id)
    {
        $salon_fields = ['salonid','salonname'];
        $merchant_fields = ['id','name'];
        $instead_receive_fields = ['id','created_at','merchant_id','salon_id','code','type','money','day'];
        
        $instead_receive = InsteadReceive::where('id',$id);
        
        $instead_receive->with([
            'salon' => function ($q) use($salon_fields)
            {
                $q->lists($salon_fields[0],$salon_fields[1]);
            }
        ]);
        
        $instead_receive->with([
            'merchant' => function ($q) use($merchant_fields)
            {
                $q->lists($merchant_fields[0],$merchant_fields[1]);
            }
        ]);
        
        return $instead_receive->first($instead_receive_fields);
    }
}
