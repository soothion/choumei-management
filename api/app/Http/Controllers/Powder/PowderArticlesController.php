<?php

namespace App\Http\Controllers\Powder;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Model\Present;
use App\Model\PresentArticleCode;
use DB;

use App\Exceptions\ApiException;
use App\Exceptions\ERROR;

class PowderArticlesController extends Controller
{
    private static $articleStatusName = array(
        '活动正常',
        '停止活动',
        '停止验证',
        '活动过期',
    );
    private static $ticketCodeStatus = array(
        1 =>'已使用',
        2 =>'未使用',
        3 =>'已过期',
    );
    private static $presentTypeName= array(
        1=>'消费赠送',
        2=>'推荐赠送',
        3=>'活动赠送',
    );
    /**
     * @api {post} /PowderArticles/addArticles 1.添加活动
     * 
     * @apiName addArticles
     * @apiGroup PowderArticles
     *
     * @apiParam {String} articleName 必填，活动名
     * @apiParam {Number} itemId 必填，项目id
     * @apiParam {Number} nums 必填，活动数量
     * @apiParam {String} startTime 必填，活动开始时间
     * @apiParam {String} endTime 必填，活动结束时间
     * @apiParam {String} expireTime 必填，活动有效时间
     * @apiParam {Number} departmentId 必填, 部门id
     * @apiParam {Number} userId 必填, 负责人id
     * @apiParam {String} detail 可选，内容
     * 
     * @apiSuccess {Array} data
     * 
     * 
     * @apiSuccessExample Success-Response:
     * 	{
     *       "result": 1,
     *       "token": "",
     *       "data": {
     *           "presentId": 3
     *       }
     *   }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     *               "result": 0,
     *               "code": 0,
     *               "token": "",
     *               "msg" :"必传参数不能为空",
     *           }
     */
    /**
    * 添加定妆赠送活动
     */
    public function addArticles()
    {
        $param = $this->param;
        $createrId = $this->user->id;
        if(empty($param['articleName']) || empty($param['itemId']) || empty($param['nums']) || empty($param['startTime']) || empty($param['endTime']) || empty($param['expireTime']) || empty($param['departmentId']) || empty($param['userId'])){
            throw new ApiException('必传参数不能为空');
        }
        if(strlen($param['articleName']) > 60){
            throw new ApiException('活动名称长度限制20字');
        }
        if(strlen($param['nums']) > 8){
            throw new ApiException('活动数量不能超过8位数');
        }
        if(strtotime($param['startTime']) < strtotime(date('Y-m-d'))){
            throw new ApiException('活动开始时间错误');
        }
        if(strtotime($param['startTime']) > strtotime($param['endTime'])){
            throw new ApiException('活动结束时间必须大于开始时间');
        }
        if(strtotime($param['endTime']) > strtotime($param['expireTime'])){
            throw new ApiException('活动有效时间必须大于活动结束时间');
        }
        if(!empty($param['detail']) && strlen(urldecode($param['detail'])) > 3000){
            throw new ApiException('活动简介内容长度超出');
        }
        //通过活动名称查询是否有重复
        $count = Present::where('name','=',$param['articleName'])->count();
        if($count){
            throw new ApiException('活动名称已存在',ERROR::POWDER_ARTICLE_NAME_EXIST);
        }
        $data['name'] = $param['articleName'];
        $data['item_id'] = $param['itemId'];
        $data['quantity'] = $param['nums'];      
        $data['start_at'] = $param['startTime'];
        $data['end_at']  = $param['endTime']." 23:59:59";
        $data['expire_at'] = $param['expireTime'];
        $data['department_id'] = $param['departmentId'];
        $data['user_id'] = $param['userId'];
        $data['creater_id'] = $createrId;
        $data['detail'] = isset($param['detail']) ? $param['detail'] : '' ;
        $data['created_at'] = time();
        $resId = Present::insertGetId($data);
        //$queries = DB::getQueryLog();
        if($resId){
            $res['presentId'] = $resId;
            return $this->success($res);
        }else{
            throw new ApiException('活动添加失败',ERROR::POWDER_ARTICLE_ADD_FIELD);
        }
    }
    /**
     * @api {post} /PowderArticles/articlesList 2.定妆活动列表
     * 
     * @apiName articlesList
     * @apiGroup PowderArticles
     *
     * @apiParam {String} articleName 选填，活动名
     * @apiParam {Number} departmentId 选填，部门id
     * @apiParam {String} startTime 选填，活动创建开始时间 YY-MM-DD
     * @apiParam {String} endTime 选填，活动创建结束时间 YY-MM-DD
     * 
     * @apiSuccess {Number} total 总数据量.
     * @apiSuccess {Number} per_page 分页大小.
     * @apiSuccess {Number} current_page 当前页面.
     * @apiSuccess {Number} last_page 当前页面.
     * @apiSuccess {Number} from 起始数据.
     * @apiSuccess {Number} to 结束数据.
     * 
     * @apiSuccess {Number} presentId 活动id.
     * @apiSuccess {String} articleName 活动名.
     * @apiSuccess {Number} itemId 项目id.
     * @apiSuccess {String} itemName 项目名.
     * @apiSuccess {Number} quantity 券总数.
     * @apiSuccess {Number} useNum 使用数量.
     * @apiSuccess {Number} notUseNum 未使用数.
     * @apiSuccess {String} startTime 活动开始时间.
     * @apiSuccess {String} endTime 活动结束时间.
     * @apiSuccess {String} expireTime 活动有效时间.
     * @apiSuccess {Number} departmentId 部门id.
     * @apiSuccess {String} departmentName 部门名.
     * @apiSuccess {Number} userId 负责人id.
     * @apiSuccess {Number} createrId 创建人id.
     * @apiSuccess {String} detail 活动详情.
     * @apiSuccess {Number} articleStatus 活动状态 1: 开启  2: 关闭'.
     * @apiSuccess {Number} verifyStatus 验证状态 1: 开启验证 2: 关闭验证.
     * @apiSuccess {Number} articleType 活动类型 1: 线下  2: 线上.
     * @apiSuccess {String} createTime 活动创建时间
     * @apiSuccess {String} articleStatusName 活动状态名
     * 
     * @apiSuccessExample Success-Response:
     * 	{
     *       "token": "",
     *       "data": {
     *           "total": 4,
     *           "per_page": 20,
     *           "current_page": 1,
     *           "last_page": 1,
     *           "from": 1,
     *           "to": 4,
     *           "data": [
     *               {
     *                   "presentId": 4,
     *                   "articleName": "test4",
     *                   "itemId": 1,
     *                   "itemName": "测试000",
     *                   "quantity": 10,
     *                   "useNum": 0,
     *                   "startTime": "2015-11-30 00:00:00",
     *                   "endTime": "2015-11-30 23:59:59",
     *                   "expireTime": "2015-12-03 00:00:00",
     *                   "departmentId": 1,
     *                   "departmentName": "总裁办",
     *                   "userId": 9527,
     *                   "createrId": 9527,
     *                   "detail": "这是一个活动",
     *                   "articleStatus": 1,
     *                   "verifyStatus": 1,
     *                   "articleType": 1,
     *                   "createTime": "2015-11-30",
     *                   "articleStatusName": "活动正常",
     *                   "notUseNum": 10
     *               },
     *               {
     *                   "presentId": 1,
     *                   "articleName": "test1",
     *                   "itemId": 1,
     *                   "itemName": "测试000",
     *                   "quantity": 10,
     *                   "useNum": 0,
     *                   "startTime": "2015-11-30 00:00:00",
     *                   "endTime": "2015-11-30 23:59:59",
     *                   "expireTime": "2015-12-03 00:00:00",
     *                   "departmentId": 1,
     *                   "departmentName": "总裁办",
     *                   "userId": 9527,
     *                   "articleStatus": 1,
     *                   "verifyStatus": 1,
     *                   "articleType": 1,
     *                   "createTime": "1970-01-01",
     *                   "articleStatusName": "活动正常",
     *                   "notUseNum": 10
     *               }
     *           ]
     *       }
     *   }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     *               "result": 0,
     *               "code": 0,
     *               "token": "",
     *               "msg" :"必传参数不能为空",
     *           }
     */
    /**
    * 定妆赠送活动列表
     */
    public function articlesList()
    {
        $param = $this->param;
        $name = isset($param['articleName']) ? trim($param['articleName']) :'';
        $departmentId = isset($param['departmentId']) ? trim($param['departmentId']) :'';
        
        $startTime = isset($param['startTime'])? strtotime($param['startTime']):'';
        $endTime = isset($param['endTime'])? strtotime($param['endTime']." 23:59:59"):'';
        
        $page = isset($param['page'])?max($param['page'],1):1;
        $pageSize = isset($param['pageSize'])?$param['pageSize']:20;
        
        $articlesList = Present::getArticlesList($name,$departmentId,$startTime,$endTime,$page,$pageSize);
        foreach ($articlesList['data'] as $key => &$val) {
           if($val['articleType'] == 2){
               $val['articleType'] = "获赠三个月内有效";
           }
           if(time() > strtotime($val['expireTime'])){
               $val['articleStatusName'] = self::$articleStatusName[3]; //活动已过期
           }elseif ($val['verifyStatus'] == 2){
               $val['articleStatusName'] = self::$articleStatusName[2];  //关闭验证
           }elseif ($val['articleStatus'] == 2) {
               $val['articleStatusName'] = self::$articleStatusName[1];  //停止活动
           }else{
               $val['articleStatusName'] = self::$articleStatusName[0];  //活动正常
           }
           $val['notUseNum'] = $val['quantity'] - $val['useNum'];
           $val['createTime'] = date('Y-m-d',$val['createTime']);
        }
        return $this->success($articlesList);
        
    }
    
