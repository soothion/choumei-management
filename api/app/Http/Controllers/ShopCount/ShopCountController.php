<?php
/**
 * 店铺结算相关
 * @author nian.zhu
 */
namespace App\Http\Controllers\ShopCount;


use App\Http\Controllers\Controller;
use App\ShopCountApi;
use App\ShopCount;
use Event;
use App\PrepayBill;

class ShopCountController extends Controller
{
    /**
     * @api {get} /shop_count/index 1.转付单列表
     * @apiName index
     * @apiGroup ShopCount
     *
     * @apiParam {Number} key  1 店铺搜索 2 商户搜索 3 店铺编号
     * @apiParam {String} keyword  根据key来的关键字
     * @apiParam {String} pay_time_min 付款最小时间 YYYY-MM-DD
     * @apiParam {String} pay_time_max 付款最大时间 YYYY-MM-DD
     * @apiParam {Number} page 可选,页数. (从1开始)
     * @apiParam {Number} page_size 可选,分页大小.(最小1 最大500,默认20)
     * @apiParam {String} sort_key 排序的键 ['id','created_at'(创建时间,默认),'code'(付款单号),'type'(付款类型),'pay_money'(付款金额),'cost_money'(换算消费额),'day'(付款日期)]
     * @apiParam {String} sort_type 排序的方式 ASC正序 DESC倒叙 (默认)
     *
     * @apiSuccess {Number} total 总数据量.
     * @apiSuccess {Number} per_page 分页大小.
     * @apiSuccess {Number} current_page 当前页面.
     * @apiSuccess {Number} last_page 当前页面.
     * @apiSuccess {Number} from 起始数据.
     * @apiSuccess {Number} to 结束数据.
     * @apiSuccess {String} code 付款单号.
     * @apiSuccess {Number} type 付款单类型 1:付交易代收款  2:付交易代收款 3:交易代收款返还
     * @apiSuccess {Number} state 状态 1:已付款 0:预览状态
     * @apiSuccess {String} pay_money 付款金额.
     * @apiSuccess {String} cost_money 换算消费额.
     * @apiSuccess {String} created_at 创建时间.
     * @apiSuccess {String} day  付款日期.
     * @apiSuccess {Object} user  制表人信息.
     * @apiSuccess {Object} salon 店铺信息.
     * @apiSuccess {Object} merchant 商盟信息.
     *
     * @apiSuccessExample Success-Response:
     *       {
     *          "result": 1,
     *          "data": {
     *              "total": 1,
     *              "per_page": 10,
     *              "current_page": 1,
     *              "last_page": 1,
     *              "from": 1,
     *              "to": 1,
     *              "data": [
     *                  {
     *                     "id": 1,
     *                     "created_at": "2015-07-03 00:00:00",
     *                     "merchant_id": 1,
     *                     "salon_id": 1,
     *                     "code": "fasdfasdfasdfasdfadfa",
     *                     "type": 1,
     *                     "uid": 1,
     *                     "pay_money": "2000.00",
     *                     "cost_money": "2500.00",
     *                     "day": "2015-07-02",
     *                     "user": {
     *                         "id": 1,
     *                         "name": ""
     *                     },
     *                     "salon": {
     *                         "salonid": 1,
     *                         "salonname": "嘉美专业烫染"
     *                     },
     *                     "merchant": {
     *                         "id": 1,
     *                         "name": "速度发多少"
     *                     }
     *                  }
     *              ]
     *          }
     *      }
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function index()
    {
        $param = $this->parameters([
            'key'=>self::T_INT,
            'keyword'=>self::T_STRING,
            'pay_time_min'=>self::T_STRING,
            'pay_time_min'=>self::T_STRING,
            'pay_time_max'=>self::T_STRING,
            'page'=>self::T_INT,
            'page_size'=>self::T_INT,
            'sort_key'=>self::T_STRING,
            'sort_type'=>self::T_STRING,
        ]);
        
        $items = ShopCountApi::searchPrepay($param);
        return $this->success($items);
    }
    
    /**
     * @api {get} /shop_count/export 11.导出转付单
     * @apiName export
     * @apiGroup ShopCount
     *
     * @apiParam {Number} key  1 店铺搜索 2 商户搜索 3 店铺编号
     * @apiParam {String} keyword  根据key来的关键字
     * @apiParam {String} pay_time_min 付款最小时间 YYYY-MM-DD
     * @apiParam {String} pay_time_max 付款最大时间 YYYY-MM-DD
     * @apiParam {Number} page 可选,页数. (从1开始)
     * @apiParam {Number} page_size 可选,分页大小.(最小1 最大500,默认20)
     * @apiParam {String} sort_key 排序的键 ['id','created_at'(创建时间,默认),'code'(付款单号),'type'(付款类型),'pay_money'(付款金额),'cost_money'(换算消费额),'day'(付款日期)]
     * @apiParam {String} sort_type 排序的方式 ASC正序 DESC倒叙 (默认)
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function export()
    {        
        $param = $this->parameters([
            'key'=>self::T_INT,
            'keyword'=>self::T_STRING,
            'pay_time_min'=>self::T_STRING,
            'pay_time_min'=>self::T_STRING,
            'pay_time_max'=>self::T_STRING,
            'page'=>self::T_INT,
            'page_size'=>self::T_INT,
            'sort_key'=>self::T_STRING,
            'sort_type'=>self::T_STRING,
        ]);  
        $header = ['店铺名称','付款单号','付款类型','付款金额','换算消费额','付款日期','创建日期','制单人','状态'];      
        $items = ShopCountApi::getPrepayCondition($param)->addSelect('updated_at')->get()->toArray(); 
        Event::fire('shopcount.export');
        $this->export_xls("转付单".date("Ymd"),$header,self::format_prepay_data($items));
    }
    
   /**
     * @api {post} /shop_count/preview 2.转付单预览
     * @apiName preview
     * @apiGroup ShopCount
     *
     * @apiParam {Number} type  转付单类型
     * @apiParam {Number} merchant_id  商户id
     * @apiParam {Number} salon_id     店铺id
     * @apiParam {Number} pay_money    付款金额
     * @apiParam {Number} cost_money   换算消费额
     * @apiParam {String} day   付款日期 (YYYY-MM-DD)
     * 
     * @apiSuccess {String} code 付款单号.
     * @apiSuccess {Number} type 付款单类型  1:付交易代收款  2:付交易代收款 3:交易代收款返还
     * @apiSuccess {Number} state 状态 1:已付款 0:预览状态
     * @apiSuccess {String} pay_money 付款金额.
     * @apiSuccess {String} cost_money 换算消费额.
     * @apiSuccess {String} created_at 创建时间.
     * @apiSuccess {String} day  付款日期.
     * @apiSuccess {Object} user  制表人信息.
     * @apiSuccess {Object} salon 店铺信息.
     * @apiSuccess {Object} merchant 商盟信息.
     *
     * @apiSuccessExample Success-Response:
     *       {
     *           "result": 1,
     *           "data": {
     *               "id": 1,
     *               "created_at": "2015-07-03 00:00:00",
     *               "merchant_id": 1,
     *               "salon_id": 1,
     *               "code": "fasdfasdfasdfasdfadfa",
     *               "type": 1,
     *               "uid": 1,
     *               "pay_money": "2000.00",
     *               "cost_money": "2500.00",
     *               "day": "2015-07-02",
     *               "user": {
     *                   "id": 1,
     *                   "name": ""
     *               },
     *               "salon": {
     *                   "salonid": 1,
     *                   "salonname": "嘉美专业烫染"
     *               },
     *               "merchant": {
     *                   "id": 1,
     *                   "name": "速度发多少"
     *               }
     *           }
     *       }
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function create()
    {
       $param_must = $this->parameters( 
           ['type'=>self::T_INT,
            'merchant_id'=>self::T_INT,
            'salon_id'=>self::T_INT
           ],true);
       $param = $this->parameters([           
            'pay_money'=>self::T_INT,
            'cost_money'=>self::T_INT,
            'day'=>self::T_STRING,
        ]);
        $param = array_merge($param,$param_must);
        $param['uid'] = $this->user->id;        
        $id = ShopCountApi::makePreviewPrepay($param);
        if(! $id)
        {
            throw new \Exception("参数有误,生成预览失败");
        }
        $items = ShopCountApi::prepayDetail($id);
        Event::fire('shopcount.create',$items['code']);
        return $this->success($items);
    }

     /**
     * @api {post} /shop_count/create 3.新建转付单
     * @apiName create
     * @apiGroup ShopCount
     *
     * @apiParam {Number} id  有预览时  将预览生成的id带过来
     * @apiParam {Number} merchant_id  商户id 有id时可不填 否则为必填
     * @apiParam {Number} salon_id     店铺id 有id时可不填 否则为必填
     * @apiParam {Number} pay_money    付款金额  有id时可不填 否则为必填
     * @apiParam {Number} cost_money   换算消费额  有id时可不填 否则为必填
     * @apiParam {String} day   付款日期 (YYYY-MM-DD) 有id时可不填 否则为必填
     * 
     * @apiSuccess {String} id 创建成功后的id.
     *
     * @apiSuccessExample Success-Response:
     *       {
     *           "result": 1,
     *           "data": {
     *               "id": 1
     *           }
     *       }
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "参数有误,生成失败"
     *		}
     */
    public function store()
    {
        $param = $this->parameters([
            'id'=>self::T_INT,
            'type'=>self::T_INT,
            'merchant_id'=>self::T_INT,
            'salon_id'=>self::T_INT,
            'pay_money'=>self::T_INT,
            'cost_money'=>self::T_INT,
            'day'=>self::T_STRING,
        ]);
        $param['uid'] = $this->user->id;
        if(isset($param['id']))
        {
            $id =  $param['id'];
            ShopCountApi::updatePrepay($param['id'], $param);
        }
        else
        {
            $id = ShopCountApi::makePrepay($param);
            if(!$id)
            {
                throw new \Exception("参数有误,生成失败");
            }
        }
        $prepays = PrepayBill::where('id',$id)->get()->toArray();
        Event::fire('shopcount.store',$prepays[0]['code']);
        return $this->success(['id'=>$id]);
    }

