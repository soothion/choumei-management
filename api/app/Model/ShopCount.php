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
            $salon_id = $attrs['salon_id'];
            $salon = Salon::where('salonid', $salon_id)->first();
            $attrs['salon_name'] = $salon->salonname;
            $attrs['salon_type'] = intval($salon->salonType);
            DB::transaction(function () use($attrs)
            {
                $salon_id = $attrs['salon_id'];
                $class = __CLASS__;
                $model = new $class();
                if (empty($salon_id)) {
                    return false;
                }                
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
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 店铺订单结算
     */
    public static function ShopCountOrder($code,$salon_id,$money,$time,$type,$salon_info,$merchant_info)
    {
        $ds_code =InsteadReceive::getNewCode();
       
        $return_code = 0;
        DB::transaction(function () use($code,$salon_id,$money,$time,$type,$salon_info,$merchant_info,$ds_code,&$return_code)
        {           
            $detail = ShopCountDetail::where("code",$code)->where("type",$type)->get()->toArray();
            $salon_id = isset($salon_info['id']) ? $salon_info['id'] : 0;
            if(!empty($detail))//已存在
            {
                $return_code = 2;
                return ;
            }
            $ret = ShopCountDetail::create([
                'code' => $code,
                'type' => $type,
                'money' => $money,
                'salon_id' => $salon_id,
                'merchant_id' => isset($merchant_info['id']) ? $merchant_info['id'] : 0,
                'created_at' => date("Y-m-d H:i:s", $time)
            ]);
            
            $ir = InsteadReceive::where('salon_id',$salon_id)->where('day',date("Y-m-d", $time))->first();
            if(empty($ir))
            {
                InsteadReceive::create([
                    'code' => $ds_code,
                    'salon_id' => isset($salon_info['id']) ? $salon_id: 0,
                    'merchant_id' => isset($merchant_info['id']) ? $merchant_info['id'] : 0,
                    'type' => InsteadReceive::TYPE_OF_ORDER,
                    'money' => $money,
                    'day' => date("Y-m-d", $time),
                    'created_at' => date("Y-m-d H:i:s")
                ]);
            }
            else
            {
                $now_money = intval($ir->money) + $money;
                InsteadReceive::where('id',$ir->id)->update(['money'=> $now_money]);
            }
          
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
                'merchant_id'=>isset($merchant_info['id'])?$merchant_info['id']:0,
                'merchant_name'=>isset($merchant_info['name'])?$merchant_info['name']:'',
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
                'merchant_name'=>isset($merchant_info['name'])?$merchant_info['name']:'',
                'salon_name'=>isset($salon_info['salon_name'])?$salon_info['salon_name']:'',
                'salon_type'=>isset($salon_info['salon_type'])?$salon_info['salon_type']:0,
                'created_at'=>date("Y-m-d H:i:s",$time),               
                'spend_money'=>$money,
                'balance_money'=>$money * -1,
                ]);
            }
            $return_code = 1;
            return ;         
        });
        return $return_code;
    }
    
    /**
     * 删除预付单
     * @param int $id
     */
    public static function deletePrepay($id)
    {
        $prepays = PrepayBill::where('id',$id)->get()->toArray();
        if(empty($prepays) || !isset($prepays[0]))
        {
            return true;
        }
         $prepay = $prepays[0];
        //只是在预览状态        
        if($prepay['state'] == 0)
        {
            PrepayBill::delete($id);
        }
        //已经生成过
        else if($prepay['state'] == 1)
        {
            $return_code = 0;   
            DB::transaction(function () use($prepay,&$return_code)
            {
                $id = $prepay['id'];
                $state = $prepay['state'];
                $salon_id = $prepay['salon_id'];
                $merchant_id = $prepay['merchant_id'];
                $type = $prepay['type'];
                $pay_money = floatval($prepay['pay_money']);
                $cost_money = floatval($prepay['cost_money']);
                $shop_counts = ShopCount::where('salon_id',$salon_id)->get(['id','pay_money','cost_money','spend_money','balance_money'])->toArray();
                $attrs = [];
                if(!empty($shop_counts) && isset($shop_counts[0]))
                {
                    $shop_count = $shop_counts[0];
                    $old_id = $shop_count['id'];
                    $old_pay_money = floatval($shop_count['pay_money']);
                    $old_cost_money = floatval($shop_count['cost_money']);
                    $old_spend_money = floatval($shop_count['spend_money']);
                    $old_balance_money = floatval($shop_count['balance_money']);
                    $attrs['pay_money'] = $old_pay_money - $pay_money;
                    $attrs['cost_money'] = $old_cost_money - $cost_money;
                    $attrs['balance_money'] = $attrs['cost_money'] - $old_spend_money;
                    self::where('id',$old_id)->update($attrs);
                }
                else
                {
                    $attrs['pay_money'] =  $pay_money * -1;
                    $attrs['cost_money'] = $cost_money * -1;
                    $attrs['balance_money'] = $attrs['cost_money'];
                    $attrs['salon_id'] = $salon_id;
                    $attrs['merchant_id'] = $merchant_id;
                    self::create($attrs);
                }
                PrepayBill::where('id',$id)->delete();
            });
            return true;
        }
        else
        {
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
