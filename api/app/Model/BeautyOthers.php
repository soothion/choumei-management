<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class BeautyOthers extends  Model
{
    protected $table = 'beauty_others';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    public static function getBaseInfo($id)
    {
        $base = self::where('id',$id)->first(['artificer_id','name','number']);
        if(empty($base))
        {
            return null;
        }
        return $base->toArray();
    }
}