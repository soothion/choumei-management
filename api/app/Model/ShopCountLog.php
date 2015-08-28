<?php
/**
 * 商铺往来结算明细
 * @author zhunian
 */
namespace App;

use Illuminate\Database\Eloquent\Model;
class ShopCountLog extends Model
{
    protected $table = 'shop_count_log';
    
    /**
     * 预付款
     * @var unknown
     */
    CONST TYPE_OF_PREPAY = 1;
    
    /**
     * 消费
     * @var unknown
     */
    CONST TYPE_OF_SPEND = 2;
        
    /**
     * 佣金
     * @var unknown
     */
    CONST TYPE_OF_COMMISSION = 3;
    
    /**
     * 佣金返还
     * @var unknown
     */
    CONST TYPE_OF_COMMISSION_RETURN = 4;
    
    /**
     * 添加记录
     * @param int $salon_id
     * @param int $type
     * @param float $money
     * @param datetime|timestamp $time
     * @param string $remark
     */
    public static function add_log($salon_id,$type,$money,$time,$remark="")
    {
        $money = floatval($money);
        $update_method = "increment";
       
        if( $type == self::TYPE_OF_SPEND || $type == self::TYPE_OF_COMMISSION_RETURN)
        {
            $update_method = "decrement";
        }
        
        if(is_numeric($time))
        {
            $time = date("Y-m-d H:i:s",intval($time));
        }  
        //之前的信息
        $model = self::where('salon_id',$salon_id)->where("count_at","<",$time)->select("balance_money")->first();
        $balance_money = $money;
        if(!empty($model))
        {
            if($update_method == "increment")
            {
                $balance_money = floatval($model->balance_money) + $money;
            }
            else 
            {
                $balance_money = floatval($model->balance_money) - $money;
            }
        }
        else 
        {
            if($update_method == "decrement")
            {
                $balance_money = $money * -1;
            }
        }
        //更新本条记录时间之后的余额信息
        self::where('salon_id',$salon_id)->where("count_at",">=",$time)->{$update_method}("balance_money",$money);
        
        //插入记录
        self::create([
            'salon_id'=>$salon_id,
            'type'=>$type,
            'money'=>$money,
            'balance_money'=>$balance_money,
            'count_at'=>$time,
            'remark'=>$remark,
            'created_at'=>date("Y-m-d H:i:s")
        ]);        
    }
    
    /**
     * 重写  免得蛋疼
     * @see \Illuminate\Database\Eloquent\Model::isFillable()
     */
    public function isFillable($key)
    {
        return true;
    }
}
