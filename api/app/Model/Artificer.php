<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Artificer extends  Model
{
    protected $table = 'artificer';
    protected $primaryKey = 'artificer_id';
    public $timestamps = false;
}