<?php 
namespace App\Http\Controllers\Coupon;

use App\Http\Controllers\Controller;
use DB;
use Excel;
use Event;
use App\Voucher;
use App\Exceptions\ApiException;
use Illuminate\Pagination\AbstractPaginator;
use App\Exceptions\ERROR;


class CouponController extends Controller{
    private static  $DES_KEY = "authorlsptime20141225\0\0\0";
	
    /***
	 * @api {get} /coupon/add 1.添加兑换劵活动
	 * @apiName add
	 * @apiGroup Coupon
	 *
	 *@apiParam {String} actName                必填        活动名称
	 *@apiParam {String} actNo                  必填        活动编号
	 *@apiParam {String} actIntro               必填        活动介绍
	 *@apiParam {Number} departmentId           必填        部门id
	 *@apiParam {Number} managerId              必填        部门负责人id
	 *@apiParam {Number} money                  必填        代金券金额
	 *@apiParam {String} getSingleLimit         必填        每个用户获取的劵上限
     *@apiParam {String} useLimitTypes          可选        限制首单使用值为2
     *@apiParam {Number} enoughMoeny            可选        满额可用
     *@apiParam {Number} totalNumber            可选        劵总数
     *@apiParam {String} getTimeStart           可选        劵获取开始时间如 2015-10-16 00:00:00
     *@apiParam {String} getTimeEnd             可选        卷获取结束时间   2015-10-16 23:59:59
	 *@apiParam {String} addActLimitStartTime   可选        代金劵可使用开始时间 2015-10-16 00:00:00
	 *@apiParam {String} addActLimitEndTime     可选        代金劵可使用结束时间 2015-10-16 23:59:59
     *@apiParam {Number} fewDay                 可选        劵获取多少天内可用 （和上面劵可使用时间必须达到传其一）
     *@apiParam {String} limitItemTypes         可选        可使用的项目格式如 ",2,3,"
     *@apiParam {String} sendSms                可选        发送的短信内容
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *		{
	 *		    "result": 1,
	 *		    "data": "",
	 *		}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "添加失败"
	 *		}
	 ***/
    public function addConf(){
        $post = $this->param;
        // 必须填写的参数
        $mustKey = array(
            'actName','actIntro','departmentId','managerId',
            'money','getSingleLimit','actNo'
        );
        // 返回前端数组
        $result = array();
        foreach( $mustKey as $val ){
            if( empty($post[ $val ]) )
                return $this->error('必填写项未填写' );
        }
        
        if( (empty($post['addActLimitStartTime']) && empty($post['addActLimitStartTime'])) && empty($post['fewDay']) )
            return $this->error('必填写项未填写' );
        
        $data = array();
        $data['vcTitle'] = $post['actName'];
        $data['vcSn'] = $post['actNo'];
        $data['vcRemark'] = $post['actIntro'];
        
        // 定义代金劵
        $data['useMoney'] = $post['money'];
        $data['getNumMax'] = $post['getSingleLimit'];
        $data['DEPARTMENT_ID'] = $post['departmentId'];
        $data['MANAGER_ID'] = $post['managerId'];
        if( isset($post['totalNumber']) ) $data['useTotalNum'] = $post['totalNumber'];
        if( isset($post['getTimeStart']) ) $data['getStart'] = strtotime($post['getTimeStart']);
        if( isset($post['getTimeEnd']) ) $data['getEnd'] = strtotime($post['getTimeEnd']);
        if( isset($post['fewDay']) ) $data['FEW_DAY'] = $post['fewDay'];
        if( isset($post['addActLimitStartTime']) ) $data['useStart'] = $post['addActLimitStartTime'];
        if( isset($post['addActLimitEndTime']) ) $data['useEnd'] = $post['addActLimitEndTime'];
        if( isset($post['limitItemTypes']) ) $data['useItemTypes'] = $post['limitItemTypes'];
        if( isset($post['useLimitTypes']) ) $data['useLimitTypes'] = $post['useLimitTypes'];
        if( isset($post['enoughMoeny']) ) $data['useNeedMoney'] = $post['enoughMoeny'];
        if( isset($post['sendSms']) )
            $data['SMS_ON_GAINED'] = $post['sendSms'];
        
        $data['status'] = 2;
        $data['ADD_TIME'] = date('Y-m-d H:i:s');
        $data['IS_REDEEM_CODE'] = 'Y';
        
        $addRes = \App\Model\VoucherConf::insertGetId( $data );

        if( empty($addRes) )
            return $this->error('插入数据失败，请稍后再试');
        return $this->success();
    }
    
