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
        $hairstylist = Self::getQuery()->where("stylistId", "=", $id)->first();

        return $hairstylist;
    }

}
