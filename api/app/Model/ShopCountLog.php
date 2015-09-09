<?php
/**
 * 商铺往来结算明细
 * @author zhunian
 */
namespace App;

use Illuminate\Database\Eloquent\Model;
class ShopCountLog extends Model
{
    public $timestamps = false;
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
        
        $change_money = $money;        
       
        if( $type == self::TYPE_OF_SPEND || $type == self::TYPE_OF_COMMISSION_RETURN)
        {
            $change_money *= -1;
        }
        
        if(is_numeric($time))
        {
            $time = date("Y-m-d H:i:s",intval($time));
        }  
        $update_method = "decrement";
        if($change_money >= 0)
        {
            $update_method == "increment";
        }
        
        //之前的信息
        $model = self::where('salon_id',$salon_id)->where("count_at","<",$time)->select("balance_money")->orderBy('count_at','DESC')->orderBy('id','DESC')->first();

        if(!empty($model))
        {
            $balance_money = floatval($model->balance_money) + $change_money;
        }
        else 
        {
            $balance_money = $change_money;
        }
                
        //插入记录
        $id = self::insertGetId([
            'salon_id'=>$salon_id,
            'type'=>$type,
            'money'=>$money,
            'balance_money'=>$balance_money,
            'count_at'=>$time,
            'remark'=>$remark,
            'created_at'=>date("Y-m-d H:i:s")
        ]);
        //更新本条记录之后的余额信息
        self::where('salon_id',$salon_id)->where("id","<>",$id)->where("count_at",">=",$time)->{$update_method}("balance_money",abs($money));
        
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
