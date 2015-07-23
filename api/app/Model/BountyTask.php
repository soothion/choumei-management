<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class BountyTask extends  Model
{
    protected $table = 'bounty_task';
    protected $primaryKey = 'btId';
    public $timestamps = false;
}