    /**
     * @api {post} /PowderArticles/showArticlesInfo 3.定妆活动详情
     * 
     * @apiName showArticlesInfo
     * @apiGroup PowderArticles
     *
     * @apiParam {Number} presentId 必填，活动id
     * 
     * @apiSuccess {Number} presentId 活动id.
     * @apiSuccess {String} articleName 活动名.
     * @apiSuccess {Number} itemId 项目id.
     * @apiSuccess {String} itemName 项目名.
     * @apiSuccess {Number} quantity 券总数.
     * @apiSuccess {Number} useNum 使用数量.
     * @apiSuccess {Number} notUseNum 未使用数.
     * @apiSuccess {String} startTime 活动开始时间.
     * @apiSuccess {String} endTime 活动结束时间.
     * @apiSuccess {String} expireTime 活动有效时间.
     * @apiSuccess {Number} departmentId 部门id.
     * @apiSuccess {String} departmentName 部门名.
     * @apiSuccess {Number} userId 用户id.
     * @apiSuccess {String} detail 活动详情.
     * @apiSuccess {Number} articleStatus 活动状态 1: 开启  2: 关闭'.
     * @apiSuccess {Number} verifyStatus 验证状态 1: 开启验证 2: 关闭验证.
     * @apiSuccess {Number} articleType 活动类型 1: 线下  2: 线上.
     * @apiSuccess {String} createTime 活动创建时间
     * @apiSuccess {String} managerName 负责人
     * 
     * @apiSuccessExample Success-Response:
     * 	{
     *       "result": 1,
     *       "token": "",
     *       "data": {
     *           "presentId": 5,
     *           "articleName": "test9",
     *           "itemId": 1,
     *           "itemName": "韩式纤体",
     *           "quantity": 10,
     *           "useNum": 0,
     *           "startTime": "2015-12-30 00:00:00",
     *           "endTime": "2015-12-30 23:59:59",
     *           "expireTime": "2016-12-03 00:00:00",
     *           "departmentId": 1,
     *           "departmentName": "总裁办",
     *           "userId": 115,
     *           "detail": "测试",
     *           "articleStatus": 1,
     *           "verifyStatus": 1,
     *           "articleType": 1,
     *           "createTime": "2015-12-01",
     *           "managerName": "唐敏",
     *           "notUseNum": 10
     *       }
     *   }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     *               "result": 0,
     *               "code": 0,
     *               "token": "",
     *               "msg" :"必传参数不能为空",
     *           }
     */
    /**
    * 定妆赠送活动详情
     */
    public function showArticlesInfo()
    {
        $param = $this->param;
        if(empty($param['presentId'])){
            throw new ApiException('必传参数不能为空');    
        }
        $where = array('present_id' => $param['presentId']);
        $articlesInfo = Present::getArticlesInfoByWhere($where);
        if(!empty($articlesInfo)){
            $articlesInfo['notUseNum'] = $articlesInfo['quantity'] - $articlesInfo['useNum'];
            $articlesInfo['createTime'] = date('Y-m-d',$articlesInfo['createTime']);       
        }
        return $this->success($articlesInfo);       
    }
    
