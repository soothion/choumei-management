<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model {

	protected $table = 'permissions';

	protected $hidden = ['pivot'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['inherit_id', 'title', 'slug', 'status', 'description', 'note'];

}
