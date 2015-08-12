<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\SalonUser;
use App\Salon;
use App\Merchant;
use App\Dividend;
use App\Town;
use App\CompanyCodeCollect;

class SalonDividendSeeder extends Seeder
{
    /**
     * Run the database seeds.   分红联盟数据插入
     *
     * @return void
     * 
     * php artisan db:seed --class=SalonDividendSeeder
     * 
     */
    public function run()
    {
    	$sql = "select `cm_s`.`salonid`,`cm_s`.`district` from `cm_salon` as `cm_s` left join `cm_dividend` as `cm_d` on `cm_s`.`salonid` = `cm_d`.`salon_id` where `cm_d`.`salon_id` is NULL";
    	$list = DB::select($sql);
    	$i = 0;
    	foreach($list as $val)
    	{
    		$disId = $this->addSalonCode($val->district,$val->salonid);
    		if($disId)
    		{
    			echo $val->salonid;
    			echo "\r\n";
    			$i++;
    		}
    	}
    	echo "OK!".$i;
    }

    
    /**
     * 添加店铺邀请码
     *
     * */
    private function addSalonCode($district,$salonid)
    {
    	$code = $this->getRecommendCode();
    	$townInfo["tname"] = "";
    	if ($district)
    	{
    		$townInfo = Town::where(array("tid"=>$district))->first();
    	}
    	
    	// 写入推荐码表
    	$datas=array (
    			"salon_id" => $salonid,
    			"district" => $townInfo["tname"]?:'',
    			"recommend_code" => $code,
    			"status" => 1,
    			"add_time" => time ()
    	);
    	return DB::table('dividend')->insertGetId($datas);
    }
    
    /**
     * 获取推荐码
     *
     * @return integer
     */
    private function getRecommendCode() {
    	$code = $this->randNum ( 4 );
    	// 如果不是四位随机数则重新生成
    	if (intval ( $code ) < 1000){
    		return $this->getRecommendCode ();
    	}
    
    	// 如果数据库中已在存在则继续执行
    	$codeTmpInfo = Dividend::where(array("recommend_code"=>$code))->first();
    	if ($codeTmpInfo){
    		return $this->getRecommendCode ();
    	}
		        
        // 集团码
        $codeComTmpInfo = CompanyCodeCollect::where(array("code"=>$code))->first();
        if ($codeComTmpInfo){
        	return $this->getRecommendCode ();
        }
    
    	return $code;
    }
    
    /**
     * 生成随即数字
     * @param int $length
     * @return string
     */
    private function randNum($length){
    	$pattern = '12356890';    //字符池,可任意修改
    	$key = '';
    
    	for($i=0;$i<$length;$i++)    {
    		$key .= $pattern{mt_rand(0, strlen($pattern) - 1)};    //生成php随机数
    	}
    
    	return $key;
    }

}
