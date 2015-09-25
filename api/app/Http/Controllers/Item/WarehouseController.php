<?php
/**
 * 项目仓库
 */
namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;

class WarehouseController extends Controller
{
    /**
     * @api {get} /warehouse/index 1.项目仓库列表
     * @apiName index
     * @apiGroup Warehouse
     *
     * @apiParam {String} item_name  项目名称的关键字
     * @apiParam {String} type 项目类型 0 全部 1普通项目 2闲时特价
     * @apiParam {String} cat 项目分类 0 全部 1普通项目 2闲时特价
     * @apiParam {String} norms_cat 项目规格0 全部 1有规格 2无规格
     * @apiParam {String} exp 项目期限 0 全部 1有期限 2无期限
     * @apiParam {String} use_limit 限制资格 0 全部 1 限首单 2限推荐
     * @apiParam {Number} page 页数 (从1开始)
     * @apiParam {Number} page_size 可选,分页大小.(最小1 最大500,默认20)
     * @apiParam {String} sort_key 排序的键 ['id','created_at'(创建时间,默认),'code'(付款单号),'type'(付款类型),'pay_money'(付款金额),'pay_type(付款方式)','cost_money'(换算消费额),'day'(付款日期)]
     * @apiParam {String} sort_type 排序的方式 ASC正序 DESC倒叙 (默认)
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
     * @api {get} /warehouse/show/{id} 2.项目仓库详情
     * @apiName show
     * @apiGroup Warehouse
     *
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
        //
    }
    
    /**
     * @api {get} /warehouse/puton 3.上架
     * @apiName puton
     * @apiGroup Warehouse
     *
     * @apiParam {String} ids  要上架的id (多个逗号隔开)
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function puton()
    {
        
    }
    
    /**
     * @api {get} /warehouse/import 4.导入
     * @apiName import
     * @apiGroup Warehouse
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
	public function import()
	{
	
	}
}
