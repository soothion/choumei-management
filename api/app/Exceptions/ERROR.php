<?php
namespace App\Exceptions;

class ERROR
{
    ////////////系统模块//////////////
    /**
     *@param token无效
     */
     CONST TOKEN_INVILD =  -40000;
     
     /**
     *@param token过期
     */
     CONST TOKEN_EXPIRED =  -40001;

     /**
     *@param 未授权访问
     */
     CONST UNAUTHORIZED =  -40002;
     
     /**
      *@param 参数丢失
      */
     CONST PARAMS_LOST =  -40003;
     
     /**
      *@param 功能关闭
      */
     CONST SERVER_STOPED =  -40004;
     
     /**
      *@param 未知错误
      */
     CONST UNKNOWN_ERROR =  -40005;
     
     /**
      *@param 获取不到配置信息
      */
     CONST CONFIG_LOST =  -40006;

     
    ////////////用户模块//////////////
     /**
     *@param 用户名已存在
     */
     CONST USER_EXIST =  -50001;     

     /**
     *@param 用户创建失败
     */
     CONST USER_CREATE_FAILED =  -50002;     

     /**
     *@param 用户更新失败
     */
     CONST USER_UPDATE_FAILED =  -50003;     

     /**
     *@param 用户密码错误
     */
     CONST USER_PASSWORD_ERROR =  -50004;

     /**
     *@param 用户不存在
     */
     CONST USER_NOT_FOUND =  -50005;



    ////////////返佣单模块//////////////
     /**
     *@param 返佣单创建失败
     */
     CONST REBATE_CREATE_FAILED =  -50100;
     

     /**
     *@param 返佣单更新失败
     */
     CONST REBATE_UPDATE_FAILED =  -50101;     

     /**
     *@param 返佣单确认失败
     */
     CONST REBATE_CONFIRM_FAILED =  -50102;     

     /**
     *@param 返佣单导入失败
     */
     CONST REBATE_UPLOAD_FAILED =  -50103;

     /**
     *@param 返佣单删除失败
     */
     CONST REBATE_DELETE_FAILED =  -50104; 

     /**
     *@param 未知返佣单
     */
     CONST REBATE_NOT_FOUND =  -50105;     

     /**
     *@param 未指定返佣单
     */
     CONST REBATE_NOT_DEFINED =  -50106;






     ////////////权限模块//////////////
     /**
     *@param 权限创建失败
     */
     CONST PERMISSION_CREATE_FAILED =  -50200;

     /**
     *@param 权限更新失败
     */
     CONST PERMISSION_UPDATE_FAILED =  -50201;


     ////////////角色模块//////////////
     /**
     *@param 角色名已存在
     */
     CONST ROLE_EXIST =  -50300;

     /**
     *@param 角色更新失败
     */
     CONST ROLE_CREATE_FAILED =  -50301;     

     /**
     *@param 角色更新失败
     */
     CONST ROLE_UPDATE_FAILED =  -50302;



     ////////////INDEX模块//////////////
     /**
     *@param 验证码错误
     */
     CONST CAPTCHA_ERROR =  -50400;

     /**
     *@param 当前帐户已停用或注销
     */
     CONST ACCOUNT_INVALID =  -50401;     

     /**
     *@param 用户名或密码错误
     */
     CONST LOGIN_FAILED =  -50402;     

     /**
     *@param 用户名或密码错误
     */
     CONST LOGOUT_FAILED =  -50403;


     ////////////佣金单模块//////////////
     /**
     *@param 未知佣金单
     */
     CONST COMMISSION_NOT_FOUND =  -50500;



     ////////////上传模块//////////////
     /**
     *@param 文件为空
     */
     CONST FILE_EMPTY =  -50507;

     /**
     *@param 文件格式错误
     */
     CONST FILE_FORMAT_ERROR =  -50508;
     
     ////////////交易管理相关///////////////
     
     /**
      * 订单不存在
      * @var unknown
      */
     CONST ORDER_NOT_EXIST = -50601;
     
     /**
      * 臭美券不存在
      * @var unknown
      */
     CONST TICKET_NOT_EXIST = -50602;
     
     /**
      * 退款单不存在
      * @var unknown
      */
     CONST REFUND_NOT_EXIST = -50603;
     
     /**
      * 订单项目不存在
      * @var unknown
      */
     CONST ORDER_ITEM_NOT_EXIST = -50604;
     
     /**
      * 退款单状态不正常
      * @var unknown
      */
     CONST REFUND_STATE_WRONG = -50605;
     
     /**
      * 退款单找不到支付流水信息
      * @var unknown
      */
     CONST REFUND_FLOW_LOST= -50606;
     
     /**
      * 退款单找不到臭美券信息
      * @var unknown
      */
     CONST REFUND_TICKET_LOST= -50607;
     
     /**
      * 退款单找不到臭美券信息
      * @var unknown
      */
     CONST REFUND_TICKET_STATE_WRONG= -50608;
     
     /**
      * 退款单找不到paymentlog信息
      * @var unknown
      */
     CONST REFUND_PAYMENT_LOG_LOST = -50609;
     
     /**
      * 退款关键信息不全
      * @var unknown
      */
     CONST REFUND_LOST_PRIMARY_INFO = -50610;
     
     /**
      * 订单状态不正确
      * @var unknown
      */
     CONST ORDER_STATUS_WRONG = -50610;
     
     /**
      * 赏金单状态不正确
      * @var unknown
      */
     CONST BOUNTY_STATUS_WRONG = -50611;
     
     /**
      * 退款单找不大流水号
      * @var unknown
      */
     CONST REFUND_CANT_FIND_TN = -50612;
	 
	 
	 ////////////消息模块//////////////
     /**
     *@param 参数错误
     */
     CONST PARAMETER_ERROR =  -50900;
	 /**
     *@param 更新失败
     */
     CONST UPDATE_FAILED =  -50901;
	 /**
     *@param 消息ID不正确
     */
     CONST MESSAGE_ID_IS_ERROR  =  -50902;

     
     
	 ////////////图片风格模块//////////////
     /**
     *@param 插入风格失败
     */
     CONST STYLE_CREATE_FAILED =  -51200;
	 /**
     *@param 更新风格失败
     */
     CONST STYLE_UPDATE_FAILED =  -51201;
      /**
     *@param 删除风格失败
     */
     CONST STYLE_DELETE_FAILED  =  -51202;	 

     /**
     *@param 未知图片
     */
     CONST STYLE_NOT_FOUND  =  -51203;


     ////////////用户等级模块//////////////
     /**
     *@param 等级设置为空
     */
     CONST LEVEL_EMPTY =  -50600;

    /**
     *@param 等级更新失败
     */
     CONST LEVEL_UPDATE_FAILED =  -50601;

    /**
     *@param 未知等级
     */
     CONST LEVEL_NOT_FOUND =  -50602;



     ////////////反馈模块//////////////
     /**
     *@param 未知反馈
     */
     CONST FEED_NOT_FOUND =  -50700;

     /**
     *@param 反馈删除失败
     */
     CONST FEED_DELETE_FAILED =  -50701;

     /**
     *@param 反馈为空
     */
     CONST FEED_EMPTY =  -50702;
     


}