    /**
     * @api {post} /PowderArticles/switchArticles 4.定妆活动开关
     * 
     * @apiName switchArticles
     * @apiGroup PowderArticles
     *
     * @apiParam {Number} presentId 必填，活动id
     * @apiParam {Number} articleStatus 必填，活动开启或关闭 1 开启  2 关闭
     * 
     * @apiSuccess {Array} data 空.
     *
     * @apiSuccessExample Success-Response:
     * 	{
     *       "result": 1,
     *       "token": "",
     *       "data": []
     *   }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     *               "result": 0,
     *               "token": "",
     *               "msg" :"必传参数不能为空",
     *           }
     */
    /**
    * 定妆赠送活动开关
     * 1 开启活动  2 关闭活动
     */
    public function switchArticles()
    {
        $param = $this->param;
        if(empty($param['presentId']) || empty($param['articleStatus'])){
            throw new ApiException('必传参数不能为空');    
        }
        if(!in_array($param['articleStatus'],array(1,2))){
            throw new ApiException('活动开关参数错误');
        }
        $where = array('present_id' => intval($param['presentId']));
        $field = array('expire_at','article_status as articleStatus');
        $articlesInfo = Present::select($field)->where($where)->first();
        if(!empty($articlesInfo)){
            if(time() > strtotime($articlesInfo['expire_at'])){
                throw new ApiException('活动已过期');
            }elseif($articlesInfo['articleStatus'] == $param['articleStatus']){
                if($param['articleStatus'] == 1){
                    throw new ApiException('活动已开启，无需再开启');
                }
                if($param['articleStatus'] == 2){
                    throw new ApiException('活动已关闭，无需再关闭');
                }
            }else {
                //进行更新
                $data = array('article_status' => $param['articleStatus'],'updated_at' => time());
                $res = Present::where($where)->update($data);
                if($res === false){
                    throw new ApiException('更新失败',ERROR::POWDER_ARTICLE_SWITCH_STATUS);
                }else{
                    return $this->success();
                }
            }
        }else{
            throw new ApiException('活动不存在');
        }
    }
    /**
     * @api {post} /PowderArticles/switchVerifyArticles 5.定妆活动验证开关
     * 
     * @apiName switchVerifyArticles
     * @apiGroup PowderArticles
     *
     * @apiParam {Number} presentId 必填，活动id
     * @apiParam {Number} verifyStatus 必填，活动开启或关闭 1 开启  2 关闭
     * 
     * @apiSuccess {Array} data 空.
     *
     * @apiSuccessExample Success-Response:
     * 	{
     *       "result": 1,
     *       "token": "",
     *       "data": []
     *   }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     *               "result": 0,
     *               "token": "",
     *               "msg" :"必传参数不能为空",
     *           }
     */
    /**
    * 定妆赠送活动验证开关 1 开启 2 关闭
     */
    public function switchVerifyArticles()
    {
        $param = $this->param;
        if(empty($param['presentId']) || empty($param['verifyStatus'])){
            throw new ApiException('必传参数不能为空');    
        }
        if(!in_array($param['verifyStatus'],array(1,2))){
            throw new ApiException('活动开关参数错误');
        }
        $where = array('present_id' => intval($param['presentId']));
        $field = array('expire_at','verify_status as verifyStatus');
        $articlesInfo = Present::select($field)->where($where)->first();
        if(!empty($articlesInfo)){
            if(time() > strtotime($articlesInfo['expire_at'])){
                throw new ApiException('活动已过期');
            }elseif($articlesInfo['verifyStatus'] == $param['verifyStatus']){
                if($param['articleStatus'] == 1){
                    throw new ApiException('活动验证已开启，无需再开启');
                }
                if($param['articleStatus'] == 2){
                    throw new ApiException('活动验证已关闭，无需再关闭');
                }
            }else {
                //进行更新
                $data = array('article_status' => $param['articleStatus'],'updated_at' => time());
                $res = Present::where($where)->update($data);
                if($res === false){
                    throw new ApiException('更新失败',ERROR::POWDER_ARTICLE_SWITCH_VERIFY_STATUS);
                }else{
                    return $this->success();
                }
            }
        }else{
            throw new ApiException('活动不存在');
        }    
    }
    /**
     * @api {post} /PowderArticles/articlesTicketList 6.兑换券详情
     * 
     * @apiName articlesTicketList
     * @apiGroup PowderArticles
     *
     * @apiParam {Number} presentId 必填，活动id
     * 
     * @apiSuccess {Number} total 总数据量.
     * @apiSuccess {Number} per_page 分页大小.
     * @apiSuccess {Number} current_page 当前页面.
     * @apiSuccess {Number} last_page 当前页面.
     * @apiSuccess {Number} from 起始数据.
     * @apiSuccess {Number} to 结束数据.
     * 
     * @apiSuccess {String} itemName 项目名.
     * @apiSuccess {Number} TicketCode 券号.
     * @apiSuccess {String} startTime 活动开始时间.
     * @apiSuccess {String} endTime 活动结束时间.
     * @apiSuccess {String} ticketStatusName 券状态名.
     * 
     * @apiSuccessExample Success-Response:
     * {
     *       "result": 1,
     *       "token": "",
     *       "data": {
     *           "total": 2,
     *           "per_page": 20,
     *           "current_page": 1,
     *           "last_page": 1,
     *           "from": 1,
     *           "to": 2,
     *           "data": [
     *               {
     *                   "itemName": "韩式无痛水光针（赠送）",
     *                   "TicketCode": "502",
     *                   "startTime": "2015-11-30 00:00:00",
     *                   "endTime": "2015-11-30 23:59:59",
     *                   "ticketStatus": 1,
     *                   "ticketStatusName": "已使用"
     *               },
     *               {
     *                   "itemName": "韩式无痛水光针（赠送）",
     *                   "TicketCode": "502",
     *                   "startTime": "2015-11-30 00:00:00",
     *                   "endTime": "2015-11-30 23:59:59",
     *                   "ticketStatus": 2,
     *                   "ticketStatusName": "未使用"
     *               }
     *           ]
     *       }
     *   }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     *               "result": 0,
     *               "code": 0,
     *               "token": "",
     *               "msg" :"必传参数不能为空",
     *           }
     */
    /**
    * 兑换券详情
     */
    public function articlesTicketList()
    {
        $param = $this->param;
        if(empty($param['presentId'])){
            throw new ApiException('必传参数不能为空');    
        }
        $page = isset($param['page'])?max($param['page'],1):1;
        $pageSize = isset($param['pageSize'])?$param['pageSize']:20;
        
        $articleTicketInfoRes =  PresentArticleCode::getArticleTicketInfo($param['presentId'],$page,$pageSize);
        foreach($articleTicketInfoRes['data'] as $key => &$val){
            $val['ticketStatusName'] = self::$ticketCodeStatus[$val['ticketStatus']];
        }
        return $this->success($articleTicketInfoRes);
    }
    /**
     * @api {post} /PowderArticles/exportArticlesTicketList 7.导出定妆活动券
     * 
     * @apiName exportArticlesTicketList
     * @apiGroup PowderArticles
     *
     * @apiParam {Number} presentId 必填，活动id
     * 
     * @apiErrorExample Error-Response:
     * 		{
     *               "result": 0,
     *               "code": 0,
     *               "token": "",
     *               "msg" :"必传参数不能为空",
     *           }
     */
    /**
     * 导出券
     */
    public function exportArticlesTicketList(){
        $param = $this->param;
        if(empty($param['presentId'])){
            throw new ApiException('必传参数不能为空');    
        }
        //获取活动名称
        $where = array('present_id'=>$param['presentId']);
        $articleInfo = Present::getArticleInfoByWhere($where);
        $articleAllTicketInfoRes =  PresentArticleCode::getAllArticleTicketInfoForExport($param['presentId']);
        foreach ($articleAllTicketInfoRes as $key => $val) {
            $res['$key']['itemName'] = $val->itemName;
            $res['$key']['ticketCode'] = $val->ticketCode;
            $res['$key']['startTime'] = $val->startTime;
            $res['$key']['endTime'] = $val->endTime;
            $res['$key']['ticketStatusName'] = self::$ticketCodeStatus[$val->ticketStatus]; 
        }
        
        $header = [
            '赠送项目',
            '赠送券编码',
            '活动起始日',
            '活动截止日',
            '状态',
        ];
//        if (!empty($res)) {
//            Event::fire("appointment.export");
//        }
        @ini_set('memory_limit', '256M');
        $this->export_xls($articleInfo['name'] . date("Ymd"), $header, $res);
    }
    
