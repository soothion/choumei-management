<?php
/**
 * 商铺往来结算相关
 * @author zhunian
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
