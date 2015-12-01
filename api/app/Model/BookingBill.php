<?php
use Illuminate\Database\Eloquent\Model;
use DB;
Use PDO;

class BookingBill extends Model
{
    protected $table = 'booking_bill';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