    /**
     * @api {post} /PowderArticles/presentList 8.定妆赠送查询列表
     * 
     * @apiName presentList
     * @apiGroup PowderArticles
     *
     * @apiParam {Number} mobilephone 选填，手机号
     * @apiParam {Number} reservateSn 选填，预约号
     * @apiParam {Number} recommendCode 选填，推荐码
     * @apiParam {Number} ticketCode 选填，券号
     * @apiParam {Number} presentType 选填，赠送方式
     * @apiParam {Number} ticketStatus 选填，券使用状态
     * @apiParam {String} startTime 选填，赠送开始时间 YY-MM-DD
     * @apiParam {String} endTime 选填，赠送结束时间 YY-MM-DD
     * 
     * @apiSuccess {Number} total 总数据量.
     * @apiSuccess {Number} per_page 分页大小.
     * @apiSuccess {Number} current_page 当前页面.
     * @apiSuccess {Number} last_page 当前页面.
     * @apiSuccess {Number} from 起始数据.
     * @apiSuccess {Number} to 结束数据.
     * 
     * @apiSuccess {Number} articleCodeId 活动赠送券记录id
     * @apiSuccess {Number} presentId 活动id.
     * @apiSuccess {Number} reservateSn 预约号.
     * @apiSuccess {Number} orderSn 系统内部订单号.
     * @apiSuccess {Number} itemId 项目id.
     * @apiSuccess {String} ticketCode 券号.
     * @apiSuccess {Number} ticketStatus 券状态.
     * @apiSuccess {Number} mobilephone 手机号.
     * @apiSuccess {Number} recommendCode 推荐码.
     * @apiSuccess {Number} presentType 赠送方式.
     * @apiSuccess {Number} managerId 操作人id.
     * @apiSuccess {Number} specialistId 专家id.
     * @apiSuccess {Number} assistantId 助理id.
     * @apiSuccess {String} expireTime 有效期.
     * @apiSuccess {String} useTime 使用时间.
     * @apiSuccess {String} recordTime 记录时间.
     * @apiSuccess {String} createdTime 添加时间.
     * @apiSuccess {String} itemName 项目名.
     * @apiSuccess {String} ticketStatusName 券状态名
     * @apiSuccess {String} presentTypeName 赠送类型名
     * 
     * @apiSuccessExample Success-Response:
     * 	{
            "result": 1,
            "token": "",
            "data": {
                "total": 1,
                "per_page": 20,
                "current_page": 1,
                "last_page": 1,
                "from": 1,
                "to": 1,
                "data": [
                    {
                        "articleCodeId": 1,
                        "presentId": 1,
                        "reservateSn": 123456,
                        "orderSn": 99999,
                        "itemId": 1,
                        "ticketCode": "502",
                        "ticketStatus": 1,
                        "mobilephone": 1802669546,
                        "recommendCode": 5020,
                        "presentType": 1,
                        "managerId": 114,
                        "specialistId": 1,
                        "assistantId": 3,
                        "expireTime": "0000-00-00 00:00:00",
                        "useTime": "0000-00-00",
                        "recordTime": "0000-00-00 00:00:00",
                        "createdTime": "0000-00-00",
                        "itemName": "韩式无痛水光针（赠送）",
                        "ticketStatusName": "已使用",
                        "presentTypeName": "消费赠送"
                    }
                ]
            }
        }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     *               "result": 0,
     *               "code": 0,
     *               "token": "",
     *               "msg" :"必传参数不能为空",
     *           }
     */
    /**
    * 定妆赠送查询
     */
    public function presentList()
    {
        $param = $this->param;
        $mobilephone = isset($param['mobilephone']) ? intval($param['mobilephone']) :'';
        $reservateSn = isset($param['reservateSn']) ? intval($param['reservateSn']) :'';
        $recommendCode = isset($param['recommendCode']) ? intval($param['recommendCode']) :'';
        $ticketCode = isset($param['ticketCode']) ? intval($param['ticketCode']) :'';
        
        $startTime = isset($param['startTime'])? strtotime($param['startTime']):'';
        $endTime = isset($param['endTime'])? strtotime($param['endTime']." 23:59:59"):'';
        
        $presentType = isset($param['presentType']) ? intval($param['presentType']) :'';
        $ticketStatus = isset($param['ticketStatus']) ? intval($param['ticketStatus']) :'';
        
        $page = isset($param['page'])?max($param['page'],1):1;
        $pageSize = isset($param['pageSize'])?$param['pageSize']:20;
        
        $presentListInfo = PresentArticleCode::getPresentList($mobilephone,$reservateSn,$recommendCode,$ticketCode,$startTime,$endTime,$presentType,$ticketStatus,$page,$pageSize);
        foreach ($presentListInfo['data'] as $key => &$val) {
            $val['ticketStatusName'] = self::$ticketCodeStatus[$val['ticketStatus']];
            $val['presentTypeName'] = self::$presentTypeName[$val['presentType']];
            $val['createTime'] = date('Y-m-d',$val['createTime']);
        }
        return $this->success($presentListInfo);
        
    }
    /**
     * @api {post} /PowderArticles/presentListInfo 9.定妆赠送详情
     * 
     * @apiName presentListInfo
     * @apiGroup PowderArticles
     *
     * @apiParam {Number} articleCodeId 必填，活动赠送券记录id
     * 
     * @apiSuccess {Number} presentId 活动id.
     * @apiSuccess {Number} reservateSn 预约订单号.
     * @apiSuccess {Number} itemId 项目id.
     * @apiSuccess {Number} orderSn 系统内部订单号.
     * @apiSuccess {Number} ticketCode 券号.
     * @apiSuccess {Number} ticketStatus 券使用状态. 1:已使用 2:未使用 3: 已过期
     * @apiSuccess {Number} verifyStatus 验证券状态. 1: 开启验证 2: 关闭验证
     * @apiSuccess {Number} mobilephone 手机号.
     * @apiSuccess {String} createTime 创建时间.
     * @apiSuccess {String} recordTime 记录时间.
     * @apiSuccess {String} expireTime 券有效时间.
     * @apiSuccess {String} useTime 使用时间.
     * @apiSuccess {Number} recommendCode 推荐码.
     * @apiSuccess {Number} presentType 赠送类型.
     * @apiSuccess {Number} managerId 记录人id.
     * @apiSuccess {Number} specialistId 专家id.
     * @apiSuccess {Number} assistantId 助理id.
     * @apiSuccess {String} itemName 项目名
     * @apiSuccess {String} articleName 活动名
     * @apiSuccess {String} managerName 负责人
     * @apiSuccess {String} specialistName 专家名
     * @apiSuccess {String} assistantName 助理名
     * @apiSuccess {String} ticketStatusName 券状态名
     * @apiSuccess {String} presentTypeName 赠送类型名
     * 
     * @apiSuccessExample Success-Response:
     * 	{
     *       "result": 1,
     *       "token": "",
     *       "data": {
     *           "articleCodeId": 1,
     *           "presentId": 1,
     *           "reservateSn": 123456,
     *           "orderSn": 99999,
     *           "itemId": 1,
     *           "ticketCode": "502",
     *           "ticketStatus": 1,
     *           "mobilephone": 1802669546,
     *           "recommendCode": 5020,
     *           "presentType": 1,
     *           "managerId": 114,
     *           "specialistId": 1,
     *           "assistantId": 3,
     *           "expireTime": "0000-00-00 00:00:00",
     *           "useTime": "0000-00-00 00:00:00",
     *           "recordTime": "0000-00-00 00:00:00",
     *           "createTime": "1970-01-01",
     *           "itemName": "韩式无痛水光针（赠送）",
     *           "articleName": "test1",
     *           "managerName": "测试用户1",
     *           "specialistName": "XIAOd",
     *           "assistantName": "",
     *           "ticketStatusName": "已使用",
     *           "presentTypeName": "消费赠送"
     *       }
     *   }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     *               "result": 0,
     *               "code": 0,
     *               "token": "",
     *               "msg" :"必传参数不能为空",
     *           }
     */
    /**
    * 定妆赠送详情
     */
    public function presentListInfo()
    {
        $param = $this->param;
        if(empty($param['articleCodeId'])){
            throw new ApiException('必传参数不能为空');    
        }
        $where = array('article_code_id' => $param['articleCodeId']);
        $presentListInfoDetail = PresentArticleCode::getPresentListInfoByWhere($where);
        if(!empty($presentListInfoDetail)){
            $presentListInfoDetail['ticketStatusName'] = self::$ticketCodeStatus[$presentListInfoDetail['ticketStatus']];
            $presentListInfoDetail['presentTypeName'] = self::$presentTypeName[$presentListInfoDetail['presentType']];
            $presentListInfoDetail['createTime'] = date('Y-m-d',$presentListInfoDetail['createTime']);
        }
        return $this->success($presentListInfoDetail);
    }
    /**
     * @api {post} /PowderArticles/usePresentTicket 10.消费券
     * 
     * @apiName usePresentTicket
     * @apiGroup PowderArticles
     *
     * @apiParam {Number} articleCodeId 必填，活动赠送券记录id
     * @apiParam {String} useTime 必填，使用时间 YY-MM-DD
     * @apiParam {Number} specialistId 必填，专家id
     * @apiParam {Number} assistantId 必填，助理id
     * 
     * @apiSuccess {Array} data 空值.
     * 
     * @apiSuccessExample Success-Response:
     * 	{
     *       "result": 1,
     *       "token": "",
     *       "data": []
     *   }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     *               "result": 0,
     *               "code": 0,
     *               "token": "",
     *               "msg" :"必传参数不能为空",
     *           }
     */
    /**
     * 定妆赠送消费使用
     */
    public function usePresentTicket(){
        $param = $this->param;
        $managerId = $this->user->id; //记录人id
        if(empty($managerId)){
            throw new ApiException('无法获取此登陆用户id');  
        }
        if(empty($param['articleCodeId']) || empty($param['useTime']) || empty($param['specialistId']) || empty($param['assistantId'])){
            throw new ApiException('必传参数不能为空');    
        }
        //判断券状态和活动验证状态
        $ticketCanUseRes =  PresentArticleCode::getPresentTicketCanUseStatusByWhere($where);
        //券可用
        if($ticketCanUseRes){
            //记录验证信息
            $res = PresentArticleCode::recordVerifyTicketInfo($managerId,$param['articleCodeId'],$param['useTime'],$param['specialistId'],$param['assistantId']);
            if($res){
                return $this->success();
            }
        }

        
    }
    
}