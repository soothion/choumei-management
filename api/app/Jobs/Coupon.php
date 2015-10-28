<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Service\NetDesCrypt;
use App\VoucherConf;
use App\Voucher;
use App\User;
use DB;

class Coupon extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
//    use InteractsWithQueue;
    public $DES_KEY = "authorlsptime20141225\0\0\0";
    public $voucherConf = [];
    public $vcId;
    
    public function __construct( $vcId ) {
        Log::info('初始化');
        $this->vcId = $vcId;
        $this->voucherConf = VoucherConf::where(['vcId'=>$vcId])->first()->toArray();
    }
    public function handle(){
        Log::info('开始处理');
        $vcId = $this->vcId;
        // 修改配置表中为已上线状态
        $statusResult = VoucherConf::where(['vcId'=>$vcId])->update(['status'=>1]);

        if(!$statusResult)
            return true;
        
        $data['vcId'] = $this->voucherConf['vcId'];
        $data['vcSn'] = $this->voucherConf['vcSn'];
        $data['vcTitle'] = $this->voucherConf['vcTitle'];
        $data['vUseMoney'] = $this->voucherConf['useMoney'];
        $data['vUseItemTypes'] = $this->voucherConf['useItemTypes'];
        $data['vUseLimitTypes'] = $this->voucherConf['useLimitTypes'];
        $data['vUseNeedMoney'] = $this->voucherConf['useNeedMoney'];
        $data['vUseStart'] = $this->voucherConf['useStart'];
        $data['vUseEnd'] = $this->voucherConf['useEnd'];
        $data['vStatus'] = 3;
        
        // 现阶段将兑换劵总数设定为3000
        $count = $this->voucherConf['useTotalNum'];
        if($count>0)
        {
            $pageSize = 100;
            $totalPage = ceil($count/$pageSize);
            for($page=0; $page < $totalPage; $page++){
                Log::info("正在处理第{$page}页数据");
                $offset = $page*$pageSize;
                $limit = min($count,$offset+$pageSize);
                $insert = ' INSERT cm_voucher (`vcId`,`vcSn`,`vcTitle`,`vUseMoney`,`vUseItemTypes`,`vUseLimitTypes`,`vUseNeedMoney`,`vUseStart`,`vUseEnd`,`vStatus`,`REDEEM_CODE`,`vSn`) VALUES ';
                $i=$offset;
                while($i<$limit) { 
                    $code = $this->encodeCouponCode();
                    $vSn = $this->getVoucherSn('DH');
                    if( $i==0 )
                        $insert .= " ( $vcId , '$vcSn', '$vcTitle',$useMoney, '$useItemTypes', '$useLimitTypes', $useNeedMoney, '$useStart', '$useEnd', 3, '$code', '$vSn')";
                    else
                        $insert .= ",( $vcId , '$vcSn', '$vcTitle',$useMoney, '$useItemTypes', '$useLimitTypes', $useNeedMoney, '$useStart', '$useEnd', 3, '$code', '$vSn')";
                }
                $insert .= ';';
                $result = DB::insert( $insert );
                Log::info("第{$page}页数据处理完成");
                if($result)
                     $i++;
            }
        }
        else
        {
            $code = $this->encodeCouponCode();
            $vSn = $this->getVoucherSn('DH');
            $insert .= " ( $vcId , '$vcSn', '$vcTitle',$useMoney, '$useItemTypes', '$useLimitTypes', $useNeedMoney, '$useStart', '$useEnd', 3, '$code', '$vSn');";
            $result = DB::insert( $insert );
        }
        return true;
	}


    // 获取代金劵编号
    private function getVoucherSn( $p = 'CM' ) {
        $pre = substr(time(), 2);
        $end = '';
        for ($i = 0; $i <3; $i++) {
            $end .= rand(0, 9);
        }
        $code = $p . $pre  . $end;
        $count = \App\Voucher::where('vSn','=',$code)->count();
        if ($count) return $this->getVoucherSn();
        return $code;
   }
   // 加密生成的兑换码
    private function encodeCouponCode(){
        $desModel = new NetDesCrypt;
        $desModel->setKey( $this->DES_KEY );
        $code = $this->createCouponCode();
        $encodeCode = $desModel->encrypt( $code );
        return $encodeCode;
    }

   // 生成原生的兑换码 $zS true : 生成以数字为先 false ： 生成以字母为先
    private function createCouponCode(){
        $code = '';
        $randRange = array(97,122);
        $otherRanger = array( 0,9 );

        while( strlen($code) < 8 ){
            $rand = rand(0,9);
            $zS = array(1,2,3,5,7);
            if( in_array($rand, $zS)  ){
                $randNum = rand( $randRange[0] , $randRange[1] );
                while( $randNum == 108 || $randNum == 111 ){
                    $randNum = rand( $randRange[0] , $randRange[1] );
                }
                $code .= chr($randNum);
            }else{
                $randNum = rand( $otherRanger[0] , $otherRanger[1] );
                $code .= $randNum;
            }
        }
        // 检查不能全部为数字或字符
        if( preg_match('#^[0-9]{8}$#',$code) || preg_match('#^[a-z]{8}$#',$code) || strlen($code) != 8 )
           return $this->createCouponCode();
        return $code;
    }
}
