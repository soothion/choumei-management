<?php namespace App\Http\Controllers\MessageBox;

use App\Http\Controllers\Controller;
use DB;

use App\CompanyCode;
use App\Dividend;
use App\Model\EventConf;
use App\Town;
use App\Model\PushConf;


use App\Exceptions\ApiException;
use App\Exceptions\ERROR;

class MessageBoxController extends Controller{
    
    private static $RECEIVE_TYPE_NAME = array('REG' => '所有注册用户','APP' => 'app安装用户','CODE' => '指定特征用户','APPNOTREG' => '安装app未注册用户');
    private static $IS_PUSH_NAME = array('Y' => '已发送','N' => '未发送');
    
   /**
     * @api {post} /messageBox/messageList 1.全量消息列表
     * 
     * @apiName messssageList
     * @apiGroup MessageBox
     *
     * @apiParam {String} messageTitle 可选,消息标题
     * @apiParam {Number} page 可选,页数.（默认为1）
     * @apiParam {Number} page_size 可选,分页大小（默认为20）
     * @apiParam {String} startTime 可选,YY-mm-dd
     * @apiParam {String} endTime 可选,YY-mm-dd
     *
     * @apiSuccess {Number} total 总数据量.
     * @apiSuccess {Number} per_page 分页大小.
     * @apiSuccess {Number} current_page 当前页面.
     * @apiSuccess {Number} last_page 当前页面.
     * @apiSuccess {Number} from 起始数据.
     * @apiSuccess {Number} to 结束数据.
    * 
     * @apiSuccess {Number} id 消息id.
     * @apiSuccess {String} receiveType 消息类型.
     * @apiSuccess {String} companyCode 集团码.
     * @apiSuccess {String} activityCode 活动码.
     * @apiSuccess {String} shopCode 店铺邀请码.
     * @apiSuccess {String} title 消息标题.
     * @apiSuccess {String} comment 消息内容.
     * @apiSuccess {String} link 链接.
     * @apiSuccess {String} detail 富文本信息内容.
     * @apiSuccess {String} isPush 推送状态.
     * @apiSuccess {Number} readNum 阅读数.
     * @apiSuccess {String} status 消息配置状态.
     * @apiSuccess {String} creatTime 创建时间.
     * @apiSuccess {String} updateTIme 更新时间.
     * @apiSuccess {String} receiveTypeName 接收类型名称.
     * @apiSuccess {String} isPushName 推送状态名称.
     * 
     * 
     * @apiSuccessExample Success-Response:
     * 	{
     *       "result": 1,
     *       "token": "",
     *       "data": {
     *           "total": 26,
     *           "per_page": 20,
     *           "current_page": 1,
     *           "last_page": 2,
     *           "from": 1,
     *           "to": 20,
     *           "data": [
     *               {
     *                   "id": 28,
     *                   "receiveType": "APP",
     *                   "companyCode": "",
     *                   "activityCode": "",
     *                   "shopCode": "",
     *                   "title": "test-1111",
     *                   "comment": "test-1111",
     *                   "sendTime": "2016-06-04 16:32:15",
     *                   "link": "http://newyingxiao.choumei.cn/sysNewsRedirectUrl/redirectUrl/id/28",
     *                   "detail": "<p>sdfasfdasdfasfasfdasf</p><p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; sdfsdfsfs</p><p>&nbsp; &nbsp;的收费的方式对方</p>",
     *                   "isPush": "Y",
     *                   "readNum": 0,
     *                   "status": "NOM",
     *                   "createTime": "2015-08-31 14:34:05",
     *                   "updateTime": "2015-09-06 10:34:58",
     *                   "receiveTypeName": "app安装用户",
     *                   "isPushName": "已发送"
     *               }
     *           ]
     *        }
     *   }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
                    "result": 0,
                    "code": 0,
                    "token": "",
                }
     */
    /***
     * 全量消息列表
     */
    public function messssageList() {
        $param = $this->param;
        $title = isset($param['messageTitle']) ? trim($param['messageTitle']) :'';
        $startTime = isset($param['startTime'])? $param['startTime']:'';
        $endTime = isset($param['endTime'])? $param['endTime']." 23:59:59":'';
        
        $page = isset($param['page'])?max($param['page'],1):1;
        $pageSize = isset($param['pageSize'])?$param['pageSize']:20;
        $status = 'DEL';
        $res = PushConf::getMessageBoxInfo($title,$status,$startTime,$endTime, $page, $pageSize);
        foreach ($res['data'] as $key => &$val) {
            $val['receiveTypeName'] = self::$RECEIVE_TYPE_NAME[$val['receiveType']];
            $val['isPushName'] = self::$IS_PUSH_NAME[$val['isPush']];
        }
        return $this->success($res);
    }
    