   /**
     * @api {get} /shop_count/show/{id} 4.转付单详情
     * @apiName show
     * @apiGroup ShopCount
     *
     * @apiParam {Number} id  id
     *
     * @apiSuccess {String} code 付款单号.
     * @apiSuccess {Number} type 付款单类型  1:付交易代收款  2:付交易代收款 3:交易代收款返还
     * @apiSuccess {Number} state 状态 1:已付款 0:预览状态
     * @apiSuccess {String} pay_money 付款金额.
     * @apiSuccess {String} cost_money 换算消费额.
     * @apiSuccess {String} created_at 创建时间.
     * @apiSuccess {String} day  付款日期.
     * @apiSuccess {Object} user  制表人信息.
     * @apiSuccess {Object} salon 店铺信息.
     * @apiSuccess {Object} merchant 商盟信息.
     *
     * @apiSuccessExample Success-Response:
     *       {
     *           "result": 1,
     *           "data": {
     *               "id": 1,
     *               "created_at": "2015-07-03 00:00:00",
     *               "merchant_id": 1,
     *               "salon_id": 1,
     *               "code": "fasdfasdfasdfasdfadfa",
     *               "type": 1,
     *               "uid": 1,
     *               "pay_money": "2000.00",
     *               "cost_money": "2500.00",
     *               "day": "2015-07-02",
     *               "user": {
     *                   "id": 1,
     *                   "name": ""
     *               },
     *               "salon": {
     *                   "salonid": 1,
     *                   "salonname": "嘉美专业烫染"
     *               },
     *               "merchant": {
     *                   "id": 1,
     *                   "name": "速度发多少"
     *               }
     *           }
     *       }
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function show($id)
    {
        $items = ShopCountApi::prepayDetail($id);
        return $this->success($items);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

     /**
     * @api {post} /shop_count/update/{id} 5.转付单 修改
     * @apiName update
     * @apiGroup ShopCount
     *
     * @apiParam {Number} merchant_id  商户id 
     * @apiParam {Number} salon_id     店铺id 
     * @apiParam {Number} pay_money    付款金额  
     * @apiParam {Number} cost_money   换算消费额 
     * @apiParam {String} day   付款日期 (YYYY-MM-DD) 
     * 
     * @apiSuccess {String} id 修改成功后的id.
     *
     * @apiSuccessExample Success-Response:
     *       {
     *           "result": 1,
     *           "data": {
     *               "id": 1
     *           }
     *       }
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "参数有误,生成失败"
     *		}
     */
    public function update($id)
    {        
        $param = $this->parameters([
            'merchant_id'=>self::T_INT,
            'salon_id'=>self::T_INT,
            'pay_money'=>self::T_INT,
            'cost_money'=>self::T_INT,
            'day'=>self::T_STRING,
        ]);
        $param['uid'] = $this->user->id;
        $ret = ShopCountApi::updatePrepay($id, $param);
        if(! $ret)
        {
            throw new \Exception("参数有误,更新失败");
        }
        
        $prepays = PrepayBill::where('id',$id)->get()->toArray();
        Event::fire('shopcount.update',$prepays[0]['code']);
        return $this->success(['id'=>$id]);
    }

