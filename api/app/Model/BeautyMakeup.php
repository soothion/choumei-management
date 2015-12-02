<?php
use Illuminate\Database\Eloquent\Model;
use DB;
Use PDO;

class BeautyMakeup extends Model
{
    protected $table = 'beauty_makeup';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
