<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\SalonUser;
use App\SalonInfo;
class SalonDataPaddingSeeder extends Seeder
{
    /**
     * 完善店铺信息
     * Run the database seeds.
     *
     * @return void
     * 
     * php artisan db:seed --class=SalonDataPaddingSeeder
     */
    public function run()
    {
		$shopArr =  array("预付款店"=>1, "投资店"=>2 ,"金字塔店"=>3);
		$file_handle = fopen("public/Uploads/20150729/salon.csv", "r");
		$s = 1;
		$i = 1;
		while (!feof($file_handle))
		{
			$line = fgetcsv($file_handle);
			if($line && $s>1)
			{
				$save = array();
				//$save["salonname"] = iconv("gbk","UTF-8",$line[4]);//店铺名
				$salonName = iconv("gbk","UTF-8",$line[5]);//店铺名
				if($salonName)
				{
					$shopType = iconv("gbk","UTF-8",$line[1]);//店铺类型
					$save["shopType"] = isset($shopArr[$shopType])?$shopArr[$shopType]:"";
		
					$yName = iconv("gbk","UTF-8",$line[8]);//业务代表
					$userName = DB::table('business_staff')->where('businessName', $yName)->first();
					if($userName)
					{
						$save["businessId"] = $userName->id;
					}
					$save["sn"] = iconv("gbk","UTF-8",$line[4]);//店铺编号
					$save["bargainno"] = iconv("gbk","UTF-8",$line[23]);//合同编号
					$contractTimeArr = explode("-", iconv("gbk","UTF-8",$line[9]));
					$contractTime = strtotime($contractTimeArr[0]);
					$save["contractTime"] = $contractTime;//合同日期
					$save["contractPeriod"] = "3_0";//合同期限
					$affectid = "";
					DB::beginTransaction();
					$row = DB::table('salon')
					->where('salonname', $salonName)
					->update($save);//更新店铺表
						
					$salonData = DB::table('salon')->where('salonname', $salonName)->first();
					if($salonData)
					{
						$salonInfo["bankName"] = iconv("gbk","UTF-8",$line[9]);//开户行
						$salonInfo["beneficiary"] = iconv("gbk","UTF-8",$line[10]);//账户名
						$salonInfo["bankCard"] = iconv("gbk","UTF-8",$line[11]);//账户号
						$salonInfo["accountType"] = 2;//帐户类型
						//$salonInfo["sn"] = iconv("gbk","UTF-8",$line[2]);;//商户编号
						
						$salonInfo["upTime"] = time();

						$whereInfo = array("salonid"=>$salonData->salonid);
						$salonTmpInfo = SalonInfo::where($whereInfo)->first();//检测附表记录是否存在
						if(!$salonTmpInfo)
						{
							DB::table('salon_info')->insertGetId(array("salonid"=>$whereInfo["salonid"]));
						}
						$affectid = SalonInfo::where(array("salonid"=>$whereInfo["salonid"]))->update($salonInfo);
					}
					$name = iconv("gbk","UTF-8",$line[3]);//商户名
					$sn = iconv("gbk","UTF-8",$line[2]);//商户编号
					$merData = DB::table('merchant')->where('sn', $sn)->first();
					if($name && $sn && !$merData)
					{
						DB::table('merchant')->where('name', $name)->update(array("sn"=>$sn));
					}
					
					if($affectid)
					{
						$i++;
						DB::commit();
					}
					else
					{
						DB::rollBack();
					}
				}
			}
				
			$s++;	
		}
		echo $i."ok";
		
		fclose($file_handle);
    }
 
}