     /**
     * @api {post} /shop_count/destroy/{id} 6.转付单 删除
     * @apiName destroy
     * @apiGroup ShopCount
     * 
     * @apiSuccess {String} ret 1 成功删除
     *
     * @apiSuccessExample Success-Response:
     *       {
     *           "result": 1,
     *           "data": {
     *               "ret": 1
     *           }
     *       }
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "参数有误,生成失败"
     *		}
     */
    public function destroy($id)
    {        
        //是 ShopCount 不是 ShopCountApi  不要改回去了
        $prepays = PrepayBill::where('id',$id)->get()->toArray();
        if(empty($prepays) || !isset($prepays[0]))
        {
            return $this->success(['ret'=>1]);
        }
        $prepay = $prepays[0];
        
        $ret = ShopCount::deletePrepay($id);
        if($ret)
        {
            Event::fire('shopcount.destroy',$prepay['code']);
            return $this->success(['ret'=>1]);
        }
        else
        {
            return $this->error("delete error!");
        }
    }
    
    /**
     * @api {get} /shop_count/delegate_list 7.代收单 列表
     * @apiName delegate_list
     * @apiGroup ShopCount
     *
     * @apiParam {Number} key  1 店铺搜索 2 商户搜索 3 店铺编号
     * @apiParam {String} keyword  根据key来的关键字
     * @apiParam {String} pay_time_min 付款最小时间 YYYY-MM-DD
     * @apiParam {String} pay_time_max 付款最大时间 YYYY-MM-DD
     * @apiParam {Number} page 可选,页数. (从1开始)
     * @apiParam {Number} page_size 可选,分页大小.(最小1 最大500,默认20)
     * @apiParam {String} sort_key 排序的键 ['id','created_at'(创建时间,默认),'code'(代收单号),'type'(代收类型),'money'(代收金额),'day'(代收日期)]
     * @apiParam {String} sort_type 排序的方式 ASC正序 DESC倒叙 (默认)
     *
     * @apiSuccess {Number} total 总数据量.
     * @apiSuccess {Number} per_page 分页大小.
     * @apiSuccess {Number} current_page 当前页面.
     * @apiSuccess {Number} last_page 当前页面.
     * @apiSuccess {Number} from 起始数据.
     * @apiSuccess {Number} to 结束数据.
     * @apiSuccess {String} code 代收单号.
     * @apiSuccess {Number} type 代收类型.1项目消费.
     * @apiSuccess {String} money 代收金额.
     * @apiSuccess {String} created_at 创建时间.
     * @apiSuccess {String} day  代收日期.
     * @apiSuccess {Object} salon 店铺信息.
     * @apiSuccess {Object} merchant 商盟信息.
     *
     * @apiSuccessExample Success-Response:
     *       {
     *           "result": 1,
     *           "data": {
     *               "total": 1,
     *               "per_page": 10,
     *               "current_page": 1,
     *               "last_page": 1,
     *               "from": 1,
     *               "to": 1,
     *               "data": [
     *                   {
     *                       "id": 1,
     *                       "created_at": "2015-07-02 00:00:00",
     *                       "merchant_id": 2,
     *                       "salon_id": 3,
     *                       "code": "dfasdagasdfasdfasd",
     *                       "type": 1,
     *                       "money": "3600.00",
     *                       "day": "2015-06-01",
     *                       "salon": {
     *                           "salonid": 3,
     *                           "salonname": "米莱国际造型连锁（田贝店）"
     *                       },
     *                       "merchant": {
     *                           "id": 2,
     *                           "name": "地对地导弹"
     *                       }
     *                   }
     *               ]
     *           }
     *       }
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function delegate_list()
    {
        $param = $this->parameters([
            'key'=>self::T_INT,
            'keyword'=>self::T_STRING,
            'pay_time_min'=>self::T_STRING,
            'pay_time_min'=>self::T_STRING,
            'pay_time_max'=>self::T_STRING,
            'page'=>self::T_INT,
            'page_size'=>self::T_INT,
            'sort_key'=>self::T_STRING,
            'sort_type'=>self::T_STRING,
        ]);
        $items = ShopCountApi::searchInsteadReceive($param);
        return $this->success($items);
    }
    
    /**
     * @api {get} /shop_count/delegate_export 12.导出代收单
     * @apiName delegate_export
     * @apiGroup ShopCount
     *
     * @apiParam {Number} key  1 店铺搜索 2 商户搜索 3 店铺编号
     * @apiParam {String} keyword  根据key来的关键字
     * @apiParam {String} pay_time_min 付款最小时间 YYYY-MM-DD
     * @apiParam {String} pay_time_max 付款最大时间 YYYY-MM-DD
     * @apiParam {Number} page 可选,页数. (从1开始)
     * @apiParam {Number} page_size 可选,分页大小.(最小1 最大500,默认20)
     * @apiParam {String} sort_key 排序的键 ['id','created_at'(创建时间,默认),'code'(代收单号),'type'(代收类型),'money'(代收金额),'day'(代收日期)]
     * @apiParam {String} sort_type 排序的方式 ASC正序 DESC倒叙 (默认)
     *  
     */
    public function delegate_export()
    {
        $param = $this->parameters([
            'key'=>self::T_INT,
            'keyword'=>self::T_STRING,
            'pay_time_min'=>self::T_STRING,
            'pay_time_min'=>self::T_STRING,
            'pay_time_max'=>self::T_STRING,
            'page'=>self::T_INT,
            'page_size'=>self::T_INT,
            'sort_key'=>self::T_STRING,
            'sort_type'=>self::T_STRING,
        ]);
        $header = ['店铺名称','代收单号','代收类型','代收金额','代收日期'];
        $items = self::getInsteadReceiveCondition($param)->get()->toArray();
        Event::fire('shopcount.delegateExport');       
        $this->export_xls("代收单".date("Ymd"), $header, self::format_ir_data($items));
        return $this->success($items);
    }
    
