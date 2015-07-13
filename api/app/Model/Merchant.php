<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model {

	protected $table = 'merchant';
	
	public $timestamps = false;
	
	protected $fillable = ['id', 'sn','name','contact','mobile','phone','email','addr','foundingDate','salonNum','addTime' ];
	


}
