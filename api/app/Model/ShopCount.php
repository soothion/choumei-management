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
                    $attrs['balance_money'] = $attrs['cost_money'] - $attrs['spend_money'];
                    $model::create($attrs);
                } else {
                    $attrs = self::mergeMoney($attrs, $item);
                    $model->update($attrs);
                }
                return true;
            });
        } else {
            return false;
        }
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
