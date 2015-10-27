<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Hairstylist extends Model {

    protected $table = 'hairstylist';
    protected $primaryKey = 'stylistId';
    public $timestamps = false;

    public static function getHairstylistsByIds($hairstylistIds) {
        $hairstylists = self::whereIn("stylistId", $hairstylistIds)->get();
        return $hairstylists;
    }

    public static function getHairstylistById($id) {
        $hairstylist = Self::getQuery()->where("stylistId", "=", $id)->get();

        if (empty($hairstylist)) {
            return [];
        } else {
            return $hairstylist[0];
        }
    }
    /**
     * 检测造型师是否有对应快剪等级
     * */
    public static function checkHairerGrade($fastGrade,$salonid)
    {
    	if(!$salonid || !$fastGrade) return false;
    	$where['fastGrade'] = $fastGrade;
    	$where['status'] = 1;
    	$where['salonId'] = $salonid;
    	$rs = self::where($where)->select(['stylistName','stylistId'])->first();
    	if($rs)
    		return true;
    	else
    		return false;
    }

}

