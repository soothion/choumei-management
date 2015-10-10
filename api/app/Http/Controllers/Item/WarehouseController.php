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
use App\Http\Requests\Request;
use Event;

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
     *                   "salon_norms_cat": []
     *               },
     *               {
     *                   "itemid": 44976,
     *                   "salonid": 1216,
     *                   "typeid": 10,
     *                   "norms_cat_id": 1243,
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
     *                   "salon_norms_cat": [
     *                       "性别",
     *                       "理发师",
     *                       "药水",
     *                       "发长"
     *                   ]
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
        $res['data'] = SalonItem::composite($datas,$format_infos);
        unset($res['next_page_url']);
        unset($res['prev_page_url']);
        return $this->success($res);
    }

    /**
     * @api {get} /warehouse/show/{id} 2.项目仓库详情  
     * @apiName show
     * @apiGroup Warehouse
     *
     * @apiParam {String} id 参见项目详情 http://192.168.13.46:8150/doc/#api-Item-show
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
     * @api {get} /warehouse/detail/{id} 3.项目仓库详情
     * @apiName show
     * @apiGroup Warehouse
     *
     * @apiParam {String} id 参见项目详情  http://192.168.13.46:8150/doc/#api-OnSale-show
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function detail($id)
    {
        //
    }
    
    /**
     * @api {get} /warehouse/puton 4.上架
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
        SalonItem::checkUp($ids);
        $res = SalonItem::whereIn('itemid',$ids)->where('status',SalonItem::STATUS_OF_DOWN)->update(['status'=>SalonItem::STATUS_OF_UP]);
       
        if($res)
        {
            $items = SalonItem::whereIn('itemid',$ids)->get(['itemname'])->toArray();
            $itemnames =  array_column($items, 'itemname');
            $info = "上架 [".implode(',',$itemnames)."]";
            Event::fire('warehouse.puton',$info);
        }   
        return $this->success([]);
    }
    
    /**
     * @api {get} /warehouse/destroy 5.删除
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
            $items = SalonItem::whereIn('itemid',$ids)->get(['itemname'])->toArray();
            $itemnames =  array_column($items, 'itemname');
            $info = "删除 [".implode(',',$itemnames)."]";
            Event::fire('warehouse.destroy',$info);           
        }
        return $this->success([]);
    }
    
    /**
     * @api {POST} /warehouse/import 6.导入
     * @apiName import
     * @apiGroup Warehouse
     *
     * @apiParam {String} salon_id  要导入到哪家店铺
     * @apiParam {File} item 必填,json文件.
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
	public function import()
	{
	    $params = $this->parameters([
	        'salon_id'=>self::T_INT,
	        'item'=>self::T_RAW,
	    ],true);
	    $file = Request::file('item');
	    if(!$file)
	    {
	        throw new ApiException("找不到上传的文件",ERROR::UPLOAD_FILE_LOST);
	    }
	    $extension = $file->getClientOriginalExtension();
	    if(strtolower($extension) != "json")
	    {
	        throw new ApiException('请上传json文件',ERROR::UPLOAD_FILE_ERR_EXTENSION);
	    }
	    $content_str = file_get_contents($file->getPathname());
	    $datas = @json_decode($content_str);
	    if(!is_array($datas) || count($datas)<1)
	    {
	        throw new ApiException('json文件格式不正确或者内容为空',ERROR::UPLOAD_FILE_ERR_FORMAT);
	    }
	    $salon_id = $params['salon_id'];
	    $items = self::formatDatas($datas,$salon_id);
	    foreach ($items as $item)
	    {
	        $data = ItemInfoController::compositeData($item);
	        ItemInfoController::upsert($data,$item['priceStyle']);
	    }
	    Event::fire('warehouse.import');
	    return $this->success([]);
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
	
	public static function formatDatas($datas,$salon_id)
	{
	    $res = [];
	    foreach($datas as $data)
	    {
	        if(!isset($data['typeid']) 
	            || !isset($data['useLimit'])
	            || !isset($data['logo'])
	            || !isset($data['desc'])
	            || !isset($data['item_type'])
	            || !isset($data['itemname'])
	            || !isset($data['fastGrade'])
	            || !isset($data['repertory'])
	            || !isset($data['exp_time'])
	            || !isset($data['total_rep'])
	            || !isset($data['addserviceStr'])
	            || !isset($data['timingAdded'])
	            || !isset($data['timingShelves'])
	            || !isset($data['limit_time'])
	            || !isset($data['inviteLimit'])
	            || !isset($data['limit_first'])
	            || !isset($data['prices'])
	            || !isset($data['price'])
	            || !isset($data['minPriceOri'])
	            || !isset($data['minPriceGroup'])
	            )
	        {
	            throw new ApiException('json文件格式不正确,缺少必要参数',ERROR::UPLOAD_FILE_ERR_FORMAT);
	        }
	        $tmp = [];
	        $tmp['salonid'] = $salon_id;
	        $tmp['typeid'] = $data['typeid'];
	        $tmp['useLimit'] = $data['useLimit'];
	        $tmp['logo'] = $data['logo'];
	        $tmp['desc'] = $data['desc'];
	        $tmp['itemType'] = $data['item_type'];
	        $tmp['itemname'] = $data['itemname'];
	        $tmp['fastGrade'] = $data['fastGrade'];
	        $tmp['repertory'] = $data['repertory'];
	        $tmp['expTimeInput'] = $data['exp_time'];
	        $tmp['totalRepInput'] = $data['total_rep'];
	        if(!empty($data['addserviceStr']))
	        {
	           $tmp['addedService'] = explode(',', $data['addserviceStr']);
	        }
	        $tmp['timingAdded'] = $data['timingAdded'];
	        $tmp['timingShelves'] = $data['timingShelves'];
	        
	        if(!empty($data['limit_time']))
	        {
	           $tmp['timeLimitInput'] = intval($data['limit_time']);
	        }
	        if(!empty($data['inviteLimit']))
	        {
	            $tmp['inviteLimit'] = intval($data['inviteLimit']);
	        }
	        if(!empty($data['limit_first']))
	        {
	            $tmp['firstLimit'] = intval($data['limit_first']);
	        }
	        
	        if(count($data['prices'])<1)
	        {
	            $tmp['priceStyle'] = 1;
	            $tmp['price'] = $data['minPriceOri'];
	            $tmp['priceDis'] = $data['minPrice'];
	            if(floatval($data['minPriceGroup']) > 0)
	            {
	                $tmp['priceGroup'] = floatval($data['minPriceGroup']);
	            }
	        }
	        else
	        {
	            $tmp['priceStyle'] = 2;
	            $normarr = self::formatNormDatas($data['prices']);
	            $normMenu = array_keys($normarr[0]['type']);
	            $tmp['normarr'] = json_encode($normarr,JSON_UNESCAPED_UNICODE);
	            $tmp['normMenu'] = json_encode($normMenu,JSON_UNESCAPED_UNICODE);
	        }
	        
	        $err_msg = [];
	        $ret = ItemInfoController::parametersFilter($tmp, $err_msg);
	        if(!$ret)
	        {
	            throw new ApiException($err_msg['msg'],$err_msg['no']);
	        }
	        $res[] = $tmp;
	    }
	    return $res;
	}
	
	public static function formatNormDatas($datas)
	{
	    $res = [];
	    foreach($datas as $data)
	    {
	        if(!isset($data['price']) || !isset($data['price_dis']) || !isset($data['price_group']) || !isset($data['formats']) )
	        {
	            throw new ApiException('json文件格式不正确,缺少必要参数',ERROR::UPLOAD_FILE_ERR_FORMAT);
	        }
	        $tmp = [];
	        $tmp['price'] = $data['price'];
	        $tmp['priceDis'] = $data['price_dis'];
	        $tmp['priceGroup'] = $data['price_group'];
	        $tmp['type'] = [];
	        foreach($data['formats'] as $format)
	        {
	            if(!isset($format['formats_name']) || !isset($format['format_name']) )
	            {
	                throw new ApiException('json文件格式不正确,缺少必要参数',ERROR::UPLOAD_FILE_ERR_FORMAT);
	            }
	            $key_val = $format['formats_name'];
	            $key = self::getNormKey($key_val);
	            $val = $format['format_name'];
	            $tmp['type'][$key] = $val;
	        }
	        $res[] = $tmp;
	    }
	    return $res;
	}
	
	public static function getNormKey($key_val)
	{
        $res = "";
        switch ($key_val) {
            case "性别":
                $res = "sex";
                break;
            case "造型师":
                $res = "hairstylist";
                break;
            case "药水":
                $res = "solution";
                break;
            case "发长":
                $res = "longhair";
                break;
        }
	    return $res;
	}
}
