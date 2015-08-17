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

}
