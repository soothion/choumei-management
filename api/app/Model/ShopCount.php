<?php
/**
 * 商铺往来结算相关
 * @author zhunian
 */
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ShopCount extends Model
{
    protected $table = 'shop_count';

   
    public function merchant(){
        return $this->belongsTo(Merchant::class);
    }
    
    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }

    /**
     * 付给商户钱
     * 
     * @param array $option            
     */
    public static function payMoney($attrs)
    {
        if (isset($attrs['salon_id']) && isset($attrs['merchant_id']) && isset($attrs['pay_money']) && isset($attrs['cost_money'])) {
            DB::transaction(function () use($attrs)
            {
                $salon_id = $attrs['salon_id'];
                $class = __CLASS__;
                $model = new $class();
                if (empty($salon_id)) {
                    return false;
                }
                $salon = Salon::where('salonid', $salon_id)->first();
                $attrs['salon_name'] = $salon->salonname;
                $attrs['salon_type'] = intval($salon->salonType);
                $item = $model->where([
                    'salon_id' => $salon_id
                ])->first();
                if (empty($item)) {
                    $attrs['balance_money'] = $attrs['cost_money'];
                    $model::create($attrs);
                } else {
                    $id = $item->id;
                    $attrs = self::mergeMoney($attrs, $item);
                    unset($attrs['id'],$attrs['salon_id']);
                    self::where('id',$id)->update($attrs);
                }
                return true;
            });
        } else {
            return false;
        }
    }
    
    /**
     * 店铺订单结算
     */
    public static function ShopCountOrder($code,$salon_id,$money,$time,$type,$salon_info,$merchant_info)
    {
        DB::transaction(function () use($code,$salon_id,$money,$time,$type,$salon_info,$merchant_info)
        {           
            $detail = ShopCountDetail::where("code",$code)->where("type",$type)->get()->toArray();
            
            if(!empty($detail))//已存在
            {
                return 2;
            }
           
           $ret = ShopCountDetail::create([
            'code'=>$code,
            'type'=>$type,
            'money'=>$money,
            'salon_id'=>isset($salon_info['id'])?$salon_info['id']:0,
            'merchant_id'=>isset($merchant_info['id'])?$merchant_info['id']:0,
            'created_at'=>date("Y-m-d H:i:s",$time)
            ]);
          
            $shop_counts = ShopCount::where('salon_id',$salon_info['id'])->get(['id','pay_money','cost_money','spend_money','balance_money'])->toArray();
          
            if(!empty($shop_counts))
            {
                $shop_count = $shop_counts[0];
                $id = $shop_count['id'];
                $pay_money = floatval($shop_count['pay_money']);
                $cost_money = floatval($shop_count['cost_money']);
                $spend_money = floatval($shop_count['spend_money']) + $money;
                $balance_money = $cost_money - $spend_money;
                ShopCount::where('id',$id)->update([
                'salon_name'=>isset($salon_info['salon_name'])?$salon_info['salon_name']:'',
                'salon_type'=>isset($salon_info['salon_type'])?$salon_info['salon_type']:0,
                'updated_at'=>date("Y-m-d H:i:s",$time),
                'pay_money'=>$pay_money,
                'cost_money'=>$cost_money,
                'spend_money'=>$spend_money,
                'balance_money'=>$balance_money,
                ]);
            }
            else 
            {
                ShopCount::create([
                'salon_id'=>isset($salon_info['id'])?$salon_info['id']:0,
                'merchant_id'=>isset($merchant_info['id'])?$merchant_info['id']:0,
                'salon_name'=>isset($salon_info['salon_name'])?$salon_info['salon_name']:'',
                'salon_type'=>isset($salon_info['salon_type'])?$salon_info['salon_type']:0,
                'created_at'=>date("Y-m-d H:i:s",$time),               
                'spend_money'=>$money,
                'balance_money'=>$money * -1,
                ]);
            }
             return 1;          
        });
    }
    
    public static function mergeMoney($attrs,$model)
    {
        if(isset($attrs['pay_money']) && isset($attrs['cost_money']))
        {
            $attrs['pay_money'] = floatval($model->pay_money) + floatval($attrs['pay_money']);
            $attrs['cost_money'] = floatval($model->cost_money) + floatval($attrs['cost_money']);
            $attrs['spend_money'] = floatval($model->spend_money);
            $attrs['balance_money'] = $attrs['cost_money'] - $attrs['spend_money'];
        }
        
        if(isset($attrs['invest_money']))
        {
            $attrs['invest_money'] = floatval($model->invest_money) + floatval($attrs['invest_money']);
            $attrs['invest_return_money'] = floatval($model->invest_return_money);
            $attrs['invest_balance_money'] = $attrs['invest_money'] - $attrs['invest_return_money'];;
        }
        
        if(isset($attrs['borrow_money']))
        {
            $attrs['borrow_money'] = floatval($model->borrow_money) + floatval($attrs['borrow_money']);
            $attrs['borrow_return_money'] = floatval($model->borrow_return_money);
            $attrs['borrow_balance_money'] = $attrs['borrow_money'] - $attrs['borrow_return_money'];;
        }        
        return $attrs;
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
