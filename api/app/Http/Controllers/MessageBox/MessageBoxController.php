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
     * @api {post} /messageBox/messssageList 1.全量消息列表
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
     * @apiSuccess {Number} ID 消息id.
     * @apiSuccess {String} RECEIVE_TYPE 消息类型.
     * @apiSuccess {String} COMPANY_CODE 集团码.
     * @apiSuccess {String} ACTIVITY_CODE 活动码.
     * @apiSuccess {String} SHOP_CODE 店铺邀请码.
     * @apiSuccess {String} TITLE 消息标题.
     * @apiSuccess {String} CONTENT 消息内容.
     * @apiSuccess {String} LINK 链接.
     * @apiSuccess {String} DETAIL 富文本信息内容.
     * @apiSuccess {String} IS_PUSH 推送状态.
     * @apiSuccess {Number} READ_NUM 阅读数.
     * @apiSuccess {String} STATUS 消息配置状态.
     * @apiSuccess {String} CREATE_TIME 创建时间.
     * @apiSuccess {String} UPDATE_TIME 更新时间.
     * @apiSuccess {String} RECEIVE_TYPE_NAME 接收类型名称.
     * @apiSuccess {String} IS_PUSH_NAME 推送状态名称.
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
     *                   "ID": 27,
     *                   "RECEIVE_TYPE": "CODE",
     *                   "COMPANY_CODE": "1867",
     *                   "ACTIVITY_CODE": "3333",
     *                   "SHOP_CODE": "",
     *                   "TITLE": "1111",
     *                   "CONTENT": "11111",
     *                   "SEND_TIME": "2015-08-29 04:07:45",
     *                   "LINK": "http://www.baidu.com",
     *                   "DETAIL": "",
     *                   "IS_PUSH": "Y",
     *                   "READ_NUM": 0,
     *                   "STATUS": "DONE",
     *                   "CREATE_TIME": "2015-08-29 02:10:26",
     *                   "UPDATE_TIME": "2015-08-29 04:07:24",
     *                   "RECEIVE_TYPE_NAME": "指定特征用户",
     *                   "IS_PUSH_NAME": "已发送"
     *               },
     *               {
     *                   "ID": 26,
     *                   "RECEIVE_TYPE": "CODE",
     *                   "COMPANY_CODE": "0202",
     *                   "ACTIVITY_CODE": "",
     *                   "SHOP_CODE": "",
     *                   "TITLE": "ceshiceshi",
     *                   "CONTENT": "ceshiceshi",
     *                   "SEND_TIME": "2015-08-29 04:04:25",
     *                   "LINK": "http://newyingxiao.choumei.cn/sysNews/redirectUrl/id/26",
     *                   "DETAIL": "<p>12222222222222233333333333333333333</p>",
     *                   "IS_PUSH": "Y",
     *                   "READ_NUM": 0,
     *                   "STATUS": "DONE",
     *                   "CREATE_TIME": "2015-08-29 02:05:14",
     *                   "UPDATE_TIME": "2015-08-29 04:04:02",
     *                   "RECEIVE_TYPE_NAME": "指定特征用户",
     *                   "IS_PUSH_NAME": "已发送"
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
            $val['RECEIVE_TYPE_NAME'] = self::$RECEIVE_TYPE_NAME[$val['RECEIVE_TYPE']];
            $val['IS_PUSH_NAME'] = self::$IS_PUSH_NAME[$val['IS_PUSH']];
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
     * @apiSuccess {Number} ID 消息id.
     * @apiSuccess {String} RECEIVE_TYPE 消息类型.
     * @apiSuccess {String} COMPANY_CODE 集团码.
     * @apiSuccess {String} ACTIVITY_CODE 活动码.
     * @apiSuccess {String} SHOP_CODE 店铺邀请码.
     * @apiSuccess {String} TITLE 消息标题.
     * @apiSuccess {String} CONTENT 消息内容.
     * @apiSuccess {String} LINK 链接.
     * @apiSuccess {String} DETAIL 富文本信息内容.
     * @apiSuccess {String} IS_PUSH 推送状态.
     * @apiSuccess {Number} READ_NUM 阅读数.
     * @apiSuccess {String} STATUS 消息配置状态.
     * @apiSuccess {String} CREATE_TIME 创建时间.
     * @apiSuccess {String} UPDATE_TIME 更新时间.
     * @apiSuccess {Array} COMPANY_CODE_ARR 集团码的值.
     * @apiSuccess {Array} ACTIVITY_CODE_ARR 活动码的值.
     * @apiSuccess {Array} SHOP_CODE_ARR 店铺码的值.
     * @apiSuccess {Array} salonInfo 店铺信息.
     * @apiSuccess {String} recommend_code 店铺推荐码.
     * @apiSuccess {String} salonname 店铺名.
     * 
     * 
     * @apiSuccessExample Success-Response:
     * 	{
            "result": 1,
            "token": "",
            "data": {
                "ID": 10,
                "RECEIVE_TYPE": "CODE",
                "COMPANY_CODE": "0202,1867",
                "ACTIVITY_CODE": "0626,3333",
                "SHOP_CODE": "9132,5539,0368,8380,1829,2156,9211,9895",
                "TITLE": "test-0003",
                "CONTENT": "test-0003",
                "SEND_TIME": "2015-08-25 16:57:10",
                "LINK": "http://newyingxiao.choumei.lu/sysNews/redirectUrl/id/10",
                "DETAIL": "",
                "IS_PUSH": "Y",
                "READ_NUM": 14,
                "STATUS": "DONE",
                "CREATE_TIME": "2015-08-22 13:08:18",
                "UPDATE_TIME": "2015-08-27 22:30:46",
                "COMPANY_CODE_ARR": [
                    "0202",
                    "1867"
                ],
                "ACTIVITY_CODE_ARR": [
                    "0626",
                    "3333"
                ],
                "SHOP_CODE_ARR": [
                    "9132",
                    "5539",
                    "0368",
                    "8380",
                    "1829",
                    "2156",
                    "9211",
                    "9895"
                ],
                "salonInfo": [
                    {
                        "recommend_code": "0368",
                        "salonname": "自作主张"
                    },
                    {
                        "recommend_code": "1829",
                        "salonname": "名人汇"
                    },
                    {
                        "recommend_code": "2156",
                        "salonname": "魅力美发沙龙"
                    },
                    {
                        "recommend_code": "5539",
                        "salonname": "千尚（沙浦头）"
                    },
                    {
                        "recommend_code": "8380",
                        "salonname": "波尔发艺名店"
                    },
                    {
                        "recommend_code": "9132",
                        "salonname": "名流造型SPA（皇岗店）"
                    },
                    {
                        "recommend_code": "9211",
                        "salonname": "雅锜护肤造型SPA会所（福田店）"
                    },
                    {
                        "recommend_code": "9895",
                        "salonname": "名流造型SPA（合正店）"
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
     *添加店铺
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
        
        $field = array('salon.salonid','salon.salonname','dividend.recommend_code');
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
    //获取所有区域
    public function getAllTown(){
        $field = array('tid','tname');
        $townInfo = Town::where('iid','=',1)->select($field)->get()->toArray();
        return $this->success($townInfo);
    }
    
    
    //获取集团码
    public function getCompanyCode(){
        
        $field = array('code','companyAcronym');
        $companyCodeInfo = CompanyCode::getCompanyCodeInfo($field);     
        return $this->success($companyCodeInfo);      
        
    }
    
    // 获取活动码
    public function getActivityCode(){
        $field = array('dividend.recommend_code','dividend.event_conf_id','event_conf.conf_title');
        $where = array('dividend.status' => 0,'dividend.activity' => 1);
        
        $dividendInfo = Dividend::select($field)
        ->leftjoin('event_conf', function ($join) {
            $join->on('event_conf.conf_id', '=', 'dividend.event_conf_id');
        })->where($where)->get()->toArray(); 
        
        return $this->success($dividendInfo);
    }
}
?>