     /**
     * @api {post} /messageBox/editMessage 2.编辑消息
     * 
     * @apiName editMessage
     * @apiGroup MessageBox
     *
     * @apiParam {Number} pushId 必填,消息ID
     * @apiParam {String} newsTitle 必填，消息标题
     * @apiParam {String} newsContent 必填, 消息内容
     * @apiParam {String} newsTime 必填, 发送时间
     * @apiParam {String} newsPush 必填, 是否推送 Y / N
     * @apiParam {String} newsLink 可选, 链接
     * @apiParam {String} newsDetail 可选, 富文本信息内容
     * 
     * @apiSuccess {Array} data 空值.
     * 
     * 
     * @apiSuccessExample Success-Response:
     * 	{
     *       "result": 1,
     *       "token": "",
     *       "data": [],
     *        
     *   }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     *               "result": 0,
     *               "code": 0,
     *               "token": "",
     *             "msg" :"必传参数不能为空",
     *           }
     */
    /**
     * 编辑消息
     */
    public function editMessage(){
        
        $param = $this->param;
        if(empty($param['pushId']) || empty($param['newsTitle']) || empty($param['newsContent']) || empty($param['newsTime'] || empty($param['newsPush']))){
            throw new ApiException('必传参数不能为空');
        }
        if(!($param['newsLink'] || $param['newsDetail'])){
            throw new ApiException('链接必传参数不能为空');
        }
        $data['TITLE'] = $param['newsTitle'];
        $data['CONTENT'] = $param['newsContent'];
        $data['SEND_TIME'] = $param['newsTime'];
        $data['LINK'] = empty($param['newsLink']) ? '' : $param['newsLink'];
        $data['DETAIL'] = urldecode($param['newsDetail']);
        $data['IS_PUSH'] = $param['newsPush'];
        $data['CREATE_TIME'] =  date('Y-m-d H:i:s');
        $res = PushConf::where('Id','=',$param['pushId'])->update($data);
        if($res === false){
            throw new ApiException('更新失败',ERROR::MessageBox_UPDATE_FAILED);
        }else{
            return $this->success();
        }
        
    }
    /**
     * @api {post} /messageBox/showMessage 3.查看消息
     * 
     * @apiName showMessage
     * @apiGroup MessageBox
     *
     * @apiParam {Number} pushId 必填,消息ID
    * 
     * @apiSuccess {Number} id 消息id.
     * @apiSuccess {String} receiveType 消息类型.
     * @apiSuccess {String} companyCode 集团码.
     * @apiSuccess {String} activityCode 活动码.
     * @apiSuccess {String} shopCode 店铺邀请码.
     * @apiSuccess {String} title 消息标题.
     * @apiSuccess {String} comment 消息内容.
     * @apiSuccess {String} link 链接.
     * @apiSuccess {String} detail 富文本信息内容.
     * @apiSuccess {String} isPush 推送状态.
     * @apiSuccess {Number} readNum 阅读数.
     * @apiSuccess {String} status 消息配置状态.
     * @apiSuccess {String} createTime 创建时间.
     * @apiSuccess {String} updateTime 更新时间.
     * @apiSuccess {Array} companyCodeArr 集团码的值.
     * @apiSuccess {Array} activityCodeArr 活动码的值.
     * @apiSuccess {Array} shopCodeArr 店铺码的值.
     * @apiSuccess {Array} salonInfo 店铺信息.
     * @apiSuccess {String} recommendCode 店铺推荐码.
     * @apiSuccess {String} salonName 店铺名.
     * 
     * 
     * @apiSuccessExample Success-Response:
     * 	{
     *       "result": 1,
     *       "token": "",
     *       "data": {
     *           "id": 10,
     *           "receiveType": "CODE",
     *           "companyCode": "0202,1867",
     *           "activityCode": "0626,3333",
     *           "shopCode": "9132,5539,0368,8380,1829,2156,9211,9895",
     *           "title": "test-0003",
     *           "comment": "test-0003",
     *           "sendTime": "2015-08-25 16:57:10",
     *           "link": "http://newyingxiao.choumei.lu/sysNews/redirectUrl/id/10",
     *           "detail": "",
     *           "isPush": "Y",
     *           "readNum": 14,
     *           "status": "DONE",
     *           "createTime": "2015-08-22 13:08:18",
     *           "updateTime": "2015-08-27 22:30:46",
     *           "companyCodeArr": [
     *               "0202",
     *               "1867"
     *           ],
     *           "activityCodeArr": [
     *               "0626",
     *               "3333"
     *           ],
     *           "shopCodeArr": [
     *               "9132",
     *               "5539",
     *               "0368",
     *               "8380",
     *               "1829",
     *               "2156",
     *               "9211",
     *               "9895"
     *           ],
     *           "salonInfo": [
     *               {
     *                   "recommendCode": "0368",
     *                   "salonName": "自作主张"
     *               },
     *               {
     *                   "recommendCode": "1829",
     *                   "salonName": "名人汇"
     *               },
     *               {
     *                   "recommendCode": "2156",
     *                   "salonName": "魅力美发沙龙"
     *               },
     *               {
     *                   "recommendCode": "5539",
     *                   "salonName": "千尚（沙浦头）"
     *               },
     *               {
     *                   "recommendCode": "8380",
     *                   "salonName": "波尔发艺名店"
     *               },
     *               {
     *                   "recommendCode": "9132",
     *                   "salonName": "名流造型SPA（皇岗店）"
     *               },
     *               {
     *                   "recommendCode": "9211",
     *                   "salonName": "雅锜护肤造型SPA会所（福田店）"
     *               },
     *               {
     *                   "recommendCode": "9895",
     *                   "salonName": "名流造型SPA（合正店）"
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
     *               "msg": "必传参数pushId不能为空",
     *           }
     */
    /**
     * 查看消息
     * @return type
     * @throws ApiException
     */
    public function showMessage(){
        $param = $this->param;
        if(empty($param['pushId'])){
            throw new ApiException('必传参数pushId不能为空');
        }
        $getMessageBoxInfo = PushConf::showMessageBoxInfoById($param['pushId']);
        if(empty($getMessageBoxInfo)){
            throw new ApiException('pushId不存在');
        }
        return $this->success($getMessageBoxInfo);
        
    }
    
    
     /**
     * @api {post} /messageBox/delMesssage 4. 删除消息
     * 
     * @apiName delMesssage
     * @apiGroup MessageBox
     *
     * @apiParam {Number} pushId 必填，消息推送ID
     *
     * @apiSuccess {Array} data 空值为成功.
     * 
     * 
     * @apiSuccessExample Success-Response:
     * 	{
     *       "result": 1,
     *       "token": "",
     *       "data": {
     *           "data": []
     *        }
     *   }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     *               "result": 0,
     *               "code": 0,
     *               "msg": "消息状态为已删除，无需再次删除",
     *               "token": ""
     *           }
     */
    /**
     * 删除消息
     */
    public function delMesssage() {
        $param = $this->param;
        if(empty($param['pushId'])){
            throw new ApiException('必传参数pushId不能为空');
        }
        $getMessageBoxInfo = PushConf::getMessageBoxInfoByID($param['pushId']);
        if(empty($getMessageBoxInfo)){
            throw new ApiException('pushId不存在');
        }else if($getMessageBoxInfo['STATUS'] == 'DEL'){
            throw new ApiException('消息状态为已删除，无需再次删除');
        }
        $updateRes = PushConf::where('ID','=',$param['pushId'])->update(array('STATUS' => 'DEL'));
        if($updateRes === false){
           throw new ApiException('删除消息失败',ERROR::MessageBox_UPDATE_FAILED); 
        }else{
           return  $this->success();
        }
    }
    
