<?php

namespace App\Http\Controllers\Alipay;
use App\Http\Controllers\Controller;
Use App\Common\AlipaySimple;
use App\BountyTask;
class AlipayRefundNotifyController extends Controller{

	/**
	 * 支付宝退款的回调 赏金单
	 */
	function callback_alipay()
	{
	    // 记录回调传入的值 
        $param=$this->param;   
//	    simple_log(date("Y-m-d H:i:s") . "\t " . json_encode($input,JSON_UNESCAPED_UNICODE)."\t\n", "alipay_callback_bounty");
        Log::info("alipay_callback_bounty's param is",$param);
	
	    //以下为debug的写法
	    //$ret = AlipaySimple::callback(array(D("Bounty"),"alipayCallback"),[],true);
	
	    //以下为正式的写法
	    $ret = AlipaySimple::callback(array(BountyTask,"alipayCallback"),[]);
	
	    if($ret)
	    {
	        echo "success";
	    }
	    else
	    {
	        echo "fail";
	    }
	    die();
	}	
}
?>