    /***
	 * @api {post} /coupon/list 2.代金劵配置列表
	 * @apiName list
	 * @apiGroup Coupon
	 *
	 * @apiParam {Number} selectItem 可选 选择的项 1: 活动编号 2.活动名称
	 * @apiParam {String} number 可选 对应项查询的字符.
	 * @apiParam {Number} status 可选 活动状态. 1. 进行中 2. 暂停 3.已关闭 4. 已结束
	 * @apiParam {String} department 部门id
	 * @apiParam {String} startTime 活动开始时间
	 * @apiParam {String} endTime 活动结束时间
	 * @apiParam {String} page 第几页
	 * @apiParam {String} pageSize 每页请求条数默认为12
     * 
     * 
	 * @apiSuccess {Number} total 总数据量.
	 * @apiSuccess {Number} per_page 分页大小.
	 * @apiSuccess {Number} current_page 当前页面.
	 * @apiSuccess {Number} last_page 当前页面.
	 * @apiSuccess {Number} from 起始数据.
	 * @apiSuccess {Number} to 结束数据.
	 * @apiSuccess {String} vcId 平台配置id
	 * @apiSuccess {String} vcSn 活动编号
	 * @apiSuccess {String} vcTitle 活动名称
	 * @apiSuccess {String} addTime 添加时间
	 * @apiSuccess {String} department 申请部门
	 * @apiSuccess {String} status 1. 进行中 2. 暂停 3.已关闭 4. 已结束
	 * @apiSuccess {String} allNum 发放数
	 * @apiSuccess {String} useNum 兑换数
	 * @apiSuccess {String} totalNum 代金劵可领总数
	 * @apiSuccess {String} actTime 活动时间
	 * 
     * 
	 * @apiSuccessExample Success-Response:
	 *		{
     *           "result": 1,
     *           "token": "",
     *           "data": {
     *               "total": 82,
     *               "per_page": 12,
     *               "current_page": 1,
     *               "last_page": 7,
     *               "next_page_url": "http://man.lu/platform/list/?page=2",
     *               "prev_page_url": null,
     *               "from": 1,
     *               "to": 12,
     *               "data": [
     *                   {
     *                       "vcId": 92,
     *                       "vcSn": cm222292,
     *                       "vcTitle": "我的测试哈哈哈",
     *                       "addTime": "0000-00-00 00:00:00",
     *                       "departmentId": 3,
     *                       "status": 2,
     *                       "allNum": 0,
     *                       "useNum": 0,
     *                       "totalNum": 0,
     *                       "actTime": "无限期活动"
     *                   },
     *                  ...
     *              }
     *          }
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 ***/
    public function confList(){
        $post = $this->param;
        $actSelect = isset($post['selectItem']) ? $post['selectItem'] : '';
        $actNumber = isset($post['number']) ? urldecode($post['number']) : '';
        $actStatus = isset($post['status']) ? $post['status'] : '';
        $actDepartment = isset($post['department']) ? $post['department'] : '';
        $actStartTime = isset($post['startTime']) ? $post['startTime'] : '';
        $actEndTime = isset($post['endTime']) ? $post['endTime'] : '';
        $page = isset( $post['page'] ) ? $post['page'] : 1;
        $pageSize = isset( $post['pageSize'] ) ? $post['pageSize'] : 12;
        
        if( empty($actSelect) && empty($actNumber) && empty($actStatus) && empty($actDepartment) && empty($actStartTime) && empty($actEndTime) ){
            //手动设置页数
            AbstractPaginator::currentPageResolver(function() use ($page) {
                return $page;
            });
            $res = \App\Model\VoucherConf::select(['vcId','vcTitle','vcSn','ADD_TIME as addTime','getStart','getEnd','DEPARTMENT_ID','status','useEnd','useTotalNum as totalNum'])
                    ->where(['vType'=>1,'IS_REDEEM_CODE'=>'Y'])
                    ->orderBy('vcId','desc')
                    ->paginate($pageSize)
                    ->toArray();
            if( empty($res) ) return $this->success();
            
            foreach( $res['data'] as $key=>$val ){
                $statistics = $this->getVoucherStatusByActId($val['vcSn'], $val['useEnd']);
                $res['data'][$key]['allNum'] = $statistics[0];
                $res['data'][$key]['useNum'] = $statistics[1];
                $res['data'][$key]['invalidNum'] = $statistics[2];
                $res['data'][$key]['actTime'] = '';
                if( empty( $val['getStart'] ) && empty($val['getEnd']) )
                    $res['data'][$key]['actTime'] = '无限期活动';
                if( !empty($val['getStart']) && empty($val['getEnd']) )
                    $res['data'][$key]['actTime'] = '开始时间：' . date('Y-m-d H:i:s', $val['getStart']);
                if( !empty($val['getEnd']) && empty($val['getStart']) )
                    $res['data'][$key]['actTime'] = '结束时间：' . date('Y-m-d H:i:s', $val['getEnd']);
                if( !empty( $val['getStart'] ) && !empty($val['getEnd']) )
                    $res['data'][$key]['actTime'] = date('Y-m-d H:i:s', $val['getStart']) . ' - ' . date('Y-m-d H:i:s', $val['getEnd']);
                $res['data'][$key]['department'] = '';
                if( !empty($val['DEPARTMENT_ID']) ){
                    $department = \App\Department::select(['title'])->where(['id'=>$val['DEPARTMENT_ID']])->first();
                    $res['data'][$key]['department'] = $department['title'];
                }
                unset( $res['data'][$key]['useEnd'] );
                unset( $res['data'][$key]['getStart'] );
                unset( $res['data'][$key]['getEnd'] );
                unset( $res['data'][$key]['DEPARTMENT_ID'] );
            }
            return $this->success( $res );
        }
        $where = '1';
        $actType = array('','vcSn','vcTitle');
        $obj = \App\Model\VoucherConf::select(['vcId','vcTitle','vcSn','ADD_TIME as addTime','getStart','getEnd','DEPARTMENT_ID','status','useEnd','useTotalNum as totalNum']);
        $obj->where(['vType'=>1,'IS_REDEEM_CODE'=>'Y']);
        if( !empty($actSelect) && !empty($actNumber) )
            $obj->where( $actType[ $actSelect ] , 'like' , "'%".$actNumber."%'" );
        
        if( !empty($actStatus) ){
            if( $actStatus != 4 )
                $obj->where('status','=',$actStatus);
            else
                $obj->where('getEnd','<',time());
        }
        if( !empty($actStartTime) && !empty($actEndTime))
            $obj->whereRaw(' (getStart <= "'.strtotime($actStartTime) .'" and getEnd >= "'.strtotime($actStartTime) .'") or (getStart <= "'.strtotime($actEndTime) .'" and getEnd >= "'.strtotime($actEndTime) .'" )');
        if( !empty( $actDepartment ) )
            $obj->where('DEPARTMENT_ID','=',$actDepartment);
        //手动设置页数
        AbstractPaginator::currentPageResolver(function() use ($page) {
            return $page;
        });
        $res = $obj->orderBy('vcId','desc')
                    ->paginate($pageSize)
                    ->toArray();
        if( empty($res) ) return $this->success();
            
        foreach( $res['data'] as $key=>$val ){
            $statistics = $this->getVoucherStatusByActId($val['vcSn'], $val['useEnd']);
            $res['data'][$key]['allNum'] = $statistics[0];
            $res['data'][$key]['useNum'] = $statistics[1];
//            $res['data'][$key]['invalidNum'] = $statistics[2];
            $res['data'][$key]['actTime'] = '';
            if( empty( $val['getStart'] ) && empty($val['getEnd']) )
                $res['data'][$key]['actTime'] = '无限期活动';
            if( !empty($val['getStart']) && empty($val['getEnd']) )
                $res['data'][$key]['actTime'] = '开始时间：' . date('Y-m-d H:i:s', $val['getStart']);
            if( !empty($val['getEnd']) && empty($val['getStart']) )
                $res['data'][$key]['actTime'] = '结束时间：' . date('Y-m-d H:i:s', $val['getEnd']);
            if( !empty( $val['getStart'] ) && !empty($val['getEnd']) )
                $res['data'][$key]['actTime'] = date('Y-m-d H:i:s', $val['getStart']) . ' - ' . date('Y-m-d H:i:s', $val['getEnd']);
            $res['data'][$key]['department'] = '';
            if( !empty($val['DEPARTMENT_ID']) ){
                $department = \App\Department::select(['title'])->where(['id'=>$val['DEPARTMENT_ID']])->first();
                $res['data'][$key]['department'] = $department['title'];
            }
            unset( $res['data'][$key]['useEnd'] );
            unset( $res['data'][$key]['getStart'] );
            unset( $res['data'][$key]['getEnd'] );
            unset( $res['data'][$key]['DEPARTMENT_ID'] );
        }
        return $this->success( $res );
    }
    /***
	 * @api {post} /coupon/actView/:id 3.代金劵活动概览
	 * @apiName actView
	 * @apiGroup Coupon
	 *
	 * @apiParam {Number} id 平台活动id
     * 
     * 
     * 
	 * @apiSuccess {String} vcTitle 活动名称
	 * @apiSuccess {String} vcSn 活动编号
	 * @apiSuccess {Number} vcRemark 活动简介.
	 * @apiSuccess {String} status 1. 进行中 2. 暂停 3.已关闭 4. 已结束
	 * @apiSuccess {Number} actTime 活动时间.
	 * @apiSuccess {Number} department 部门.
	 * @apiSuccess {Number} manager 负责人.
	 * @apiSuccess {Number} totalNum 现金券总张数上限.
	 * @apiSuccess {String} budget 现金券预算总金额
	 * @apiSuccess {String} ADD_TIME 添加时间
	 * @apiSuccess {String} department 申请部门
	 * @apiSuccess {String} companyCode 指定集团获取
	 * @apiSuccess {String} activityCode 指定活动获取
	 * @apiSuccess {String} dividendCode 指定店铺获取
	 * @apiSuccess {String} allNum 已发放数
	 * @apiSuccess {String} allMoney 已发放金额
	 * @apiSuccess {String} invalidNum 已失效数
	 * @apiSuccess {String} useNumed 已使用数
	 * @apiSuccess {String} useMoneyed 已使用总金额
	 * @apiSuccess {String} consumeNum 已消费数
	 * @apiSuccess {String} consumeMoney 已消费数金额
	 * @apiSuccess {String} invalidNum 已失效数
	 * 
     * 
	 * @apiSuccessExample Success-Response:
	 *		{
     *           "result": 1,
     *           "token": "",
     *           "data": {
     *               "vcTitle": "指定项目7可以获取",
     *               "vcSn": "cm718745",
     *               "vcRemark": "顶顶顶顶",
     *               "status": 2,
     *               "actTime": "2015-07-08 00:00:00 - 2015-07-30 23:59:59",
     *               "department": "",
     *               "manager": "",
     *               "totalNum": 0,
     *               "budget": 0,
     *               "companyCode": "-",
     *               "activityCode": "-",
     *               "dividendCode": "-",
     *               "allNum": 128,
     *               "allMoney": 1280,
     *               "useNumed": 34,
     *               "useMoneyed": 340,
     *               "consumeNum": 0,
     *               "consumeMoney": 0,
     *               "invalidNum": 3
     *           }
     *       }
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 ***/
    public function actView($id){
        if( empty($id) )
            throw new ApiException('参数错误', ERROR::RECEIVABLES_ERROR);
        $voucherConfInfo = \App\Model\VoucherConf::select(['vcTitle','vcSn','vcRemark','getStart','getEnd','status','DEPARTMENT_ID','MANAGER_ID','useTotalNum','getCodeType','getCode','useMoney'])
                ->where(['vcId'=>$id,'vType'=>1,'IS_REDEEM_CODE'=>'Y'])
                ->first()
                ->toArray();
        
        $voucherConfInfo['actTime'] = '';
        if( empty( $voucherConfInfo['getStart'] ) && empty($voucherConfInfo['getEnd']) )
            $voucherConfInfo['actTime'] = '无限期活动';
        if( !empty($voucherConfInfo['getStart']) && empty($voucherConfInfo['getEnd']) )
            $voucherConfInfo['actTime'] = '开始时间：' . date('Y-m-d H:i:s', $voucherConfInfo['getStart']);
        if( !empty($voucherConfInfo['getEnd']) && empty($voucherConfInfo['getStart']) )
            $voucherConfInfo['actTime'] = '结束时间：' . date('Y-m-d H:i:s', $voucherConfInfo['getEnd']);
        if( !empty( $voucherConfInfo['getStart'] ) && !empty($voucherConfInfo['getEnd']) )
            $voucherConfInfo['actTime'] = date('Y-m-d H:i:s', $voucherConfInfo['getStart']) . ' - ' . date('Y-m-d H:i:s', $voucherConfInfo['getEnd']);
        $voucherConfInfo['department'] = '';
        if( !empty($voucherConfInfo['DEPARTMENT_ID']) ){
            $department = \App\Department::select(['title'])->where(['id'=>$voucherConfInfo['DEPARTMENT_ID']])->first();
            $voucherConfInfo['department'] = $department['title'];
        }
        $voucherConfInfo['manager'] = '';
        if( !empty($voucherConfInfo['MANAGER_ID']) ){
            $manager = \App\Manager::select(['name'])->where(['id'=>$voucherConfInfo['MANAGER_ID']])->first();
            $voucherConfInfo['manager'] = $manager['name'];
        }
        $voucherConfInfo['totalNum'] = '无上限';
        $voucherConfInfo['budget'] = ' - ';
        if( empty( $voucherConfInfo['useTotalNum'] )){
            $voucherConfInfo['totalNum'] = $voucherConfInfo['useTotalNum'];
            $voucherConfInfo['budget'] = $voucherConfInfo['useTotalNum'] * $voucherConfInfo['useMoney'];
        }
        
        $voucherConfInfo['companyCode'] = '-';
        $voucherConfInfo['activityCode'] = '-';
        $voucherConfInfo['dividendCode'] = '-';
        if( !empty($voucherConfInfo['getCodeType']) ){
            $temp = ['','dividendCode','companyCode','activityCode'];
            $voucherConfInfo[ $voucherConfInfo['getCodeType'] ] = $voucherConfInfo['getCode'];
        }
        // 劵情况统计情况
        $voucherConfInfo['allNum'] = 0;
        $voucherConfInfo['allMoney'] = 0;
        $voucherConfInfo['useNumed'] = 0;
        $voucherConfInfo['useMoneyed'] = 0;
        $voucherConfInfo['consumeNum'] = 0;
        $voucherConfInfo['consumeMoney'] = 0;
        $voucherConfInfo['invalidNum'] = 0;
        
        $totalNum = \App\Voucher::whereRaw( 'vcId='.$id.' and vStatus<>10 and vStatus<>3 ' )
                ->count();
        if( empty($totalNum) ){
            return $this->success( $voucherConfInfo );
        }
        $voucherConfInfo['allNum'] = $totalNum;
        $voucherConfInfo['allMoney'] = $totalNum * $voucherConfInfo['useMoney'];
        
        $useNumed = \App\Voucher::where( ['vcId'=>$id,'vStatus'=>2] )
                ->count();
        $voucherConfInfo['useNumed'] = $useNumed;
        $voucherConfInfo['useMoneyed'] = $useNumed * $voucherConfInfo['useMoney'];
        
        $useNumed = \App\Voucher::where( ['vcId'=>$id,'vStatus'=>5] )
                ->count();
        $voucherConfInfo['invalidNum'] = $useNumed;
        
        // 查找已消费数
        if( !empty($useNumed) ){
            $order = Voucher::select(['vOrderSn'])->where( ['vcId'=>$id,'vStatus'=>2] )->get()->toArray();
            $consumeNum = 0;
            foreach( $order as $val ){
                $t = \App\Order::where(['ordersn'=>$val['vOrderSn'],'status'=>4])->count();
                if( $t )
                    $consumeNum++;
            }
            $voucherConfInfo['consumeNum'] = 0;
            $voucherConfInfo['consumeMoney'] = 0;
        }
        if( time() > $voucherConfInfo['getEnd'] )
            $voucherConfInfo['status'] = 4;
        unset( $voucherConfInfo['getStart'] );
        unset( $voucherConfInfo['getEnd'] );
        unset( $voucherConfInfo['DEPARTMENT_ID'] );
        unset( $voucherConfInfo['MANAGER_ID'] );
        unset( $voucherConfInfo['useMoney'] );
        unset( $voucherConfInfo['useTotalNum'] );
        unset( $voucherConfInfo['getCodeType'] );
        unset( $voucherConfInfo['getCode'] );
            
        return $this->success( $voucherConfInfo );
        
    }
    /***
	 * @api {post} /coupon/getInfo/:id 4.读取代金劵配置
	 * @apiName getInfo
	 * @apiGroup Coupon
	 *
	 * @apiParam {Number} id 平台活动id
     * 
     * 
     * 
	 * @apiSuccess {Number} vcId 活动配置
	 * @apiSuccess {String} actName 活动名称
	 * @apiSuccess {String} actNo 活动编号
	 * @apiSuccess {Number} actIntro 活动简介.
	 * @apiSuccess {Number} departmentId 部门.
	 * @apiSuccess {Number} managerId 负责人.
	 * @apiSuccess {Number} money 代金劵金额
	 * @apiSuccess {Number} totalNumber 代金券可领总数
	 * @apiSuccess {Number} limitItemTypes 限制可使用项目类别格式为（,1,2,）
	 * @apiSuccess {Number} useLimitTypes 使用限制类型 2 为限制首单
	 * @apiSuccess {Number} enoughMoeny 限制项目需满足金额才可使用
	 * @apiSuccess {String} addActLimitStartTime    可使用时间.起始(0 表示不限制)
	 * @apiSuccess {String} addActLimitEndTime      可使用时间.结束(0 表示不限制)
	 * @apiSuccess {Number} getSingleLimit       个人可获取最大券数
	 * @apiSuccess {String} getTimeStart 可获取时间 起始(0 表示不限制)
	 * @apiSuccess {String} getTimeEnd   可获取时间 结束(0 表示不限制)
	 * @apiSuccess {String} singleEnoughMoney 获取需满足金额(0表示不限制)
	 * @apiSuccess {String} sendSms 获取代金券时下发的短信内容
	 * @apiSuccess {String} fewDay 获取代金劵后多少天内可用
	 * 
     * 
	 * @apiSuccessExample Success-Response:
	 *		{
     *           "result": 1,
     *           "token": "",
     *           "data": {
     *                       "getSingleLimit": 1,
     *                       "actName": "试哈哈哈",
     *                       "actNo": "cm718745",
     *                       "actIntro": "这是一个简单的介绍",
     *                       "departmentId": 5,
     *                       "managerId": 3,
     *                       "money": 10,
     *                       "code": "",
     *                       "getItemTypes": ",7,",
     *                       "useLimitTypes": "",
     *                       "enoughMoeny": 100,
     *                       "totalNumber": 0,
     *                       "singleEnoughMoney": 0,
     *                       "getTimeStart": 1436284800,
     *                       "getTimeEnd": 1438271999,
     *                       "addActLimitStartTime": 1437580800,
     *                       "addActLimitEndTime": 1437753599,
     *                       "fewDay": 12,
     *                       "getTypes": "0",
     *                       "sendSms": "",
     *                        "getCodeType": 0,
     *                       "selectItem": 2
     *           }
     *       }
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 ***/
    public function getInfo($id){
        if( empty($id) )
            throw new ApiException('参数错误', ERROR::RECEIVABLES_ERROR);
        $voucherConfInfo = \App\Model\VoucherConf::select(['getNumMax as getSingleLimit','vcTitle as actName','vcSn as actNo','vcRemark as actIntro'
            ,'DEPARTMENT_ID as departmentId','MANAGER_ID as managerId','useMoney as money','getCode as code','getItemTypes','useLimitTypes'
            ,'useNeedMoney as enoughMoeny','useTotalNum as totalNumber' ,'getNeedMoney as singleEnoughMoney','getStart as getTimeStart','getEnd as getTimeEnd'
            ,'useStart as addActLimitStartTime','useEnd as addActLimitEndTime','FEW_DAY as fewDay','getTypes','SMS_ON_GAINED as sendSms','getCodeType'])
                ->where(['vcId'=>$id,'vType'=>1,'IS_REDEEM_CODE'=>'Y'])
                ->first()
                ->toArray();
        if( in_array($voucherConfInfo['getTypes'],[1,2]) )
            $voucherConfInfo['selectItem'] = 1;
        if( $voucherConfInfo['getTypes'] == 3 ){
            $voucherConfInfo['selectItem'] = 2;
            $phoneList = \App\Voucher::select(['vMobilephone'])->where(['vcId'=>$id])->get();
            $temp = [];
            foreach( $phoneList as $val ){
                $temp[] = $val['vMobilephone'];
            }
            $voucherConfInfo['phoneList'] = $temp;
        }
        if( empty($voucherConfInfo['getTypes']) && ( in_array($voucherConfInfo['code'],[1,2,3]) || $voucherConfInfo['getItemTypes']) ){
            $voucherConfInfo['selectItem'] = 2;
        }
        if( $voucherConfInfo['getTypes'] == 4 )
            $voucherConfInfo['selectItem'] = 3;
        if( $voucherConfInfo['getTypes'] == 5 )
            $voucherConfInfo['selectItem'] = 4;
        
        return $this->success( $voucherConfInfo );
    }
    /***
	 * @api {get} /coupon/editConf 5.编辑平台代金劵活动
	 * @apiName editConf
	 * @apiGroup Coupon
	 *
	 *@apiParam {String} vcId                   必填        活动配置id
	 *@apiParam {String} actName                可选        活动名称
	 *@apiParam {String} actIntro               可选        活动介绍
	 *@apiParam {Number} departmentId           可选        部门id
	 *@apiParam {Number} managerId              可选        部门负责人id
     *@apiParam {String} useLimitTypes          可选        限制首单使用值为2
     *@apiParam {String} limitItemTypes         可选        可使用的项目格式如 ",2,3,"
     *@apiParam {Number} enoughMoeny            可选        满额可用
     *@apiParam {String} getTimeStart           可选        劵获取开始时间如 2015-10-16 00:00:00
     *@apiParam {String} getTimeEnd             可选        卷获取结束时间   2015-10-16 23:59:59
	 *@apiParam {String} addActLimitStartTime   可选        代金劵可使用开始时间 2015-10-16 00:00:00
	 *@apiParam {String} addActLimitEndTime     可选        代金劵可使用结束时间 2015-10-16 23:59:59
     *@apiParam {Number} fewDay                 可选        劵获取多少天内可用 （和上面劵可使用时间必须达到传其一）
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *		{
	 *		    "result": 1,
	 *		    "data": "",
	 *		}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "添加失败"
	 *		}
	 ***/
    public function editConf(){
        $post = $this->param;
        if( empty( $post['id'] ) )
            throw new ApiException('参数错误', ERROR::RECEIVABLES_ERROR);
        $id = $post['id'];
        
        $data = array();
        if( isset( $post['actName'] ) )  $data['vcTitle'] = $post['actName'];
        if( isset( $post['actIntro'] ) ) $data['vcRemark'] = $post['actIntro'];
        if( isset( $post['departmentId'] ) ) $data['DEPARTMENT_ID'] = $post['departmentId'];
        if( isset( $post['managerId'] ) ) $data['MANAGER_ID'] = $post['managerId'];
        if( isset($post['getTimeStart']) ) $data['getStart'] = strtotime($post['getTimeStart']);
        if( isset($post['getTimeEnd']) ) $data['getEnd'] = strtotime($post['getTimeEnd']);
        if( isset($post['fewDay']) ) $data['FEW_DAY'] = $post['fewDay'];
        if( isset($post['addActLimitStartTime']) ) $data['useStart'] = $post['addActLimitStartTime'];
        if( isset($post['addActLimitEndTime']) ) $data['useEnd'] = $post['addActLimitEndTime'];
        if( isset($post['limitItemTypes']) ) $data['useItemTypes'] = $post['limitItemTypes'];
        if( isset($post['useLimitTypes']) ) $data['useLimitTypes'] = $post['useLimitTypes'];
        if( isset($post['enoughMoeny']) ) $data['useNeedMoney'] = $post['enoughMoeny'];
        
        $addRes = \App\Model\VoucherConf::where(['vcId'=>$id])->update( $data );
        
        if( empty($addRes) )
            return $this->error('插入数据失败，请稍后再试');
        return $this->success();
    }
    /***
	 * @api {get} /coupon/offlineConf/{:id} 6.代金劵平台下线操作
	 * @apiName offlineConf
	 * @apiGroup Coupon
	 *
	 *@apiParam {Number} id                   必填        活动配置id
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *		{
	 *		    "result": 1,
	 *		    "data": "",
	 *		}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "下线失败，请重新下线"
	 *		}
	 ***/
    public function offlineConf( $id ){
        if( empty($id) )
            throw new ApiException('参数错误', ERROR::RECEIVABLES_ERROR);
        $update = \App\Model\VoucherConf::where(['vcId'=>$id])->update(['status'=>2]);
        if( empty( $update ) )
            return $this->error('下线失败，请重新下线');
        return $this->success();
    }
    /***
	 * @api {get} /coupon/closeConf/{:id} 7.代金劵平台关闭操作
	 * @apiName closeConf
	 * @apiGroup Coupon
	 *
	 *@apiParam {Number} id                   必填        活动配置id
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *		{
	 *		    "result": 1,
	 *		    "data": "",
	 *		}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "关闭失败，请重新下线"
	 *		}
	 ***/
    public function closeConf( $id ){
        if( empty($id) )
            throw new ApiException('参数错误', ERROR::RECEIVABLES_ERROR);
        $conf = \App\Model\VoucherConf::where(['vcId'=>$id])->update(['status'=>3]);
        if( $conf )
            \App\Voucher::where(['vcId'=>$id])->whereIn('vStatus',[1,3])->update(['vStatus'=>5]);
        else
            return $this->error('关闭失败，请重新关闭');
        return $this->success();
    }
    /***
	 * @api {get} /coupon/upConf/{:id} 8.代金劵平台活动上线操作
	 * @apiName upConf
	 * @apiGroup Coupon
	 *
	 *@apiParam {Number} id                   必填        活动配置id
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *		{
	 *		    "result": 1,
	 *		    "data": "",
	 *		}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "关闭失败，请重新下线"
	 *		}
	 ***/
    public function upConf($vcId){
        if( empty($id) )
            throw new ApiException('参数错误', ERROR::RECEIVABLES_ERROR);
        // 修改配置表中为已上线状态
        \App\Model\VoucherConf::where(['vcId'=>$vcId])->update(['status'=>1]);
        // 修改voucher表中手机是否已经注册
        $phoneList = \App\Voucher::select(['vMobilephone'])->where(['vStatus'=>10,'vcId'=>$vcId])->get()->toArray();
        //判断当前活动是否勾选了消费类项目
        $voucherConf = \App\Model\VoucherConf::select(['getItemTypes','getNeedMoney','getStart','getEnd'])->where(['vcId'=>$vcId])->first()->toArray();
        
        $getItemTypes=$voucherConf['getItemTypes'];
        $getNeedMoney=$voucherConf['getNeedMoney'];
        
        foreach( $phoneList as $val ){
            $voucherData = array();
            $where1 = 'vStatus=10 AND vMobilephone ="'.$val['vMobilephone'].'"';
            $voucherStatus = $this->verifyUserPhoneExists($val['vMobilephone']);
            // 统一处理 不管有无注册过
            $nowTime=  time();
            // 其中一个不为空则有消费类型  将状态改为3 待激活   
            if(!empty($getItemTypes)||!empty($getNeedMoney)||(!empty($voucherConf['getStart']) && $nowTime< $voucherConf['getStart'])){  
                $voucherData['vStatus']=3;
            }else{ 
               $voucherData['vStatus'] = empty( $voucherStatus ) ? 3 : 1;
            }
            $voucherData['vUserId'] = empty( $voucherStatus ) ? 0 : $voucherStatus['user_id'];
            \App\Voucher::whereRaw( $where1 )->update( $voucherData );
        }
        $this->upActCoupon( $vcId );
        return $this->success();
    }
    /***
	 * @api {get} /coupon/getCoupon/{:id} 9.查看实体券编码和密码
	 * @apiName getCoupon
	 * @apiGroup Coupon
	 *
	 *@apiParam {Number} id                   必填     代金劵配置id
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *		{
	 *		    "result": 1,
	 *		    "data": [
     *              {
     *                   "vSn": "DH45331379719",
     *                   "REDEEM_CODE": "3a7abe91c69e2a6ab2d49ffad6905846",
     *                   "vUseMoney": 50
     *               },
     *             .....
     *         ],
	 *		}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "关闭失败，请重新下线"
	 *		}
	 ***/
    public function getCoupon($vcId){
        if( empty($vcId) )
            throw new ApiException('参数错误', ERROR::RECEIVABLES_ERROR);
        $coupon = \App\Voucher::select(['vSn','REDEEM_CODE','vUseMoney'])->where(['vcId'=>$vcId])->get();
        return $this->success( $coupon );
    }
    /***
	 * @api {get} /coupon/exportCoupon/{:id} 10.导出查看实体券编码和密码
	 * @apiName exportCoupon
	 * @apiGroup Coupon
	 *
	 *@apiParam {Number} id                   必填     代金劵配置id
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *		{
	 *		    "result": 1,
	 *		    "data": "",
	 *		}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "关闭失败，请重新下线"
	 *		}
	 ***/
    public function exportCoupon($vcId){
        if( empty($vcId) )
            throw new ApiException('参数错误', ERROR::RECEIVABLES_ERROR);
        $result = \App\Voucher::select(['vSn','REDEEM_CODE','vUseMoney','vcTitle'])->where(['vcId'=>$vcId])->get()->toArray();
        $desModel = new \Service\NetDesCrypt;
        $desModel->setKey( self::$DES_KEY );
		$title = '代金劵-'. $result[0]['vcTitle'] .date('Ymd');
        foreach( $result as $key => $val ){
            array_unshift($result[$key], $key+1);
            if( strlen( $val['REDEEM_CODE'] ) !=8 )
                $result[$key]['REDEEM_CODE'] = $desModel->decrypt( $val['REDEEM_CODE'] );
            unset( $result[$key]['vcTitle'] );
        }
        //导出excel	   
		$header = ['序号','兑换券编码','兑换券密码','兑换券金额'];
		Excel::create($title, function($excel) use($result,$header){
		    $excel->sheet('Sheet1', function($sheet) use($result,$header){
			        $sheet->fromArray($result, null, 'A1', false, false);//第五个参数为是否自动生成header,这里设置为false
	        		$sheet->prependRow(1, $header);//添加表头

			    });
		})->export('xls');
    }
    /***
	 * @api {post} /coupon/exportList 11.导出代金劵配置列表
	 * @apiName exportList
	 * @apiGroup Coupon
	 *
	 * @apiParam {Number} selectItem 可选 选择的项 1: 活动编号 2.活动名称
	 * @apiParam {String} number 可选 对应项查询的字符.
	 * @apiParam {Number} status 可选 活动状态. 1. 进行中 2. 暂停 3.已关闭 4. 已结束
	 * @apiParam {String} department 部门id
	 * @apiParam {String} startTime 活动开始时间
	 * @apiParam {String} endTime 活动结束时间
     * 
     * 
     * 
	 * @apiSuccessExample Success-Response:
	 *		{
     *           "result": 1,
     *           "token": "",
     *           "data": ""
     *          }
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 ***/
    public function exportList(){
        $post = $this->param;
        $actSelect = isset($post['selectItem']) ? $post['selectItem'] : '';
        $actNumber = isset($post['number']) ? urldecode($post['number']) : '';
        $actStatus = isset($post['status']) ? $post['status'] : '';
        $actDepartment = isset($post['department']) ? $post['department'] : '';
        $actStartTime = isset($post['startTime']) ? $post['startTime'] : '';
        $actEndTime = isset($post['endTime']) ? $post['endTime'] : '';
        $page = isset( $post['page'] ) ? $post['page'] : 1;
        $pageSize = isset( $post['pageSize'] ) ? $post['pageSize'] : 12;
        
        $i = 0;
        if( empty($actSelect) && empty($actNumber) && empty($actStatus) && empty($actDepartment) && empty($actStartTime) && empty($actEndTime) ){
            
            $res = \App\Model\VoucherConf::select(['vcId','vcTitle','vcSn','ADD_TIME as addTime','getStart','getEnd','DEPARTMENT_ID','status','useEnd','useTotalNum as totalNum'])
                    ->where(['vType'=>1,'IS_REDEEM_CODE'=>'Y'])
                    ->orderBy('vcId','desc')
                    ->get()
                    ->toArray();
            if( empty($res) ) return $this->success();
            $tempData = [];
            $department = '';
            foreach( $res as $key=>$val ){
                $statistics = $this->getVoucherStatusByActId($val['vcSn'], $val['useEnd']);
                $actTime = '';
                if( empty( $val['getStart'] ) && empty($val['getEnd']) )
                    $actTime = '无限期活动';
                if( !empty($val['getStart']) && empty($val['getEnd']) )
                    $actTime = '开始时间：' . date('Y-m-d H:i:s', $val['getStart']);
                if( !empty($val['getEnd']) && empty($val['getStart']) )
                    $actTime = '结束时间：' . date('Y-m-d H:i:s', $val['getEnd']);
                if( !empty( $val['getStart'] ) && !empty($val['getEnd']) )
                    $actTime = date('Y-m-d H:i:s', $val['getStart']) . ' - ' . date('Y-m-d H:i:s', $val['getEnd']);
                $res['data'][$key]['department'] = '';
                if( !empty($val['DEPARTMENT_ID']) ){
                    $department = \App\Department::select(['title'])->where(['id'=>$val['DEPARTMENT_ID']])->first();
                    $department = $department['title'];
                }
                $tempData[$key][] = $i++;
                $tempData[$key][] = $val['vcTitle'];
                $tempData[$key][] = $val['vcSn'];
                $tempData[$key][] = $val['totalNum'];
                $tempData[$key][] = $statistics[1] + $statistics[3];
                $tempData[$key][] = $statistics[1];
                $tempData[$key][] = $val['addTime'];
                $tempData[$key][] = $actTime;
                $tempData[$key][] = $department;
            }
            unset( $res );
            $title = '代金劵活动查询列表' .date('Ymd');
            //导出excel	   
            $header = ['序号','活动名称','活动编码','券总数','已兑换数','已使用数','创建时间','活动时间','申请部门'];
            Excel::create($title, function($excel) use($tempData,$header){
                $excel->sheet('Sheet1', function($sheet) use($tempData,$header){
                        $sheet->fromArray($tempData, null, 'A1', false, false);//第五个参数为是否自动生成header,这里设置为false
                        $sheet->prependRow(1, $header);//添加表头

                    });
            })->export('xls');
        }
        $where = '1';
        $actType = array('','vcSn','vcTitle');
        $obj = \App\Model\VoucherConf::select(['vcId','vcTitle','vcSn','ADD_TIME as addTime','getStart','getEnd','DEPARTMENT_ID','status','useEnd','useTotalNum as totalNum']);
        $obj->where(['vType'=>1,'IS_REDEEM_CODE'=>'Y']);
        if( !empty($actSelect) && !empty($actNumber) )
            $obj->where( $actType[ $actSelect ] , 'like' , "'%".$actNumber."%'" );
        
        if( !empty($actStatus) ){
            if( $actStatus != 4 )
                $obj->where('status','=',$actStatus);
            else
                $obj->where('getEnd','<',time());
        }
        if( !empty($actStartTime) && !empty($actEndTime))
            $obj->whereRaw(' (getStart <= "'.strtotime($actStartTime) .'" and getEnd >= "'.strtotime($actStartTime) .'") or (getStart <= "'.strtotime($actEndTime) .'" and getEnd >= "'.strtotime($actEndTime) .'" )');
        if( !empty( $actDepartment ) )
            $obj->where('DEPARTMENT_ID','=',$actDepartment);
        //手动设置页数
        AbstractPaginator::currentPageResolver(function() use ($page) {
            return $page;
        });
        $res = $obj->orderBy('vcId','desc')
                    ->paginate($pageSize)
                    ->toArray();
        if( empty($res) ) return $this->success();
            
        $tempData = [];
        $department = '';
        foreach( $res as $key=>$val ){
            $statistics = $this->getVoucherStatusByActId($val['vcSn'], $val['useEnd']);
            $actTime = '';
            if( empty( $val['getStart'] ) && empty($val['getEnd']) )
                $actTime = '无限期活动';
            if( !empty($val['getStart']) && empty($val['getEnd']) )
                $actTime = '开始时间：' . date('Y-m-d H:i:s', $val['getStart']);
            if( !empty($val['getEnd']) && empty($val['getStart']) )
                $actTime = '结束时间：' . date('Y-m-d H:i:s', $val['getEnd']);
            if( !empty( $val['getStart'] ) && !empty($val['getEnd']) )
                $actTime = date('Y-m-d H:i:s', $val['getStart']) . ' - ' . date('Y-m-d H:i:s', $val['getEnd']);
            $res['data'][$key]['department'] = '';
            if( !empty($val['DEPARTMENT_ID']) ){
                $department = \App\Department::select(['title'])->where(['id'=>$val['DEPARTMENT_ID']])->first();
                $department = $department['title'];
            }
            $tempData[$key][] = $i++;
            $tempData[$key][] = $val['vcTitle'];
            $tempData[$key][] = $val['vcSn'];
            $tempData[$key][] = $val['totalNum'];
            $tempData[$key][] = $statistics[1] + $statistics[3];
            $tempData[$key][] = $statistics[1];
            $tempData[$key][] = $val['addTime'];
            $tempData[$key][] = $actTime;
            $tempData[$key][] = $department;
        }
            unset( $res );
            $title = '代金劵查询列表' .date('Ymd');
            //导出excel	   
            $header = ['序号','活动名称','活动编码','券总数','已兑换数','已使用数','创建时间','活动时间','申请部门'];
            Excel::create($title, function($excel) use($tempData,$header){
                $excel->sheet('Sheet1', function($sheet) use($tempData,$header){
                        $sheet->fromArray($tempData, null, 'A1', false, false);//第五个参数为是否自动生成header,这里设置为false
                        $sheet->prependRow(1, $header);//添加表头

                    });
            })->export('xls');
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
    // 获取代金劵编号
    private function getVoucherSn( $p = 'CM' ) {
        $pre = substr(time(), 2);
        $end = '';
        for ($i = 0; $i <3; $i++) {
            $end .= rand(0, 9);
        }
        $code = $p . $pre  . $end;
        $count = Voucher::where('vSn','=',$code)->count();
        if ($count) 
            return $this->getVoucherSn();
        return $code;
          
   }
   // 获取代金劵状态
   private function getVoucherStatusByActId( $vcSn , $useEnd ){
        // 总的发放数
        $allNum = \App\Voucher::where( ['vcSn'=>$vcSn])->where('vStatus','<>',10)->count();
        // 已发放数
        $useNum = \App\Voucher::where( ['vcSn'=>$vcSn,'vStatus'=>2] )->count();
        // 未使用数
        $noUseNum = \App\Voucher::where( ['vcSn'=>$vcSn,'vStatus'=>1] )->count();
        
        $invalidNum = \App\Voucher::where( ['vcSn'=>$vcSn,'vStatus'=>5] )->count();
        if( !empty($invalidNum) )
            return array( $allNum , $useNum , $invalidNum ,$noUseNum  );
        // 已失效数
        if( empty($useEnd) ||  time()<$useEnd ){
            $invalidNum = 0;
        }else{
            $invalidNum = $allNum - $useNum;
        }
        return array( $allNum , $useNum , $invalidNum ,$noUseNum);
    }
   
    // 点击上线操作生成兑换码劵插入到代金劵表中
    private function upActCoupon( $vcId ){
        $voucherConf = \App\Model\VoucherConf::where(['vcId'=>$vcId])->first()->toArray();
        // 未找到项目配置信息 或 项目配置信息不是兑换活动配置
        if( empty($voucherConf) || $voucherConf['IS_REDEEM_CODE']== 'N'){
            return false;
        }
        $count = \App\Voucher::where(['vcId'=>$vcId])->count();
        if( !empty($count) )
            return false;
        $data['vcId'] = $voucherConf['vcId'];
        $data['vcSn'] = $voucherConf['vcSn'];
        $data['vcTitle'] = $voucherConf['vcTitle'];
        $data['vUseMoney'] = $voucherConf['useMoney'];
        $data['vUseItemTypes'] = $voucherConf['useItemTypes'];
        $data['vUseLimitTypes'] = $voucherConf['useLimitTypes'];
        $data['vUseNeedMoney'] = $voucherConf['useNeedMoney'];
        $data['vUseStart'] = $voucherConf['useStart'];
        $data['vUseEnd'] = $voucherConf['useEnd'];
        $data['vStatus'] = 3;
        for($i=0,$len=$voucherConf['useTotalNum'];$i<$len;$i++){
            $data['REDEEM_CODE'] = $this->encodeCouponCode();
            $data['vSn'] = $this->getVoucherSn('DH');
            $addVoucher = \App\Voucher::insertGetId($data);
//            $addVoucher = $voucherModel->add( $data );
            if(empty($addVoucher)){
                file_put_contents('Application/Runtime/Logs/voucher.log', print_r($data,true),FILE_APPEND);
            }
        }
    }
    // 加密生成的兑换码
    private function encodeCouponCode(){
        $desModel = new \Service\NetDesCrypt;
        $desModel->setKey( self::$DES_KEY );
        $code = $this->createCouponCode();
        $encodeCode = $desModel->encrypt( $code );
        // 判断当前是否存在
        $exists = \App\Voucher::where( ['REDEEM_CODE'=>$encodeCode] )->count();
        if( !empty($exists) ){
            $this->encodeCouponCode();
        }
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
        if( preg_match('#^[0-9]{8}$#',$code) || preg_match('#^[a-z]{8}$#',$code) || strlen($code) != 8 ){
           return $this->createCouponCode();
        }
        return $code;
    }
}