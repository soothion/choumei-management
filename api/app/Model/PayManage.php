<?php
/**
 * 财务 付款管理相关
 * @author zhunian
 */
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class PayManage extends Model
{
    /**
     * 状态 待提交
     * @var unknown
     */
    const STATE_OF_TO_SUBMIT = 1;
    
    
    /**
     * 状态 待审批
     * @var unknown
     */
    const STATE_OF_TO_CHECK = 2;
    
    /**
     * 状态 待付款
     * @var unknown
     */
    const STATE_OF_TO_PAY = 3;
        
    /**
     * 状态 已付款
     * @var unknown
     */
    const STATE_OF_PAIED = 4;
    
    /**
     * 付交易代收款
     * @var unknown
     */
    const TYPE_OF_FJY = 1;
    
    
    /**
     * 付业务投资款
     * @var unknown
     */
    const TYPE_OF_FTZ = 2;

    
    protected $table = 'pay_manage';
    
//     public function merchant(){
//         return $this->belongsTo(Merchant::class);
//     }
    
    public function make_user()
    {
        return $this->belongsTo(Manager::class,'make_uid');
    }
    
    public function confirm_user()
    {
        return $this->belongsTo(Manager::class,'confirm_uid');
    }
    
    public function cash_user()
    {
        return $this->belongsTo(Manager::class,'cash_uid');
    }
    
    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }
    
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
    

    /**
     * 生成新单 (从收款单生成)
     * @param array $attr
     * @return boolean|new id
     */
    public static function makeFromReceive($attr)
    {       
        return 1;
    }
    
    /**
     * 生成新单
     * @param array $attr
     * @return boolean|new id
     */
    public static function make($attr)
    {
        if( !isset($attr['type']) ||
            !isset($attr['salon_id']) ||
            !isset($attr['merchant_id']) ||
            !isset($attr['pay_type']) ||
            !isset($attr['require_day']) ||
            !isset($attr['cycle']) ||
            !isset($attr['cycle_day']) ||
            !isset($attr['cycle_money']) ||
            !isset($attr['make_uid'])
         )
        {
            return false;
        }
        $record = [
            'type'=>intval($attr['type']),
            'salon_id'=>intval($attr['salon_id']),
            'merchant_id'=>intval($attr['merchant_id']),
            'pay_type'=>intval($attr['pay_type']),
            'require_day'=>$attr['require_day'],
            'cycle'=>intval($attr['cycle']),
            'cycle_day'=>intval($attr['pay_type']),
            'cycle_money'=>floatval($attr['cycle_money']),
            'make_uid'=>intval($attr['make_uid']),
        ];
        $record['code'] = self::makeNewCode($record['type']);     
        $record['state'] = self::STATE_OF_TO_CHECK;
        $record['created_at'] = $record['updated_at'] =date("Y-m-d H:i:s");
        $id = self::insertGetId($record);
        return $id;
    }
    
    /**
     * 修改
     * @param unknown $id
     * @param unknown $attr
     */
    public static function change($id,$attr)
    {
        if(!isset($attr['make_uid']))
        {
            return false;
        }        
        $record = [
            'make_uid'=>intval($attr['make_uid']),
        ];
        if(isset($attr['salon_id']))
        {
            $record['salon_id'] =intval($attr['salon_id']);
        }
        if(isset($attr['merchant_id']))
        {
            $record['merchant_id'] =intval($attr['merchant_id']);
        }
        if(isset($attr['pay_type']))
        {
            $record['pay_type'] =intval($attr['pay_type']);
        }
        if(isset($attr['require_day']))
        {
            $record['require_day'] =intval($attr['require_day']);
        }
        if(isset($attr['cycle']))
        {
            $record['cycle'] =intval($attr['cycle']);
        }
        if(isset($attr['cycle_day']))
        {
            $record['cycle_day'] =intval($attr['cycle_day']);
        }
        if(isset($attr['cycle_money']))
        {
            $record['cycle_money'] =intval($attr['cycle_money']);
        }
        $record['updated_at'] =date("Y-m-d H:i:s");
        $item = self::where('id',$id)->first(['state']);
        if($item->state  != self::STATE_OF_TO_SUBMIT || $item->state  != self::STATE_OF_TO_CHECK )
        {
            return false;
        }        
        $record['state'] = PayManage::STATE_OF_TO_CHECK;
        self::where('id',$id)->update($record);
        return $id;
    }
    
    /**
     * 删除
     * @param unknown $id
     */
    public static function destory($id)
    {
        $item = self::where('id',$id)->first(['state']);
        if($item->state  != PayManage::STATE_OF_TO_SUBMIT || $item->state  != PayManage::STATE_OF_TO_CHECK )
        {
            return false;
        }
        self::where('id',$id)->delete();
        return true;
    }
    
    /**
     * 审核
     * @param unknown $id
     * @param unknown $opera
     * @param unknown $uid
     */
    public static function check($ids,$opera,$uid)
    {
        if(is_numeric($ids))
        {
            $ids = [$ids];
        }
        if(!is_array($ids))
        {
            return false;
        }
        
        $item_nums = self::whereIn('id',$ids)->where('state',self::STATE_OF_TO_CHECK)->count();
        if(count($ids) !== $item_nums)
        {
            return false;
        }
        $state = self::STATE_OF_TO_SUBMIT;
        if($opera)
        {
            $state = self::STATE_OF_TO_PAY;
        }
        self::whereIn('id',$ids)->where('state',self::STATE_OF_TO_CHECK)->update(
            [
                'state'=>$state,
                'confirm_uid' => intval($uid),  
                'confirm_at' => date("Y-m-d"),
            ]
         );
        return true;
    }
    
    /**
     * 确认
     * @param unknown $id
     * @param unknown $opera
     * @param unknown $uid
     */
    public static function confirm($ids,$opera,$uid)
    {
        if(is_numeric($ids))
        {
            $ids = [$ids];
        }
        if(!is_array($ids))
        {
            return false;
        }
        
        $item_nums = self::whereIn('id',$ids)->where('state',self::STATE_OF_TO_PAY)->count();
        if(count($ids) !== $item_nums)
        {
            return false;
        }
        $state = self::STATE_OF_TO_CHECK;
        if($opera)
        {
            $state = self::STATE_OF_PAIED;
        }
        self::whereIn('id',$ids)->where('state',self::STATE_OF_TO_PAY)->update(
            [
                'state'=>$state,
                'cash_uid' => intval($uid),
                'pay_day' => date("Y-m-d"),
            ]
        );
        //#@todo 确认后的其他操作
        return true;
    }

    public static function makeNewCode($type)
    {
        $prefix = "";
        if ($type == self::TYPE_OF_FTZ)
        {
            $prefix = "FTZ-";
        }
        else
        {
            $prefix = "FJY-";//默认付交易
        }
        
        $prefix .=date("ymdHis");
        
        $class = __CLASS__;
        $obj = new $class;
         
        $count = $obj->where("code","like",$prefix."%")->count();
        $now_num = intval($count) + 1;
        return $prefix.str_pad(strval($now_num), 3,"0",STR_PAD_LEFT);
    }
    
    public function isFillable($key)
    {
        return true;
    }
    
    
    public static function search($options)
    {
        $salon_fields = [
            'salonid',
            'salonname',
            'sn',
        ];
        $base_fields = [
            'id',
            'code',
            'type',
            'salon_id',
            'merchant_id',
            'money',
            'pay_type',
            'require_day',
            'pay_day',
            'created_at',
            'state',
        ];
        $order_by_fields = [
            'id',
            'code',
            'type',
            'money',
            'pay_type',
            'require_day',
            'pay_day',
            'created_at',
            'state',
        ];
    
        $base = self::select($base_fields);
    
        // 关键字搜索
        if (isset($options['key']) && ! empty($options['key']) && isset($options['keyword']) && ! empty($options['keyword'])) {
            $key = intval($options['key']);
            $keyword = "%" . str_replace([
                "%",
                "_"
            ], [
                "\\%",
                "\\_"
            ], $options['keyword']) . "%";
            if ($key == 1) {
                $base->whereRaw("salon_id in (SELECT `salonid` FROM `cm_salon` WHERE `salonname` LIKE '{$keyword}')");
            } elseif ($key == 2) {
                $base->whereRaw("merchant_id in (SELECT `id` FROM `cm_merchant` WHERE `name` LIKE '{$keyword}')");
            } elseif ($key == 3) {
                $base->whereRaw("salon_id in (SELECT `salonid` FROM `cm_salon` WHERE `sn` LIKE '{$keyword}')");
            }
        }
        
        // 付款单类型
        if (isset($options['type']) && !empty($options['type'])) {
            $base->where('type', intval($options['type']) );
        }
        
        // 付款方式类型
        if (isset($options['pay_type']) && !empty($options['pay_type'])) {
            $base->where('pay_type', intval($options['pay_type']) );
        }
        
        // 状态
        if (isset($options['state']) && !empty($options['state'])) {
            $base->where('state', intval($options['state']) );
        }
        
        $base->with([
            'salon' => function ($q) use($salon_fields)
            {
                $q->get($salon_fields);
            }
        ]);
        
        $base->with([
            'make_user' => function ($q)
            {
                $q->lists('id','name');
            }
        ])->with([
            'confirm_user' => function ($q)
            {
                $q->lists('id','name');
            }
        ]);
    
        // 按时间搜索
        if (isset($options['pay_time_min']) && preg_match("/^\d{4}\-\d{2}\-\d{2}$/", trim($options['pay_time_min']))) {
            $base->where('pay_day', ">=", trim($options['pay_time_min']));
        }
        if (isset($options['pay_time_max']) && preg_match("/^\d{4}\-\d{2}\-\d{2}$/", trim($options['pay_time_max']))) {
            $base->where('pay_day', "<=", trim($options['pay_time_max']));
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
        return $base->orderBy($order, $order_by);
    }

}
