<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingCalendar extends Model {

	protected $table = 'booking_calendar';
	
	public $timestamps = false;
	
	protected $primaryKey = 'ID';
	
	
}

