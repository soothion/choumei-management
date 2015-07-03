<?php
/**
 * 代收单相关
 * @author zhunian
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class InsteadReceive extends Model
{

    protected $table = 'instead_receive';
    
    public function merchant(){
        return $this->belongsTo(Merchant::class);
    }
    
    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }
    
}
