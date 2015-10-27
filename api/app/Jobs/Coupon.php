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
    
    public function __construct( $vcId ) {
        $this->voucherConf = VoucherConf::where(['vcId'=>$vcId])->first()->toArray();
    }
    public function handle(){
        
         // 修改voucher表中手机是否已经注册
        $phoneList = Voucher::select(['vMobilephone'])->where(['vStatus'=>10,'vcId'=>$vcId])->get()->toArray();
        //判断当前活动是否勾选了消费类项目
        $voucherConf = VoucherConf::select(['getItemTypes','getNeedMoney','getStart','getEnd'])->where(['vcId'=>$vcId])->first()->toArray();
        
        $getItemTypes=$voucherConf['getItemTypes'];
        $getNeedMoney=$voucherConf['getNeedMoney'];

        DB::beginTransaction();
        // 修改配置表中为已上线状态
        $statusResult = VoucherConf::where(['vcId'=>$vcId])->update(['status'=>1]);
        

        $vcId = $this->voucherConf['vcId'];
        $vcSn = $this->voucherConf['vcSn'];
        $vcTitle = $this->voucherConf['vcTitle'];
        $useMoney = $this->voucherConf['useMoney'];
        $useItemTypes = $this->voucherConf['useItemTypes'];
        $useLimitTypes = $this->voucherConf['useLimitTypes'];
        $useNeedMoney = $this->voucherConf['useNeedMoney'];
        $useStart = $this->voucherConf['useStart'];
        $useEnd = $this->voucherConf['useEnd'];
        // 现阶段将兑换劵总数设定为3000
        $insert = ' INSERT cm_voucher (`vcId`,`vcSn`,`vcTitle`,`vUseMoney`,`vUseItemTypes`,`vUseLimitTypes`,`vUseNeedMoney`,`vUseStart`,`vUseEnd`,`vStatus`,`REDEEM_CODE`,`vSn`) VALUES ';
        $len = $this->voucherConf['useTotalNum'];
        if( $len >1 ){
            for($i=0,$len;$i<$len;$i++){
                $code = $this->encodeCouponCode();
                $vSn = $this->getVoucherSn('DH');
                if( $i==0 )
                    $insert .= " ( $vcId , '$vcSn', '$vcTitle',$useMoney, '$useItemTypes', '$useLimitTypes', $useNeedMoney, '$useStart', '$useEnd', 3, '$code', '$vSn')";
                elseif( $i == $len-1 )
                    $insert .= ",( $vcId , '$vcSn', '$vcTitle',$useMoney, '$useItemTypes', '$useLimitTypes', $useNeedMoney, '$useStart', '$useEnd', 3, '$code', '$vSn')";
                else    
                    $insert .= ",( $vcId , '$vcSn', '$vcTitle',$useMoney, '$useItemTypes', '$useLimitTypes', $useNeedMoney, '$useStart', '$useEnd', 3, '$code', '$vSn');";
            }
        }else{
            $code = $this->encodeCouponCode();
            $vSn = $this->getVoucherSn('DH');
            $insert .= " ( $vcId , '$vcSn', '$vcTitle',$useMoney, '$useItemTypes', '$useLimitTypes', $useNeedMoney, '$useStart', '$useEnd', 3, '$code', '$vSn');";
        }
       $result = DB::insert( $insert );

       if($statusResult&&$result)
       {
            DB::commit();
            return true;
       }
       else
       {
            DB::rollBack();
            Log::info('生成兑换劵失败'.$insert);
            return false;
       }
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
        // 判断当前是否存在
        $exists = \App\Voucher::where( ['REDEEM_CODE'=>$encodeCode] )->count();
        if( !empty($exists) ) $this->encodeCouponCode();
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
