<?php
use Illuminate\Database\Eloquent\Model;
use DB;
Use PDO;

class BookingCash extends Model
{
    protected $table = 'booking_cash';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
