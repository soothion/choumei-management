<?php
/**
 * 订单表
 * @author zhunian
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends  Model
{
    protected $table = 'order';
    protected $primaryKey = 'orderid';
}

?>