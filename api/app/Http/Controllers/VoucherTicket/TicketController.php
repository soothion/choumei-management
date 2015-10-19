<?php
    
    
namespace App\Http\Controllers\VoucherTicket;

use App\Http\Controllers\Controller;
use DB;
use Excel;
use App\Voucher;
use App\Exceptions\ApiException;
use Illuminate\Pagination\AbstractPaginator;
use App\Exceptions\ERROR;

class TicketController extends Controller {
    /**
	 * @api {post} /voucher/list 1.现金卷列表
	 * @apiName list
	 * @apiGroup voucher
	 *
	 * @apiParam {Number} keywordType 可选,关键词类型. 1.活动编号 2. 活动名称 3.现金劵编号 4.用户手机号  5.使用店铺 6. 分享手机号
	 * @apiParam {String} keyword 可选,关键词.
	 * @apiParam {int} status 可选,劵状态.
	 * @apiParam {String} startTime 可选,使用时间 起始日期Y-m-d H:i:s.
	 * @apiParam {String} endTime 可选,使用时间 结束日期Y-m-d H:i:s.
	 * @apiParam {Number} page 可选,页数. 默认为1第一页
	 * @apiParam {Number} pageSize 可选,分页大小. 默认为20
	 *
	 * @apiSuccess {Number} total 总数据量.
	 * @apiSuccess {Number} per_page 分页大小.
	 * @apiSuccess {Number} current_page 当前页面.
	 * @apiSuccess {Number} last_page 当前页面.
	 * @apiSuccess {Number} from 起始数据.
	 * @apiSuccess {Number} to 结束数据.
	 * @apiSuccess {Number} vId 代金券id
	 * @apiSuccess {Number} vSn 代金券编号
	 * @apiSuccess {Number} vcSn 活动编号
	 * @apiSuccess {Number} vcTitle 活动标题.
	 * @apiSuccess {String} vOrderSn 关联订单号.
	 * @apiSuccess {String} vUseMoney 代金券金额.
	 * @apiSuccess {String} vUseTime 使用时间
	 * @apiSuccess {Number} vMobilephone 用户手机号
	 * @apiSuccess {String} vSalonName 店铺名.
	 * @apiSuccess {String} vSalonName 店铺名.
	 * @apiSuccess {Number} vStatus 状态: 1未使用 2已使用 3待激活 4活动关闭 5已失效
	 * @apiSuccess {Number} REDEEM_CODE 兑换码
	 * 
	 *
	 *
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "data": {
	 *	        "total": 51,
	 *	        "per_page": "1",
	 *	        "current_page": 1,
	 *	        "last_page": 51,
	 *	        "from": 1,
	 *	        "to": 1,
	 *	        "data": [
	 *	            {
     *                  "vId": "1",
     *                  "vSn": "CM37811677985",
     *                  "vcTitle": "^__^首次注册可以获取代金券",
     *                  "vOrderSn": "",
     *                  "vUseMoney": 10,
     *                  "vUseStart": 1437580800,
     *                  "vUseEnd": 1437753599,
     *                  "vMobilephone": "18212345608",
     *                  "vSalonName": "",
     *                  "vStatus": 1
     *               },
	 *              ......
	 *	        ]
	 *	    }
	 *	}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 */
    public function ticketList(){
        $post = $this->param;
        $keyword = isset( $post['keyword'] ) ? $post['keyword'] : '';
        $status = isset($post['status']) ? $post['status'] : '';
        $startTime = isset( $post['startTime'] ) ? strtotime($post['startTime']) : '';
        $endTime = isset( $post['endTime'] ) ? strtotime($post['endTime']) : '';
        $page = isset($param['page'])?$param['page']:1;
		$pageSize = isset($param['pageSize'])?$param['pageSize']:20;
        $obj = Voucher::where('vStatus','<>',10);
        if($keyword){
            $keywordType = $post['keywordType'];
            $selectType = array('','vcSn','vcTitle','vSn','vMobilephone','vSalonName'); // 目前缺少分享手机号
            if( !empty( $selectType[ $keywordType ] ) )
                $obj->where( $selectType[ $keywordType ] , 'like' , "%$keyword%" );
        }

        if($status){
			if ($status == 1)
                $obj->where( 'vStatus','=',1 )->where('vUseEnd','>',time());
			elseif ($status == 2)
                $obj->where( 'vStatus','=',2 );
			elseif ($status == 3)
                $obj->whereRaw('vStatus=5 or ('.time().' > vUseEnd and vStatus not in (2,4))');
			elseif( $status == 4)
                $obj->where('vStatus','=',1)->where('len(REDEEM_CODE)','<>',12);
        }

        if($startTime && empty($endTime))
            $obj->where('vUseTime','>=',$startTime);
        
        if($endTime && empty($startTime))
            $obj->where('vUseTime','<=',$endTime);
        
        if($startTime && $endTime)
            $obj->whereBetween('vUseTime',[$startTime,$endTime]);
        $count =  $obj->count();
        //手动设置页数
		AbstractPaginator::currentPageResolver(function() use ($page) {
		    return $page;
		});
		
        $list = $obj->select(['vId','vSn','vcSn','vcTitle','vOrderSn','vUseMoney','vUseTime','vMobilephone','vSalonName','vStatus','REDEEM_CODE'])
                ->orderBy('vId','DESC')
                ->paginate($pageSize)
                ->toArray();
        foreach($list['data'] as $key => $val ){
            $list['data'][$key]['vUseTime'] = empty($list['data'][$key]['vUseTime']) ? '' : date('Y-m-d H:i:s',$val['vUseTime']);
        }
        return $this->success( $list );
    }
    /***
	 * @api {get} /voucher/invalidStatus/:id 2.作废现金卷
	 * @apiName invalidStatus
	 * @apiGroup voucher
	 *
	 * @apiParam {Number} id 代金劵id.
	 *
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *		{
	 *		    "result": 1,
	 *		    "data": {
	 *		    }
	 *		}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "更新失败重新尝试"
	 *		}
	 ***/
    public function invalidStatus($id){
		if( !isset( $id ) )
            throw new ApiException('更新失败', ERROR::RECEIVABLES_ERROR);
        $res = Voucher::where( ["vId",$id] )
                ->where('vStatus',1)
                ->update(['vStatus',5]);
        if( !empty( $res ) )
            return $this->success();
        throw new ApiException('更新失败', ERROR::RECEIVABLES_ERROR);
    }
     /***
	 * @api {get} /voucher/info/:id 3.获取卷详情
	 * @apiName info
	 * @apiGroup voucher
	 *
	 *
      * @apiParam {Number} id 代金劵id.
      * 
      * 
      * 
	 * 
	 * @apiSuccess {Number} vcId 活动id.
	 * @apiSuccess {String} vcSn 活动编号
	 * @apiSuccess {String} vSn 分类名称
	 * @apiSuccess {String} vAddTime 获取时间
	 * @apiSuccess {String} vMobilephone 用户手机号
	 * @apiSuccess {String} vUseMoney 代金券金额
	 * @apiSuccess {String} vUseTime 使用时间
	 * @apiSuccess {String} vUseStart 可使用时间 起始(0 表示不限制)
	 * @apiSuccess {String} vUseEnd 可使用时间 结束(0 表示不限制)
	 * @apiSuccess {String} vOrderSn 关联订单号
	 * @apiSuccess {String} vSalonName 店铺名
	 * @apiSuccess {String} vItemName 项目名
	 * @apiSuccess {String} vcTitle 活动标题
	 * @apiSuccess {String} REDEEM_CODE 兑换码
	 * @apiSuccess {String} isPay 支付
	 * @apiSuccess {String} getText 获取条件
	 * @apiSuccess {String} useLimitText 使用限制
	 * 
	 * @apiSuccessExample Success-Response:
	 *		{
	 *		    "result": 1,
	 *		    "data": [{
     *               "typeid": 1,
     *               "typename": "洗剪吹"
	 *		    },
     *          ...
     *          ]
	 *		}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
     *          "result": 1,
     *          "token": "",
     *           "data": {
     *               "vcId": 8,
     *               "vcSn": "cm999888",
     *               "vSn": "CM45093010561",
     *               "vAddTime": 1445093010,
     *               "vMobilephone": "15820487269",
     *               "vUseMoney": 50,
     *                "vUseTime": 0,
     *                "vUseStart": 0,
     *               "vUseEnd": 0,
     *               "vOrderSn": "",
     *               "vSalonName": "",
     *               "vItemName": "",
     *               "vcTitle": "test-0001",
     *               "REDEEM_CODE": "",
     *               "isPay": "",
     *               "getText": "全平台用户(店铺码用户;洗吹;烫染;限时特价 ;)",
     *                "useLimitText": ""
     *           }
     *     }
	 ***/
    public function info( $id ){
		if( !isset( $id ) )
            throw new ApiException('更新失败', ERROR::RECEIVABLES_ERROR);
        $allItemType = $this->_getItemType();
        $voucherInfo = Voucher::select(['vcId','vcSn','vSn','vAddTime','vMobilephone','vUseMoney','vUseTime','vUseStart','vUseEnd','vOrderSn','vSalonName','vItemName','vcTitle','REDEEM_CODE'])
                ->where('vId','=',$id)
                ->first()
                ->toArray();
        // 如果有关联订单 需要找到支付状态
        if( empty($voucherInfo) )
            throw new ApiException('获取信息失败', ERROR::RECEIVABLES_ERROR);
        if( !empty( $voucherInfo['vOrderSn'] ) ){
            $isPay = \App\Order::select(['ispay'])->where('ordersn','=',$voucherInfo['vOrderSn'])->first();
            if( empty( $isPay ) )
                throw new ApiException('获取信息失败', ERROR::RECEIVABLES_ERROR);
            $voucherInfo['isPay'] = $isPay == 1 ?  '未支付' :  '已支付';
        }else
            $voucherInfo['isPay'] = '';
        $voucherConfInfo = \App\VoucherConf::select(['useItemTypes','useLimitTypes','useNeedMoney','getTypes','getItemTypes','getCodeType','getCode','getNeedMoney'])
                ->where('vcId','=',$voucherInfo['vcId'])
                ->first()
                ->toArray();
        // 查找获取条件
        $getText = ['','新注册;','首次消费;','指定手机号;','指定集团码用户;','指定活动码用户;','指定店铺码用户;','指定消费项目;','全平台用户;','H5用户;'];
        $voucherInfo['getText'] = '';
        if( $voucherConfInfo['getTypes'] == 1 || $voucherConfInfo['getTypes'] == 2 )
            $voucherInfo['getText'] .= $getText[  $voucherConfInfo['getTypes']  ];
        elseif( $voucherConfInfo['getTypes'] == 3 )
            $voucherInfo['getText'] = $getText[  3  ]; 
        elseif( $voucherConfInfo['getTypes'] == 4 ){
            if( !empty($voucherConfInfo['getNeedMoney']) )
                $voucherInfo['getText'].= "项目消费满". $voucherConfInfo['getNeedMoney'] ."元获取;";
            $voucherInfo['getText'] .= '全平台用户('; 
            $codeText = ['','店铺码用户;','集团码用户;','活动码用户;'];
            if( !empty($voucherConfInfo['getCodeType']) )   $voucherInfo['getText'] .= $codeText[ $voucherConfInfo['getCodeType'] ];
            if( !empty( $voucherConfInfo['getItemTypes'] ) ){
                $getTypes = explode(',',$voucherConfInfo['getItemTypes']);
                $tempItemArr = [];
                foreach($allItemType as $val){
                    $tempItemArr[ $val['typeid'] ] = $val;
                }
                foreach( $getTypes as $val){
                    $voucherInfo['getText'] .= $tempItemArr[ $val ]['typename'] . ";";
                }
            }
            $voucherInfo['getText'] .= ")";
        }elseif( $voucherConfInfo['getTypes'] == 5 )
            $voucherInfo['getText'] = 'H5用户;'; 
        
        $voucherInfo['useLimitText'] = '';
        // 查找限制条件
        if( !empty( $voucherConfInfo['useLimitTypes'] ) && $voucherConfInfo['useLimitTypes'] == 2 )
            $voucherInfo['useLimitText'] .= '限制首单;';
        if( !empty( $voucherConfInfo['useItemTypes'] ) ){
            $getTypes = explode(',',$voucherConfInfo['useItemTypes']);
            $tempItemArr = [];
            foreach($allItemType as $val){
                $tempItemArr[ $val['typeid'] ] = $val;
            }
            if( !empty($getTypes) )
                $voucherInfo['useLimitText'] .= "指定";
            foreach( $getTypes as $val){
                $voucherInfo['useLimitText'] .= $tempItemArr[ $val ]['typename'] . ";";
            }
        }
        if( !empty( $voucherConfInfo['useNeedMoney'] ) )
            $voucherInfo['useLimitText'] .= "项目消费满". $voucherConfInfo['useNeedMoney'] ."元使用;";
        return $this->success( $voucherInfo );
	}
    // 获取分类
    private function _getItemType(){
        // 这里用于 代金劵和配置中会和前端约定 增加一个项目特价类型为typeid为101
        $itemType = \App\SalonItemtype::select(['typeid','typename'])
                ->where('status','=',1)
                ->orderBy('sortIt','DESC')
                ->get()
                ->toArray();
        array_unshift( $itemType , array('typeid'=>101,'typename'=>'限时特价 ') );
        return $itemType;
    }
}