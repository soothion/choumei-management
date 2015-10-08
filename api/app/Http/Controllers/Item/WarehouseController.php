<?php
/**
 * 项目仓库
 */
namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\AbstractPaginator;
use App\SalonItem;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;
use App\SalonItemFormats;

class WarehouseController extends Controller
{
    /**
     * @api {get} /warehouse/index 1.项目仓库列表
     * @apiName index
     * @apiGroup Warehouse
     * 
     * @apiParam {String} salonid  店铺
     * @apiParam {String} itemname  项目名称的关键字
     * @apiParam {String} typeid 项目类型 0 全部 1普通项目 2闲时特价
     * @apiParam {String} item_type 项目分类 0 全部 1普通项目 2闲时特价
     * @apiParam {String} norms_cat_id 项目规格0 全部 1有规格 2无规格
     * @apiParam {String} exp_time 项目期限 0 全部 1有期限 2无期限
     * @apiParam {String} buylimit 限制资格 0 全部 1 限首单 2限推荐
     * @apiParam {Number} page 页数 (从1开始)
     * @apiParam {Number} page_size 可选,分页大小.(最小1 最大500,默认20)
     * @apiParam {String} sort_key 排序的键 ['itemid','salonid','typeid','norms_cat_id','itemname','item_type','minPrice','minPriceOri','minPriceGroup']
     * @apiParam {String} sort_type 排序的方式 ASC正序 DESC倒叙 (默认)
     * 
     * @apiSuccess {Number} total 总数据量.
     * @apiSuccess {Number} per_page 分页大小.
     * @apiSuccess {Number} current_page 当前页面.
     * @apiSuccess {Number} last_page 当前页面.
     * @apiSuccess {Number} from 起始数据.
     * @apiSuccess {Number} to 结束数据.
     * @apiSuccess {String} itemid 项目id
     * @apiSuccess {String} salonid 店铺id
     * @apiSuccess {String} itemname 项目名称
     * @apiSuccess {String} minPriceOri 原价(最低)
     * @apiSuccess {String} maxPriceOri 原价(最高)
     * @apiSuccess {String} minPrice 臭美价(最低)
     * @apiSuccess {String} maxPrice 臭美价(最高)
     * @apiSuccess {String} minPriceGroup 集团价(最低)
     * @apiSuccess {String} maxPriceGroup 集团价(最高)
     * @apiSuccess {String} salon_norms_cat 项目规格信息 (待定)
     * @apiSuccess {String} item_type 项目类型 (1普通项目2闲时特价)
     * @apiSuccess {String} salon 店铺信息
     * @apiSuccess {String} salon.salonname 店铺名
     * @apiSuccess {String} salon_item_type 项目分类信息
     * @apiSuccess {String} salon_item_type.typename 项目分类名称
     * @apiSuccess {String} salon_norms_cat 项目规格信息 (待定)
     * 
     * @apiSuccessExample Success-Response:
     *      {
     *           "total": 43353,
     *           "per_page": 20,
     *           "current_page": 1,
     *           "last_page": 2168,
     *           "from": 1,
     *           "to": 20,
     *           "data": [
     *               {
     *                   "itemid": 44977,
     *                   "salonid": 1216,
     *                   "typeid": 10,
     *                   "norms_cat_id": 0,
     *                   "itemname": "老姜洗发水",
     *                   "item_type": 1,
     *                   "minPrice": 25,
     *                   "maxPrice": 25,
     *                   "minPriceOri": 30,
     *                   "maxPriceOri": 30,
     *                   "minPriceGroup": "23.00",
     *                   "maxPriceGroup": "23.00",
     *                   "salon": {
     *                       "salonid": 1216,
     *                       "salonname": "阿伟专业造型"
     *                   },
     *                   "salon_item_type": {
     *                       "typeid": 10,
     *                       "typename": "其他"
     *                   },
     *                   "salon_norms_cat": null
     *               },
     *               {
     *                   "itemid": 44976,
     *                   "salonid": 1216,
     *                   "typeid": 10,
     *                   "norms_cat_id": 0,
     *                   "itemname": "香缇卡洗发水",
     *                   "item_type": 1,
     *                   "minPrice": 21,
     *                   "maxPrice": 21,
     *                   "minPriceOri": 25,
     *                   "maxPriceOri": 25,
     *                   "minPriceGroup": "19.00",
     *                   "maxPriceGroup": "19.00",
     *                   "salon": {
     *                       "salonid": 1216,
     *                       "salonname": "阿伟专业造型"
     *                   },
     *                   "salon_item_type": {
     *                       "typeid": 10,
     *                       "typename": "其他"
     *                   },
     *                   "salon_norms_cat": null
     *               }
     *           ]
     *       }
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function index()
    {
        $params = $this->parameters([
            'salonid'=>self::T_INT,
            'itemname'=>self::T_STRING,
            'typeid'=>self::T_INT,
            'item_type'=>self::T_INT,
            'norms_cat_id'=>self::T_INT,
            'exp_time'=>self::T_INT,
            'buylimit'=>self::T_INT,
            'page'=>self::T_INT,
            'page_size'=>self::T_INT,
            'sort_key'=>self::T_STRING,
            'sort_type'=>self::T_STRING,
        ]);
        
        $params['status'] = SalonItem::STATUS_OF_DOWN;
        
        $itemObj = self::search($params);
        
        //页数
        $page = isset($params['page'])?max(intval($params['page']),1):1;
        $size = isset($params['page_size'])?max(intval($params['page_size']),1):20;
        AbstractPaginator::currentPageResolver(function() use ($page) {
            return $page;
        });
        
        $res =  $itemObj->paginate($size)->toArray();
        $datas = $res['data'];
        $norms_cat_ids = array_column($datas, "norms_cat_id");
        $format_infos = SalonItemFormats::getItemsByNormscatids($norms_cat_ids);
        $res['data'] = self::composite($datas,$format_infos);
        unset($res['next_page_url']);
        unset($res['prev_page_url']);
        return $this->success($res);
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
        $params = $this->parameters([
            'ids'=>self::T_STRING,
        ],true);
        $ids = explode(",", $params['ids']);
        $ids = array_map("intval", $ids);
        self::checkUp($ids);
        $res = SalonItem::whereIn('itemid',$ids)->where('status',SalonItem::STATUS_OF_DOWN)->update(['status'=>SalonItem::STATUS_OF_UP]);
        if($res)
        {
            return $this->success([]);
        }                
    }
    
    /**
     * @api {get} /warehouse/destroy 4.删除
     * @apiName destroy
     * @apiGroup Warehouse
     *
     * @apiParam {String} ids  要删除的id (多个逗号隔开)
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function destroy()
    {
        $params = $this->parameters([
            'ids'=>self::T_STRING,
        ],true);
        $ids = explode(",", $params['ids']);
        $ids = array_map("intval", $ids);
        $res = SalonItem::whereIn('itemid',$ids)->where('status',SalonItem::STATUS_OF_DOWN)->update(['status'=>SalonItem::STATUS_OF_DELETE]);
        if($res)
        {
            return $this->success([]);
        }
    }
    
    /**
     * @api {POST} /warehouse/import 5.导入
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
	
	/**
	 * 
	 * @param unknown $params
	 */
	public static function search($params)
	{
	    $base_fields = ['itemid','salonid','typeid','norms_cat_id','itemname','item_type','minPrice','maxPrice','minPriceOri','maxPriceOri','minPriceGroup','maxPriceGroup'];
	 //   $salon_fields=['salonid','salonname'];
	    $type_fields=['typeid','typename'];
	    $order_fields = ['itemid','salonid','typeid','norms_cat_id','itemname','item_type','minPrice','minPriceOri','minPriceGroup'];
	    
	    $base = SalonItem::select($base_fields);
	
	    if(isset($params['status']) && !empty($params['status']))
	    {
	        $base->where('status',$params['status']);
	    }
	    if(isset($params['salonid']) && !empty($params['salonid']))
	    {
	        $base->where('salonid',$params['salonid']);
	    }
	    if(isset($params['item_type']) && !empty($params['item_type']))
	    {
	        $base->where('item_type',$params['item_type']);
	    }
	    if(isset($params['typeid']) && !empty($params['typeid']))
	    {
	        $base->where('typeid',$params['typeid']);
	    }
	    if(isset($params['norms_cat_id']) && $params['norms_cat_id'] == 1)
	    {
	        $base->where('norms_cat_id','<>',0);
	    }
	    if(isset($params['exp_time']) && $params['exp_time'] == 1)
	    {
	        $base->where('exp_time','<>',0);
	    }
	    
	    if(isset($params['buylimit']) && !empty($params['buylimit']))
	    {
	        if($params['buylimit'] == 1)
	        {
	            $base->join('salon_item_buylimit',function($join){
	                $join->on('salon_item_buylimit.salon_item_id','=','salon_item.itemid')->where('salon_item_buylimit.limit_first','=',1);
	            });
	        }
	        elseif($params['buylimit'] == 2)
	        {
	            $base->join('salon_item_buylimit',function($join){
	                $join->on('salon_item_buylimit.salon_item_id','=','salon_item.itemid')->where('salon_item_buylimit.limit_invite','=',1);
	            });
	        }
	    }
	    
	    if(isset($params['itemname']) && !empty($params['itemname']))
	    {
	        $keyword = '%' . str_replace([
	            "%",
	            "_"
	        ], [
	            "\\%",
	            "\\_"
	        ], $params['itemname']) . "%";
	        $base->where('itemname','like',$keyword);
	    }
	    
// 	    $base->with([
//             'salon' => function ($q) use($salon_fields)
//             {
//                 $q->get($salon_fields);
//             }
//         ]);	 

	    $base->with([
	        'salonItemType' => function ($q) use($type_fields)
	        {
	            $q->get($type_fields);
	        }
	    ]);
	    
// 	    $base->with([
// 	        'salonNormsCat' => function ($q) use($norms_fields)
// 	        {
// 	            $q->get($norms_fields);
// 	        }
// 	    ]);

	    // 排序
	    if (isset($params['sort_key']) && in_array($params['sort_key'], $order_fields)) {
	        $order = $params['sort_key'];
	    } else {
	        $order = $order_fields[0];
	    }
	    
	    if (isset($params['sort_type']) && strtoupper($params['sort_type']) == "ASC") {
	        $order_by = "ASC";
	    } else {
	        $order_by = "DESC";
	    }
	    
	    return $base->orderBy($order, $order_by);
	}
	
