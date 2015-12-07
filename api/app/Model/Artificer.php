<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Artificer extends  Model
{
    protected $table = 'artificer';
    protected $primaryKey = 'artificer_id';
    public $timestamps = false;
    
    public static function getBaseInfo($id)
    {
        $base = self::where('artificer_id',$id)->first(['artificer_id','name','number']);
        if(empty($base))
        {
            return null;
        }
        return $base->toArray();
    }
}