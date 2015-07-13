<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model {

	protected $table = 'management_log';

        protected $fillable = ['username', 'roles', 'operation', 'slug', 'object', 'ip'];

}