	/**
	 * @param array $itemIds 要上架的salon item ids的数组
	 * @return array $result 检查的结果 或者 参数错误提示
	 */
	public static function checkUp($ids) 
	{
	    $ids = array_unique($ids);
	    $input_count = count($ids);
	    if($input_count<1)
	    {
	        return true;
	    }  
	    
	    $now_time = time();
	    $items = SalonItem::select(['itemid','itemname','exp_time','total_rep','sold'])->whereIn('itemid',$ids)->where('status',SalonItem::STATUS_OF_DOWN)->get();
	    if(empty($items))
	    {
	        throw new ApiException("要上架的项目不存在或者状态不正确",ERROR::ITEM_LOST_OR_WRONG_STATE);
	    }
	    $itemArr = $items->toArray();
	    $item_ids = array_column($itemArr, "itemid");
	    $error_ids = array_diff($item_ids, $ids);
	    if(count($error_ids)>0)
	    {
	        throw new ApiException("ids : [".implode(',', $error_ids)."] 项目不存在或者状态不正确",ERROR::ITEM_LOST_OR_WRONG_STATE);
	    }
	    
	    foreach ($itemArr as $item)
	    {
	        $id = $item['itemid'];
	        $name = $item['itemname'];
	        $exp_time = intval($item['exp_time']);
	        $total_rep = intval($item['total_rep']);
	        $sold = intval($item['sold']);
	        if($exp_time >0 && $exp_time < $now_time)
	        {
	            throw new ApiException("项目 [{$id} : $name] 有效期 [".date("Y-m-d H:i:s",$exp_time)."]应大于当前时间",ERROR::ITEM_WRONG_EXP_TIME);
	        }
	        
	        if($total_rep >0 && $total_rep < $sold)
	        {
	            throw new ApiException("项目 [{$id} : $name] 库存[{$total_rep}]应大于已售份数[{$sold}]",ERROR::ITEM_WRONG_TOTAL_REQ);
	        }
	    }
	    return true;
	}
	
	
	public static function composite($items,$formats)
	{
	    foreach($items as &$item)
	    {
	        $cat_id = $item['norms_cat_id'];
	        if(isset($formats[$cat_id]))
	        {
	            $item['salon_norms_cat'] = $formats[$cat_id];
	        }
	        else 
	        {
	            $item['salon_norms_cat'] = [];
	        }
	    }
	    return $items;
	}
}
