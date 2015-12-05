<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
Use PDO;

class BeautyOrderItem extends Model
{
    protected $table = 'beauty_order_item';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    public function isFillable($key)
    {
        return true;
    }
}