    /**
     * @api {get} /shop_count/delegate_detail/{id} 8.代收单 详情
     * @apiName delegate_detail
     * @apiGroup ShopCount
     *
     * @apiParam {Number} id  id
     *
     * @apiSuccess {String} code 代收单号.
     * @apiSuccess {Number} type 代收类型.1项目消费.
     * @apiSuccess {String} money 代收金额.
     * @apiSuccess {String} created_at 创建时间.
     * @apiSuccess {String} day  代收日期.
     * @apiSuccess {Object} salon 店铺信息.
     * @apiSuccess {Object} merchant 商盟信息.
     *
     * @apiSuccessExample Success-Response:
     *       {
     *           "result": 1,
     *           "data": {
     *               "id": 1,
     *               "created_at": "2015-07-02 00:00:00",
     *               "merchant_id": 2,
     *               "salon_id": 3,
     *               "code": "dfasdagasdfasdfasd",
     *               "type": 1,
     *               "money": "3600.00",
     *               "day": "2015-06-01",
     *               "salon": {
     *                   "salonid": 3,
     *                   "salonname": "米莱国际造型连锁（田贝店）"
     *               },
     *               "merchant": {
     *                   "id": 2,
     *                   "name": "地对地导弹"
     *               }
     *           }
     *       }
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function delegate_detail($id)
    {
        $item = ShopCountApi::insteadReceiveDetail($id);
        return $this->success($item);
    }
    
    /**
     * @api {get} /shop_count/balance 9.商户往来列表
     * @apiName balance
     * @apiGroup ShopCount
     *
     * @apiParam {Number} key  1 店铺搜索 2 商户搜索 3 商户编号
     * @apiParam {String} keyword  根据key来的关键字
     * @apiParam {Number} page 可选,页数. (从1开始)
     * @apiParam {Number} page_size 可选,分页大小.(最小1 最大500,默认20)
     * @apiParam {String} sort_key 排序的键 ['id','created_at'(创建时间,默认),'salon_name','salon_type','pay_money','cost_money',...(money相关的key)]
     * @apiParam {String} sort_type 排序的方式 ASC正序 DESC倒叙 (默认)
     *
     * @apiSuccess {Number} total 总数据量.
     * @apiSuccess {Number} per_page 分页大小.
     * @apiSuccess {Number} current_page 当前页面.
     * @apiSuccess {Number} last_page 当前页面.
     * @apiSuccess {Number} from 起始数据.
     * @apiSuccess {Number} to 结束数据.
     * @apiSuccess {String} merchant_name 商户名称.
     * @apiSuccess {String} salon_name 店铺名称.
     * @apiSuccess {Number} salon_type 店铺名称类型(1预付款店 2投资店 3金字塔店).
     * @apiSuccess {String} pay_money 预付款/付交易代收款.
     * @apiSuccess {String} cost_money 换算消费额.
     * @apiSuccess {String} spend_money 交易消费额.
     * @apiSuccess {String} balance_money 交易余额.
     * @apiSuccess {String} invest_money 付投资款.
     * @apiSuccess {String} invest_return_money 付投款返还.
     * @apiSuccess {String} invest_balance_money 投资余额.
     * @apiSuccess {String} borrow_money 付借款.
     * @apiSuccess {String} borrow_return_money 借款返还.
     * @apiSuccess {String} borrow_balance_money 借款余额.
     *
     * @apiSuccessExample Success-Response:
     *       {
     *           "result": 1,
     *           "data": {
     *               "total": 1,
     *               "per_page": 10,
     *               "current_page": 1,
     *               "last_page": 1,
     *               "from": 1,
     *               "to": 1,
     *               "data": [
     *                   {
     *                       "id": 1,
     *                       "created_at": "2015-07-01 00:00:00",
     *                       "merchant_id": 3,
     *                       "merchant_name":"米莱国际",
     *                       "salon_id": 2,
     *                       "salon_name":"米莱国际造型连锁(田贝店)",
     *                       "salon_type":1,
     *                       "pay_money": "123.00",
     *                       "cost_money": "111.00",
     *                       "spend_money": "23434.00",
     *                       "balance_money": "2334.00",
     *                       "invest_money": "2334.00",
     *                       "invest_return_money": "23.00",
     *                       "invest_balance_money": "343.00",
     *                       "borrow_money": "2323.00",
     *                       "borrow_return_money": "34.00",
     *                       "borrow_balance_money": "2334.00"
     *                   }
     *               ]
     *           }
     *       }
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function balance()
    {
        $param = $this->parameters([
            'key'=>self::T_INT,
            'keyword'=>self::T_STRING,
            'pay_time_min'=>self::T_STRING,
            'pay_time_min'=>self::T_STRING,
            'pay_time_max'=>self::T_STRING,
            'page'=>self::T_INT,
            'page_size'=>self::T_INT,
            'sort_key'=>self::T_STRING,
            'sort_type'=>self::T_STRING,
        ]);
        $items = ShopCountApi::searchShopCount($param);
        return $this->success($items);
    }
    
    /**
     * @api {get} /shop_count/balance_export 13.导出商户往来列表
     * @apiName balance_export
     * @apiGroup ShopCount
     *
     * @apiParam {Number} key  1 店铺搜索 2 商户搜索 3 商户编号
     * @apiParam {String} keyword  根据key来的关键字
     * @apiParam {Number} page 可选,页数. (从1开始)
     * @apiParam {Number} page_size 可选,分页大小.(最小1 最大500,默认20)
     * @apiParam {String} sort_key 排序的键 ['id','created_at'(创建时间,默认),'salon_name','salon_type','pay_money','cost_money',...(money相关的key)]
     * @apiParam {String} sort_type 排序的方式 ASC正序 DESC倒叙 (默认)
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function balance_export()
    {        
        $param = $this->parameters([
            'key'=>self::T_INT,
            'keyword'=>self::T_STRING,
            'pay_time_min'=>self::T_STRING,
            'pay_time_min'=>self::T_STRING,
            'pay_time_max'=>self::T_STRING,
            'page'=>self::T_INT,
            'page_size'=>self::T_INT,
            'sort_key'=>self::T_STRING,
            'sort_type'=>self::T_STRING,
        ]);
        $header = ['店铺','所属商户','店铺类型','预付款--付交易代收款','换算消费额','交易消费额','交易余额','付投资款','付投款返还','投资余额','付借款','借款返还','借款余额'];
        $items = ShopCountApi::getShopCountCondition($param)->get()->toArray();       
        Event::fire('shopcount.balanceExport');
        $this->export_xls("店铺往来".date("Ymd"), $header, self::format_shopcount_data($items));
    }
    
    /**
     * @api {post} /shop_count/count_order 10.订单结算相关 (外部调用)
     * @apiName count_order
     * @apiGroup ShopCount
     *
     * @apiParam {Number} type  1 订单 2 赏金单
     * @apiParam {String} ordersn  订单单号,赏金单单号(多个用英文逗号","隔开)
     * @apiParam {String} token  加密验证指纹
     *
     * @apiSuccess {Array} success 成功结算的订单号
     * @apiSuccess {Array} already 已经结算过的订单号
     * @apiSuccess {Number} type 结算的类型 同传入的type
     *
     * @apiSuccessExample Success-Response:
     *      {
     *           "result": 1,
     *           "data": {
     *               "success": [
     *                   "33923619970",
     *                   "33924964189",
     *                   "33927676599",
     *                   "33928797449",
     *                   "33929103073",
     *                   "33929641274",
     *                   "33929787504",
     *                   "33930191013",
     *                   "33930816691",
     *                   "33994190861"
     *               ],
     *               "type": 2,
     *               "already": [
     *
     *               ]
     *           }
     *       }
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "错误信息"
     *		}
     */    
    public function countOrder()
    {
        $param = $this->parameters([
            'type'=>self::T_INT,
            'ordersn'=>self::T_STRING,
            'token'=>self::T_STRING,
        ],true);
        $passed = ShopCountApi::checkToken($param);
        if(!$passed)
        {
            return $this->error("Unauthorized",401);
        }
        $orders = explode(",", $param['ordersn']);
        $res = null;
        $operation = "";
        if ($param['type'] == 1)
        {
            $operation = "订单结算";
            $res = ShopCountApi::countOrder($orders);
        }
        else if($param['type'] == 2)
        {
            $operation = "赏金单结算";
            $res = ShopCountApi::countBounty($orders);
        }
        if(!empty($res))
        {
            Event::fire("shopcount.countOrder",['operation'=>$operation,'object'=>$param['ordersn']]);
        }
        return $this->success($res);
    }