    /**
     * @api {post} /messageBox/addPushConf 5.新增消息
     * 
     * @apiName addPushConf
     * @apiGroup MessageBox
     *
     * @apiParam {String} receiveType 必填，接收消息类型  REG-所有注册用户 APP-app安装用户  CODE-指定特征用户 APPNOTREG安装app未注册用户'
     * @apiParam {String} newsTitle 必填，消息标题
     * @apiParam {String} newsContent 必填, 消息内容
     * @apiParam {String} newsTime 必填, 发送时间
     * @apiParam {String} newsPush 必填, 是否推送 Y / N
     * @apiParam {String} newsLink 可选, 链接
     * @apiParam {String} newsDetail 可选, 富文本信息内容
     * 
     * @apiSuccess {Array} data 空值.
     * 
     * 
     * @apiSuccessExample Success-Response:
     * 	{
     *       "result": 1,
     *       "token": "",
     *       "data": [],
     *        
     *   }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     *               "result": 0,
     *               "code": 0,
     *               "token": "",
     *             "msg" :"必传参数不能为空",
     *           }
     */
    /**
     * 新增消息
     */
	
    public function addPushConf(){
        $param = $this->param;
        if(empty($param['receiveType']) || empty($param['newsTitle']) || empty($param['newsContent']) || empty($param['newsTime'])){
            throw new ApiException('必传参数不能为空');
        }
        if(!($param['newsLink'] || $param['newsDetail'])){
            throw new ApiException('链接必传参数不能为空');
        }
        $receiveTypeArray = array('REG','APP','CODE','APPNOTREG');
        if(!in_array(trim($param['receiveType']),$receiveTypeArray)){
            throw new ApiException('参数错误--receiveType',ERROR::MessageBox_PARAMETER_ERROR);
        }
        if(strtotime($param['newsTime']) - time() < 30* 60){
            throw new ApiException('发送时间必须晚于提交时间30分钟之后',ERROR::MessageBox_PARAMETER_ERROR);
        }        
        $data['RECEIVE_TYPE'] = trim($param['receiveType']);
        
        $data['COMPANY_CODE'] = empty($param['companyCode']) ? '' : $param['companyCode'];
        $data['ACTIVITY_CODE'] = empty($param['activityCode']) ? '' : $param['activityCode'];
        $data['SHOP_CODE'] = empty($param['recommendCode']) ? '' : $param['recommendCode'];
        $data['TITLE'] = $param['newsTitle'];
        $data['CONTENT'] = $param['newsContent'];
        $data['SEND_TIME'] = $param['newsTime'];
        $data['LINK'] = empty($param['newsLink']) ? '' : $param['newsLink'];
        $data['DETAIL'] = urldecode($param['newsDetail']);
        $data['IS_PUSH'] = $param['newsPush'];
        $data['STATUS'] = 'NOM';
        $data['CREATE_TIME'] =  date('Y-m-d H:i:s');
        //DB::enableQueryLog();
        $res = PushConf::insert($data);
        //$queries = DB::getQueryLog();
        if($res){
            return $this->success();
        }else{
            throw new ApiException('消息添加失败',ERROR::MessageBox_ADD_FAILED);
        }
        //   receiveType=TTT&newsTitle=TTT&newsContent=TTT&newsDetail=TTT
    }
    
