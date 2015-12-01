<?php
use Illuminate\Database\Eloquent\Model;
use DB;
Use PDO;

class BookingSalonRefund extends Model
{
    protected $table = 'booking_salon_refund';
    protected $primaryKey = 'ID';
    public $timestamps = false;
}