    protected static function format_prepay_data($datas)
    {
        $res = [];
        foreach ($datas as $data) {
            $salon_name = isset($data['salon']['salonname']) ? $data['salon']['salonname'] : '';
            $typename = $data['type'] == 3 ? "交易代收款返还" : "付交易代收款";
            $username = $data['user']['name'];
            $statename = "已付款";
            $res[] = [
                'salon_name' => $salon_name,
                'code' => $data['code'],
                'typename' => $typename,
                'pay_money' => $data['pay_money'],
                'cost_money' => $data['cost_money'],
                'day' => $data['day'],
                'updated_at' => $data['updated_at'],
                'username' => $username,
                'statename' => $statename
            ];
        }
        return $res;
    }

    protected static function format_ir_data($datas)
    {
        $res = [];
        foreach ($datas as $data) {
            $salon_name = isset($data['salon']['salonname']) ? $data['salon']['salonname'] : '';
            $typename = "项目消费";
            $res[] = [
                'salon_name' => $salon_name,
                'code' => $data['code'],
                'typename' => $typename,
                'money' => $data['money'],
                'day' => $data['day']
            ];
        }
        return $res;
    }

    protected static function format_shopcount_data($datas)
    {
        $res = [];
        foreach ($datas as $data) {
            $salon_type_name = "";
            switch ($data['salon_type']) {
                case 1:
                    $salon_type_name = "预付款店";
                    break;
                case 2:
                    $salon_type_name = "投资店";
                    break;
                case 3:
                    $salon_type_name = "金字塔店";
                    break;
            }
            $typename = "项目消费";
            $res[] = [
                'salon_name' => $data['salon_name'],
                'merchant_name' => $data['merchant_name'],
                'salon_type_name' => $salon_type_name,
                'pay_money' => $data['pay_money'],
                'cost_money' => $data['cost_money'],
                'spend_money' => $data['spend_money'],
                'balance_money' => $data['balance_money'],
                'invest_money' => $data['invest_money'],
                'invest_return_money' => $data['invest_return_money'],
                'invest_balance_money' => $data['invest_balance_money'],
                'borrow_money' => $data['borrow_money'],
                'borrow_return_money' => $data['borrow_return_money'],
                'borrow_balance_money' => $data['borrow_balance_money']
            ];
        }
        return $res;
    }
}
