<?php
/**
 * 转付单相关
 * @author zhunian
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class PrepayBill extends Model
{
 
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
        return $this->belongsTo(User::class,'uid');
    }
    
}
