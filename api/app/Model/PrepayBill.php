<?php
/**
 * 转付单相关
 * @author zhunian
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class PrepayBill extends Model
{
    /**
     * 预付交易款类型
     * @var int
     */
    CONST TYPE_OF_PREPAY = 1;
    
    /**
     * 付代收交易款
     * @var int
     */
    CONST TYPE_OF_ALREADYPAY = 2;
    
    /**
     * 交易款返还
     * @var int
     */
    CONST TYPE_OF_RETURN = 3;
    
    /**
     * 状态  预览状态  已经不用   2015-08-24
     * @var int
     */
    CONST STATE_OF_PREVIEW = 0;
    
    /**
     * 状态  已付款状态
     * @var int
     */
    CONST STATE_OF_COMPLETED = 1;
    
    /**
     * 状态  待提交
     * @var int
     */
    CONST STATE_OF_TO_SUBMIT = 2;
    
    /**
     * 状态 待审批
     * @var int
     */
    CONST STATE_OF_TO_CHECK = 3;
    
    /**
     * 状态  带付款
     * @var int
     */
    CONST STATE_OF_TO_PAY = 4;
    
    protected $table = 'prepay_bill';
    
    public function merchant(){           
        return $this->belongsTo(Merchant::class);
    }
    
    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }
    
    public function user()
    {
        return $this->belongsTo(Manager::class,'uid');
    }
    
    /**
     * 生成新的一条单号
     */
    public static function getNewCode($type)
    {
        $type = intval($type);
        $prefix = "YF";
        switch ($type) {
            case self::TYPE_OF_ALREADYPAY:
                $prefix = "FDS";
                break;
            case self::TYPE_OF_RETURN:
                $prefix = "YFFH";
                break;
            default:
                $prefix = "YF";
        }
        $prefix .=date("ymdHis");
        
        $class = __CLASS__;
        $obj = new $class;
       
        $count = $obj->where("code","like",$prefix."%")->count();
        $now_num = intval($count) + 1;
        return $prefix.str_pad(strval($now_num), 3,"0",STR_PAD_LEFT);
    }
    
}
