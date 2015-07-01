<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {

    /**
     * The attributes that are fillable via mass assignment.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'description', 'department_id', 'city_id', 'status', 'note'];

	protected $table = 'roles';

	protected $hidden = ['pivot'];

	public function users(){
		return $this->belongsToMany('App\User');
	}

	public function city(){
		return $this->belongsTo('App\City');
	}

	public function department(){
		return $this->belongsTo('App\Department');
	}

	public function permissions(){
		return $this->belongsToMany('App\Permission');
	}
}
