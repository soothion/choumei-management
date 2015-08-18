<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use DB;
class SalonChangeGradeSeeder extends Seeder
{
    /**
     * 每天0：3分    调整店铺等级
     * Run the database seeds.
     *
     * @return void
     * 
     * php artisan db:seed --class=SalonChangeGrade  
     */
    public function run()
    {
		//$row = DB::table('salon')->where('salonGrade', $salonName)->update(array("sn"=>$salonSn));
		echo 22;
    }
 
}
