<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model {

	protected $table = 'merchant';
	
	protected $fillable = ['id', 'sn','name','contact','mobile','phone','email','addr','foundingDate','salonNum','addTime' ];
	


}