    /**
     * @api {post} /messageBox/addSalon 6.添加查找店铺
     * 
     * @apiName addSalon
     * @apiGroup MessageBox
     *
     * @apiParam {Number} district 选填，区域号
     * @apiParam {String} salonName 选填，店铺名
     * @apiParam {Number} recommendCode 选填，推荐码
     * 
     * @apiSuccess {Number} salonId 店铺id.
     * @apiSuccess {String} salonName 店铺名.
     * @apiSuccess {Number} recommendCode 店铺推荐码.
     * 
     * 
     * @apiSuccessExample Success-Response:
     * 	{
     *       "result": 1,
     *       "token": "",
     *       "data": [
     *           {
     *               "salonId": 1,
     *               "salonName": "嘉美专业烫染",
     *               "recommendCode": "8280"
     *           },
     *           {
     *               "salonId": 2,
     *               "salonName": "名流造型SPA（皇岗店）",
     *               "recommendCode": "9132"
     *           }
     *  }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     *               "result": 0,
     *               "code": 0,
     *               "token": "",
     *           }
     */
    /**
     *添加查找店铺
     */
    public function addSalon(){
        $param = $this->param;
        $district = isset($param["district"])?intval($param["district"]):0;
        $salonname = isset($param["salonName"])?trim($param["salonName"]):"";
        $recommendCode = isset($param["recommendCode"])?intval($param["recommendCode"]):0;
        if($district){
            $where["salon.district"] = $district;
        }        
        if($recommendCode){
            $where["recommendCode"] = $recommendCode;
        }
        
        $where['salon.status'] = 1;
        $where['salon.salestatus'] = 1;
        $where['dividend.activity'] = 2;
        $where['dividend.status'] = 0;
        
        $field = array('salon.salonid as salonId','salon.salonname as salonName','dividend.recommend_code as recommendCode');
        //DB::enableQueryLog();
        $query = Dividend::select($field)
        ->join('salon', function ($join) {
            $join->on('salon.salonid', '=', 'dividend.salon_id');
        })->where($where);        
        if($salonname){
            $recommendSalonInfo = $query->where('salon.salonname', 'like',"%".$salonname."%")->get()->toArray();
        }else{
            $recommendSalonInfo = $query->get()->toArray();
        }
        //$queries = DB::getQueryLog();
        return $this->success($recommendSalonInfo);           
        
    }
     /**
     * @api {post} /messageBox/getAllTown 7.获取所有区域
     * 
     * @apiName getAllTown
     * @apiGroup MessageBox
     * 
     * @apiSuccess {Number} townId 区id.
     * @apiSuccess {String} townName 区名.
     * 
     * @apiSuccessExample Success-Response:
     * 	{
     *       "result": 1,
     *       "token": "",
     *       "data": [
     *           {
     *               "townId": 1,
     *               "townName": "福田区"
     *           },
     *           {
     *               "townId": 2,
     *               "townName": "罗湖区"
     *           },
     *           {
     *               "townId": 3,
     *               "townName": "南山区"
     *           },
     *           {
     *               "townId": 4,
     *               "townName": "宝安区"
     *           },
     *           {
     *               "townId": 5,
     *               "townName": "龙岗区"
     *           },
     *           {
     *               "townId": 6,
     *               "townName": "盐田区"
     *           }
     *       ]
     *   }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     *               "result": 0,
     *               "code": 0,
     *               "token": "",
     *           }
     */
    //获取所有区域
    public function getAllTown(){
        $field = array('tid as townId','tname as townName');
        $townInfo = Town::where('iid','=',1)->select($field)->get()->toArray();
        return $this->success($townInfo);
    }
     /**
     * @api {post} /messageBox/getCompanyCode 8.获取所有集团码
     * 
     * @apiName getCompanyCode
     * @apiGroup MessageBox
     * 
     * @apiSuccess {Number} code 集团码.
     * @apiSuccess {String} companyAcronym 集团名.
     * 
     * @apiSuccessExample Success-Response:
     * 	{
     *       "result": 1,
     *       "token": "",
     *       "data": [
     *           {
     *               "code": "0202",
     *               "companyAcronym": "臭美美发"
     *           },
     *           {
     *               "code": "4848",
     *               "companyAcronym": "臭臭"
     *           },
     *           {
     *               "code": "6990",
     *               "companyAcronym": "南航"
     *           },
     *   }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     *               "result": 0,
     *               "code": 0,
     *               "token": "",
     *           }
     */
    //获取集团码
    public function getCompanyCode(){
        
        $field = array('code','companyAcronym');
        $companyCodeInfo = CompanyCode::getCompanyCodeInfo($field);     
        return $this->success($companyCodeInfo);             
    }
     /**
     * @api {post} /messageBox/getCompanyCode 8.获取所有集团码
     * 
     * @apiName getCompanyCode
     * @apiGroup MessageBox
     * 
     * @apiSuccess {Number} recommendCode 推荐码.
     * @apiSuccess {Number} eventConfId 活动配置id.
     * @apiSuccess {String} confTitle 活动名.
     * 
     * @apiSuccessExample Success-Response:
     * 	{
     *       "result": 1,
     *       "token": "",
     *       "data": [
     *           {
     *               "recommendCode": "0626",
     *               "eventConfId": 5,
     *               "confTitle": "送西瓜活动"
     *           },
     *           {
     *               "recommendCode": "5917",
     *               "eventConfId": 29,
     *               "confTitle": "快餐厅活动"
     *           }
     *   }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     *               "result": 0,
     *               "code": 0,
     *               "token": "",
     *           }
     */
    // 获取活动码
    public function getActivityCode(){
        $field = array('dividend.recommend_code as recommendCode','dividend.event_conf_id as eventConfId','event_conf.conf_title as confTitle');
        $where = array('dividend.status' => 0,'dividend.activity' => 1);
        
        $dividendInfo = Dividend::select($field)
        ->leftjoin('event_conf', function ($join) {
            $join->on('event_conf.conf_id', '=', 'dividend.event_conf_id');
        })->where($where)->get()->toArray(); 
        
        return $this->success($dividendInfo);
    }
}
?>