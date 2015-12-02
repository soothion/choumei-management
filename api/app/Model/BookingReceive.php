<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
Use PDO;

class BookingReceive extends Model
{
    protected $table = 'booking_receive';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
