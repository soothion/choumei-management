<?php
/**
*财务  付款管理
*/
namespace App\Http\Controllers\Pay;

use App\Http\Controllers\Controller;
use App\PayManage;

class PayController extends Controller
{    
    /**
     * @api {get} /pay_manage/index 1.付款单列表
     * @apiName index
     * @apiGroup PayManage
     *
     * @apiParam {Number} key  1 店铺搜索  2 店铺编号
     * @apiParam {String} keyword  根据key来的关键字
     * @apiParam {String} pay_time_min 付款最小时间 YYYY-MM-DD
     * @apiParam {String} pay_time_max 付款最大时间 YYYY-MM-DD
     * @apiParam {String} type 0 全部 1 付交易代收款 2 付业务投资款
     * @apiParam {String} pay_type 0 全部 1 银行存款 2账扣支付 3现金  4支付宝 5财付通
     * @apiParam {String} state 0 全部 1 待提交 2待审批 3待付款  4已付款
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
     *                         "sn":"商铺编号",
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $params = $this->parameters([
            'type' => self::T_INT,
            'salon_id' => self::T_INT,
            'money' => self::T_FLOAT,
            'pay_type' => self::T_INT,
            'require_day' => self::T_STRING,
            'cycle' => self::T_INT,
            'cycle_day' => self::T_INT,
            'cycle_money' => self::T_FLOAT,
        ],true);
        //#@todo for debug
        // $params['make_uid'] = $this->user->id;
        $params['code'] = PayManage::makeNewCode($params['type']);
        $params['make_uid'] = 1;
        $params['state'] = PayManage::STATE_OF_TO_CHECK;
        $id = PayManage::insertGetId($params);
        if($id)
        {
            return $this->success(['id'=>$id]);
        }
        else 
        {
            return $this->error("创建失败");
        }
       
    }

    /**
     * @api {post} /pay_manage/show/{id} 6.付款单  查看
     * @apiName show
     * @apiGroup PayManage
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
     *		    "msg": "参数有误,删除失败"
     *		}
     */
    public function show($id)
    {
        $query = PayManage::where('id',$id);
        $query->with([
            'make_user' => function ($q)
            {
                $q->lists('name');
            }
        ])->with([
            'confirm_user' => function ($q)
            {
                $q->lists('name');
            }
        ])->with([
            'cash_user' => function ($q)
            {
                $q->lists('name');
            }
        ])->with([
            'salon' => function ($q)
            {
                $q->get(['salonname','sn']);
            }
        ]);
        $item = $query->first()->toArray();
        
        return $this->success($item);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $params = $this->parameters([
            'type' => self::T_INT,
            'salon_id' => self::T_INT,
            'money' => self::T_FLOAT,
            'pay_type' => self::T_INT,
            'require_day' => self::T_STRING,
            'cycle' => self::T_INT,
            'cycle_day' => self::T_INT,
            'cycle_money' => self::T_FLOAT,
        ],true);
        //#@todo for debug
        // $params['make_uid'] = $this->user->id;
        $params['make_uid'] = 1;
        
        $item = PayManage::where('id',$id)->first(['type']);
        if($item->type  != PayManage::STATE_OF_TO_SUBMIT || $item->type  != PayManage::STATE_OF_TO_CHECK )
        {
            return $this->error("此单状态不允许修改");
        }
        
        $params['state'] = PayManage::STATE_OF_TO_CHECK;
        PayManage::where('id',$id)->update($params);
        return $this->success(['id'=>$id]);
    }

    /**
     * @api {post} /pay_manage/destroy/{id} 6.付款单  删除
     * @apiName destroy
     * @apiGroup PayManage
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
     *		    "msg": "参数有误,删除失败"
     *		}
     */
    public function destroy($id)
    {
        $item = PayManage::where('id',$id)->first(['type']);
        if($item->type  != PayManage::STATE_OF_TO_SUBMIT || $item->type  != PayManage::STATE_OF_TO_CHECK )
        {
            return $this->error("此单状态不允许删除");
        }
        PayManage::where('id',$id)->delete();
        return $this->success(["ret"=>1]);
    }
    
    /**
     * @api {post} /pay_manage/check 6.付款单  审批
     * @apiName destroy
     * @apiGroup PayManage
     *
     * @apiParam {String} ids  id1,id2,... 
     * @apiParam {Number} do  1 通过 2 拒绝
     * 
     * @apiSuccess {String} ret 1 执行成功
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
     *		    "msg": "参数有误,执行失败"
     *		}
     */
    public function check()
    {
        $params = $this->parameters([
            'ids' => self::T_STRING,
            'do' => self::T_INT,
        ],true);
    }

    /**
     * @api {post} /pay_manage/check 6.付款单  确认
     * @apiName destroy
     * @apiGroup PayManage
     *
     * @apiParam {String} ids  id1,id2,...
     * @apiParam {Number} do  1 通过 2 拒绝
     *
     * @apiSuccess {String} ret 1 执行成功
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
     *		    "msg": "参数有误,执行失败"
     *		}
     */
    public function confirm()
    {
        $params = $this->parameters([
            'ids' => self::T_STRING,
            'do' => self::T_INT,
        ],true);
    }

    /**
     * @api {get} /pay_manage/export 1.付款单导出
     * @apiName export
     * @apiGroup PayManage
     *
     * @apiParam {Number} key  1 店铺搜索  2 店铺编号
     * @apiParam {String} keyword  根据key来的关键字
     * @apiParam {String} pay_time_min 付款最小时间 YYYY-MM-DD
     * @apiParam {String} pay_time_max 付款最大时间 YYYY-MM-DD
     * @apiParam {String} type 0 全部 1 付交易代收款 2 付业务投资款
     * @apiParam {String} pay_type 0 全部 1 银行存款 2账扣支付 3现金  4支付宝 5财付通
     * @apiParam {String} state 0 全部 1 待提交 2待审批 3待付款  4已付款
     * @apiParam {Number} page 可选,页数. (从1开始)
     * @apiParam {Number} page_size 可选,分页大小.(最小1 最大500,默认20)
     * @apiParam {String} sort_key 排序的键 ['id','created_at'(创建时间,默认),'code'(付款单号),'type'(付款类型),'pay_money'(付款金额),'cost_money'(换算消费额),'day'(付款日期)]
     * @apiParam {String} sort_type 排序的方式 ASC正序 DESC倒叙 (默认)
     * 
     *
     */
    public function export()
    {
        
    }
}
