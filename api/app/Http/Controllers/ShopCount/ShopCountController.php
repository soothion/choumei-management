<?php
/**
 * 店铺结算相关
 * @author nian.zhu
 */
namespace App\Http\Controllers\ShopCount;


use App\Http\Controllers\Controller;
use App\ShopCountApi;

class ShopCountController extends Controller
{
    /**
     * @api {get} /shop_count/index 1.角色列表
     * @apiName index
     * @apiGroup ShopCount
     *
     * @apiParam {Number} key  1 店铺搜索 2 商户搜索
     * @apiParam {String} keyword  根据key来的关键字
     * @apiParam {String} pay_time_min 付款最小时间 YYYY-MM-DD
     * @apiParam {String} pay_time_max 付款最大时间 YYYY-MM-DD
     * @apiParam {Number} page 可选,页数. (从1开始)
     * @apiParam {Number} size 可选,分页大小.(最小1 最大500,默认10)
     * @apiParam {String} sort_key 排序的键 ['id','created_at'(创建时间,默认),'code'(付款单号),'type'(付款类型),'pay_money'(付款金额),'cost_money'(换算消费额),'day'(付款日期)]
     * @apiParam {String} sort_type 排序的方式 1正序 2倒叙 (默认)
     *
     * @apiSuccess {Number} total 总数据量.
     * @apiSuccess {Number} per_page 分页大小.
     * @apiSuccess {Number} current_page 当前页面.
     * @apiSuccess {Number} last_page 当前页面.
     * @apiSuccess {Number} from 起始数据.
     * @apiSuccess {Number} to 结束数据.
     * @apiSuccess {String} code 付款单号.
     * @apiSuccess {Number} type 状态.1已付款.
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
        $items = ShopCountApi::searchPrepay($this->param);
        return $this->success($items);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
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
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
    
    /**
     * 代收单 列表  查询
     *
     * @return array 
     */
    public function delegate_list()
    {
        
    }
    
    /**
     * 代收单 详情
     * @param int $id
     */
    public function delegate_detail($id)
    {
        
    }
    
    /**
     * 商户往来余额
     */
    public function balance()
    {
        
    }
    
    /**
     * 商户往来余额 详情
     */
    public function balance_detail()
    {
    
    }
}
