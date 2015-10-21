<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyCode extends Model {

	protected $table = 'company_code';
    protected $primaryKey = 'companyId';
    public $timestamps = false;

}
