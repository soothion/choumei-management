<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\SalonUser;
use App\SalonInfo;
use Maatwebsite\Excel\Facades\Excel;
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
		$file = 'public/Uploads/20150729/salon.xlsx';

		Excel::load($file, function($reader) {
		    $reader = $reader->getSheet(0);
		    $results = $reader->toArray();
		    array_shift($results);
    		foreach ($results as $key => $line) {
				$save = array();
				//$save["salonname"] = iconv("gbk","UTF-8",$line[4]);//店铺名
				$salonName = trim($line[5]);//店铺名
				if($salonName)
				{
					$shopType = $line[1];//店铺类型
					$save["shopType"] = isset($shopArr[$shopType])?$shopArr[$shopType]:"";
		
					$yName = $line[8];//业务代表
					$userName = DB::table('business_staff')->where('businessName', $yName)->first();
					if($userName)
					{
						$save["businessId"] = $userName->id;
					}
					$save["sn"] = $line[4];//店铺编号
					$save["bargainno"] = $line[23];//合同编号
					$contractTimeArr = explode("-", $line[9]);
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
						$salonInfo["bankName"] = $line[10];//开户行
						$salonInfo["branchName"] = $line[11];//支行名称
						$salonInfo["beneficiary"] = $line[12];//账户名
						$salonInfo["bankCard"] = $line[13];//账户号
						
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
					$name = $line[3];//商户名
					$sn = $line[2];//商户编号
					$merData = DB::table('merchant')->where('sn', $sn)->first();
					if($name && $sn && !$merData)
					{
						DB::table('merchant')->where('name', $name)->update(array("sn"=>$sn));
					}
						
					if($affectid)
					{
						echo "$salonName update success \n";
						DB::commit();
					}
					else
					{
						echo "$salonName update error \n";
						DB::rollBack();
					}
				}
			}
		});
    }
}
