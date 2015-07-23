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
    
    CONST TOKEN_KEY = "CHOUmei";
    
    /////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////    业务逻辑相关         /////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////
    /**
     * 统计已消费的信息
     */
    public static function countAlreadyCost()
    {
        
    }
    
    /**
     * 已消费的订单结算
     * @param array $options 订单号
     */
    public static function countOrder($options)
    {
        $order_sns = $options;
        $input_count = count($order_sns);
        $base_order_infos = Order::whereIn("ordersn",$order_sns)->where('status',4)->select('ordersn','salonid','priceall','use_time')->get()->toArray();
        
        $select_count = count($base_order_infos);
        //状态检查
        if($input_count != $select_count)
        {
            throw new \Exception("some ordersn in [".implode(",", $order_sns)."] is wrong status");
        }
        
        $salon_ids = array_column($base_order_infos, "salonid");
        
        $other_info = self::getSalonMerchantBaseInfo($salon_ids);
       
        $res = ['success'=>[],'type'=>1,'already'=>[]];
        foreach($base_order_infos as $order)
        {
            $ordersn = $order['ordersn'];
            $salon_id = $order['salonid'];
            $money = floatval($order['priceall']);
            $time = $order['use_time'];
            $salon_info = [];
            $merchant_info = [];
            $merchant_id = 0;
            if(isset($other_info['salon'][$salon_id]))
            {
                $salon_info = $other_info['salon'][$salon_id];
                $merchant_id = $other_info['salon'][$salon_id]['merchant_id'];
            }
            if(isset($other_info['merchant'][$merchant_id]))
            {
                $merchant_info = $other_info['merchant'][$merchant_id];
            }
            $ret = ShopCount::ShopCountOrder($ordersn,$salon_id,$money,$time,1,$salon_info,$merchant_info);   
            if($ret == 1)
            {
                $res['success'][] = $ordersn;
            }
            else if($ret == 2)
            {
               $res['already'][] = $ordersn;;
            }
        }
        return $res;
    }

    /**
     * 结算赏金单
     * @param array $options
     */
    public static function countBounty($options)
    {
        $order_sns = $options;
        $input_count = count($order_sns);
        $base_order_infos = BountyTask::whereIn("btSn",$order_sns)->where('btStatus',4)->select('btSn','salonId','money','endTime')->get()->toArray();
        
        $select_count = count($base_order_infos);
        //状态检查
        if($input_count != $select_count)
        {
            throw new \Exception("some ordersn in [".implode(",", $order_sns)."] is wrong status");
        }
        
        $salon_ids = array_column($base_order_infos, "salonId");
        
        $other_info = self::getSalonMerchantBaseInfo($salon_ids);
         
        $res = ['success'=>[],'type'=>2,'already'=>[]];
        foreach($base_order_infos as $order)
        {
            $ordersn = $order['btSn'];
            $salon_id = $order['salonId'];
            $money = floatval($order['money']);
            $time = $order['endTime'];
            $type = 2;
            $salon_info = [];
            $merchant_info = [];
            $merchant_id = 0;
            if(isset($other_info['salon'][$salon_id]))
            {
                $salon_info = $other_info['salon'][$salon_id];
                $merchant_id = $other_info['salon'][$salon_id]['merchant_id'];
            }
            if(isset($other_info['merchant'][$merchant_id]))
            {
                $merchant_info = $other_info['merchant'][$merchant_id];
            }
            $ret = ShopCount::ShopCountOrder($ordersn,$salon_id,$money,$time,$type,$salon_info,$merchant_info);
            if($ret == 1)
            {
                $res['success'][] = $ordersn;
            }
            else if($ret == 2)
            {
                $res['already'][] = $ordersn;;
            }
        }
        return $res;
    }
    
    /**
     * 收到用户的钱
     * @param array $options
     */
    public static function receiveMoney($options)
    {
        
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
            && isset($options['type'])
            && isset($options['salon_id'])
            && isset($options['uid'])
            && isset($options['pay_money'])
            && isset($options['cost_money'])
            && isset($options['day']))
        {
            $code = PrepayBill::getNewCode($options['type']);
            $options['code'] = $code;
            $options['state'] = PrepayBill::STATE_OF_COMPLETED;
            $options['created_at'] = $options['updated_at'] = date("Y-m-d H:i:s");
            $id = PrepayBill::insertGetId($options);
            $params = [
                'merchant_id'=>$options['merchant_id'],
                'salon_id'=>$options['salon_id'],
                'pay_money'=>$options['pay_money'],
                'cost_money'=>$options['cost_money'],
            ];
            ShopCount::payMoney($params);
            return $id;
        }
        return false;
    }
    
    /**
     * 更新一个预付单
     */
    public static function updatePrepay($id,$options)
    {
        $ret = true;
        $prepay = PrepayBill::where('id',$id)->first();
        if(empty($prepay))
        {
            $ret = false;
            return $ret;
        }
        if($prepay->state == 0)
        {
            $options['state'] = PrepayBill::STATE_OF_COMPLETED;
            $ret = PrepayBill::where('id',$id)->update($options);
        }
        else if($prepay->state == 1)
        {
            $options['updated_at'] = date("Y-m-d H:i:s");
            $ret =  PrepayBill::where('id',$id)->update($options);
            if(isset($options['pay_money']) && isset($options['cost_money']))
            {
                $options['pay_money'] = floatval($options['pay_money']) - floatval($prepay->pay_money);
                $options['cost_money'] = floatval($options['cost_money']) - floatval($prepay->cost_money);
                if (isset($options['merchant_id']))
                {
                    $options['merchant_id'] = intval($options['merchant_id']);
                }
                else
                {
                    $options['merchant_id'] = $prepay->merchant_id;
                }
                if (isset($options['salon_id']))
                {
                    $options['salon_id'] = intval($options['salon_id']);
                }
                else
                {
                    $options['salon_id'] = $prepay->salon_id;
                }
                if( $options['pay_money'] != 0 &&   $options['cost_money'] != 0)
                {
                    $params = [
                        'merchant_id'=>$options['merchant_id'],
                        'salon_id'=>$options['salon_id'],
                        'pay_money'=>$options['pay_money'],
                        'cost_money'=>$options['cost_money'],
                    ];
                    $ret =ShopCount::payMoney($params);
                }
            }
        }
        return $ret;
    }
    
    /**
     * 删除预付单
     * @param int $id
     * @return boolean|NULL
     */
    public static function deletePrepay($id)
    {
        $prepay = PrepayBill::where('id',$id)->first();
        if(empty($prepay))
        {
            return true;
        }
    
        if($prepay->state == 0)
        {
            PrepayBill::delete($id);
        }
        else if($prepay->state == 1)
        {
            PrepayBill::delete($id);
            $options = [];
            $options['salon_id'] = intval($prepay->salon_id);
            $options['pay_money'] = floatval($prepay->pay_money) * -1;
            $options['cost_money'] = floatval($prepay->cost_money) * -1;
            ShopCount::payMoney($options);
    
        }
        return null;
    }
    
    public static function getSalonMerchantBaseInfo($salon_ids)
    {
        $salon_infos = Salon::whereIn('salonid',$salon_ids)->get(['salonid','salonname','shopType','merchantId'])->toArray();
        $merchant_ids = array_column($salon_infos, "merchantId");
        $merchant_infos = Merchant::whereIn('id',$merchant_ids)->get(['id','name'])->toArray();
        $res = ['salon'=>[],'merchant'=>[]];
        foreach ($salon_infos as $salon)
        {
            $id= $salon['salonid'];
            $res['salon'][$id] = ['id'=>$id,'salon_name'=>$salon['salonname'],'shop_type'=>$salon['shopType'],'merchant_id'=>$salon['merchantId']];
        }
        foreach ($merchant_infos as $merchant)
        {
            $id = $merchant['id'];
            $res['merchant'][$id] = ['id'=>$id,'name'=>$merchant['name']];
        }        
        return $res;
    }
    
    
    public static function makeToken(&$params)
    {
        asort($params);
        $url = http_build_query($params);
        $params['token'] =  md5(md5($url).self::TOKEN_KEY);
    }
    
    public static function checkToken($params)
    {
        if(isset($params['token']))
        {
            $token = $params['token'];
            unset($params['token']);
            self::makeToken($params);
            return $params['token'] === $token;
        }
        return false;
    }    
    
    /////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////    数据查询相关         /////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////
    
    /**
     * 搜索预付款信息
     * @param array $options
     */
    public static function searchPrepay($options)
    {
        $salon_fields = ['salonid','salonname'];
        $merchant_fields = ['id','name'];
        $user_fields = ['id','name'];
        $prepay_fields = ['id','created_at','merchant_id','salon_id','code','type','uid','pay_money','cost_money','day','state'];
        $order_by_fields = ['id','created_at','code','type','pay_money','cost_money','day'];

        $prepay = PrepayBill::where('state','<>',PrepayBill::STATE_OF_PREVIEW)->select($prepay_fields);
        
        //关键字搜索
        if(isset($options['key']) && !empty($options['key']) && isset($options['keyword']) && !empty($options['keyword']))
        {
            $key = intval($options['key']);
            $keyword = '%'.str_replace(["%","_"], ["\\%","\\_"], $options['keyword'])."%";
            if ($key == 1)
            {
                $salon_ids=Salon::where('salonname','like',$keyword)->lists('salonid');
                $prepay->whereIn('salon_id',$salon_ids);
            }
            else 
            {
                $merchant_ids=Merchant::where('name','like',$keyword)->lists('id');
                $prepay->whereIn('merchant_id',$merchant_ids);
            }
//             if($key == 1)
//             { 
//                 $prepay->getQuery()->wheres[] = [
//                     'type'=>'In',
//                     'column'=>'salon_id',
//                     'operator'=>'IN',
//                     'value' => function()
//                     {
//                       return "SELECT `salonid` FROM `cm_order` where salonname like '{$keyword}'";
//                     },
//                   'boolean'=>'and'
//                 ];                
//             }
//             elseif ($key == 2)
//             {
//                 $prepay->getQuery()->wheres[] = [
//                     'type'=>'In',
//                     'column'=>'merchant_id',
//                     'operator'=>'IN',
//                     'value' => function()
//                     {
//                         return "SELECT `id` FROM `cm_merchant` where `name` like '{$keyword}'";
//                     },
//                     'boolean'=>'and'
//                   ];
//             }
        }
        
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
        $size = isset($options['page_size'])?max(intval($options['page_size']),1):20;
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
        
        if(isset($options['sort_type']) && strtoupper($options['sort_type']) == "ASC")
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
        if(isset($options['key']) && !empty($options['key']) && isset($options['keyword']) && !empty($options['keyword']))
        {
            $key = intval($options['key']);
            $keyword = "%".str_replace(["%","_"], ["\\%","\\_"], $options['keyword'])."%";
            
            if ($key == 1)
            {
                $salon_ids=Salon::where('salonname','like',$keyword)->lists('salonid');
                $instead_receive->whereIn('salon_id',$salon_ids);
            }
            else
            {
                $merchant_ids=Merchant::where('name','like',$keyword)->lists('id');
                $instead_receive->whereIn('merchant_id',$merchant_ids);
            }
            
//             if($key == 1)
//             {
                   
//                 $instead_receive->getQuery()->wheres[] = [
//                     'type'=>'In',
//                     'column'=>'salon_id',
//                     'operator'=>'IN',
//                     'value' => function()
//                     {
//                       return "SELECT `salonid` FROM `cm_order` where salonname like '{$keyword}'";
//                     },
//                   'boolean'=>'and'
//                 ];                
//             }
//             elseif ($key == 2)
//             {
//                 $instead_receive->getQuery()->wheres[] = [
//                     'type'=>'In',
//                     'column'=>'merchant_id',
//                     'operator'=>'IN',
//                     'value' => function()
//                     {
//                         return "SELECT `id` FROM `cm_merchant` where `name` like '{$keyword}'";
//                     },
//                     'boolean'=>'and'
//                   ];
//             }


        }
        
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
        $size = isset($options['page_size'])?max(intval($options['page_size']),1):20;
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
        
            if(isset($options['sort_type']) && strtoupper($options['sort_type']) == "ASC")
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
        $salon_fields = ['salonid','salonname','shopType'];
        $merchant_fields = ['id','name'];
        $shop_count_fields = ['id','created_at','merchant_id','merchant_name','salon_id','salon_name','salon_type','pay_money','cost_money','spend_money','balance_money','invest_money','invest_return_money','invest_balance_money','borrow_money','borrow_return_money','borrow_balance_money'];
        $order_by_fields = ['id','created_at','salon_name','salon_type','pay_money','cost_money','spend_money','balance_money','invest_money','invest_return_money','invest_balance_money','borrow_money','borrow_return_money','borrow_balance_money'];
        
        $salon_infos = null;
        $merchant_infos =null;
        
        $shop_count = ShopCount::select($shop_count_fields);
        
        //关键字搜索
        if(isset($options['key']) && !empty($options['key']) && isset($options['keyword']) && !empty($options['keyword']))
        {
            $key = intval($options['key']);
            $keyword = "%".str_replace(["%","_"], ["\\%","\\_"], $options['keyword'])."%";
            if ($key == 1)
            {
                $salon_infos=Salon::where('salonname','like',$keyword)->get($salon_fields)->toArray();
                $salon_ids = array_column($salon_infos, "salonid");
                $shop_count->whereIn('salon_id',$salon_ids);
            }
            else
            {
                $merchant_infos=Merchant::where('name','like',$keyword)->get($merchant_fields)->toArray();
                $merchant_ids = array_column($merchant_infos, "id");
                $shop_count->whereIn('merchant_id',$merchant_ids);
            }
            
//             if ($key == 1)
//             {
//                 $shop_count->where('salon_name','like',$keyword);
//             }
//             else
//             {
//                 $shop_count->where('merchant_name','like',$keyword);
//             }
            
//             if($key == 1)
//             {
//                 $shop_count->where('salonname','like',"%{$keyword}%");
//             }
//             elseif ($key == 2)
//             {
//                 $shop_count->getQuery()->wheres[] = [
//                     'type'=>'In',
//                     'column'=>'merchant_id',
//                     'operator'=>'IN',
//                     'value' => function()
//                     {
//                         return "SELECT `id` FROM `cm_merchant` where `name` like '{$keyword}'";
//                     },
//                     'boolean'=>'and'
//                   ];
//             }
        }
        
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
        $size = isset($options['page_size'])?max(intval($options['page_size']),1):20;
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
        
            if(isset($options['sort_type']) && strtoupper($options['sort_type']) == "ASC")
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
            $res = self::formatShopCountOut($res,$salon_infos,$merchant_infos);
            return $res;
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
        $prepay_fields = ['id','created_at','merchant_id','salon_id','code','type','uid','pay_money','cost_money','day','state'];
        
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
    
    protected static function formatShopCountOut($bases,$salon_infos,$merchant_infos)
    {
        $datas = (isset($bases['data'])&&count($bases['data']>0))?$bases['data']:null;
        if (empty($datas))
        {
            return $bases;
        }
        
        if(empty($salon_infos))
        {
            $salon_ids = array_column($datas, "salon_id");
            $salon_infos = Salon::whereIn("salonid",$salon_ids)->get(['salonid','salonname','shopType'])->toArray();
        }
        if (empty($merchant_infos))
        {
            $merchant_ids = array_column($datas, "merchant_id");
            $merchant_infos = Merchant::whereIn("id",$merchant_ids)->get(['id','name'])->toArray();
        }
        $salon_info_indexs =[];
        $merchant_info_indexs = [];
        foreach ($salon_infos as $info)
        {
            $key = $info['salonid'];
            $salon_info_indexs[$key] = $info;
        }
        
        foreach ($merchant_infos as $info)
        {
            $key = $info['id'];
            $merchant_info_indexs[$key] = $info;
        }
        
        foreach ($datas as &$data)
        {
            $salon_id = $data['salon_id'];
            $merchant_id = $data['merchant_id'];
            if(isset($salon_info_indexs[$salon_id]))
            {
                $data['salon_name'] = $salon_info_indexs[$salon_id]['salonname'];
                $data['salon_type'] = $salon_info_indexs[$salon_id]['shopType'];
            }
            if(isset($merchant_info_indexs[$merchant_id]))
            {
                $data['merchant_name'] = $merchant_info_indexs[$merchant_id]['name'];
            }
        }
        $bases['data'] = $datas;
        return $bases;
    }
}
