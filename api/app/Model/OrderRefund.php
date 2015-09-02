<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderRefund extends Model
{
    protected $table = 'order_refund';
    protected $primaryKey = 'order_refund_id';
    public $timestamps = false;
}
