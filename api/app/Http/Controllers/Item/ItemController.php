<?php namespace App\Http\Controllers;

use App\Item;
use Illuminate\Pagination\AbstractPaginator;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;

class ItemController extends Controller{


	/**
	 * @api {post} /list/city 1.获取城市列表
	 * @apiName city
	 * @apiGroup List
	 *
	 *
	 * @apiSuccess {Array} city 返回城市列表数组.
	 *
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "data": [
	 *	        {
	 *	            "id": 4,
	 *	            "title": "北京"
	 *	        },
	 *	        {
	 *	            "id": 2,
	 *	            "title": "广州"
	 *	        },
	 *	        {
	 *	            "id": 3,
	 *	            "title": "武汉"
	 *	        },
	 *	        {
	 *	            "id": 1,
	 *	            "title": "深圳"
	 *	        }
	 *	    ]
	 *	}
	 *
	 */
	public function index(){
		$param = $this->param;
		$query = Item::getQueryByParam($param);
		$page = isset($param['page'])?max($param['page'],1):1;
		$page_size = isset($param['page_size'])?$param['page_size']:20;

		//手动设置页数
		AbstractPaginator::currentPageResolver(function() use ($page) {
		    return $page;
		});

		$fields = array(
		    'itemid',
		    'salonid',
			'itemname',
			'typename',
			'minPrice',
			'minPriceOri',
			'minPriceGroup'
		);

		//分页
	    $result = $query->select($fields)->paginate($page_size)->toArray();
	    foreach ($result['data'] as $key=>$item) {
	    	$item->format = Item::getFormat($item->salonid);
	    }
	    unset($result['next_page_url']);
	    unset($result['prev_page_url']);
	    return $this->success($result);
	}


	public function show($id){
		$item = Item::get($id);
		if(!$item)
			throw new ApiException('未知项目ID', ERROR::ITEM_NOT_FOUND);
		return $this->success($item);
			
	}


}
?>