<?php

namespace App\Http\Controllers\Banner;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\AbstractPaginator;
use App\Banner;
use App\Exceptions\ERROR;
use App\Exceptions\ApiException;
use Event;


class BannerController extends Controller {

    /**
     * @api {post} /banner/index 1.主页或项目banner列表
     * @apiName index
     * @apiGroup  Banner
     *
     * @apiParam {Number} type 必填,'1' 代表主页banner  '2' 代表项目banner.
     * 
     * 
     * @apiSuccessExample Success-Response:
     * {
     *       "result": 1,
     *       "token": "",
     *       "data":
     *              {
     *                   "total": 48,
     *                   "per_page": 20,
     *                   "current_page": 1,
     *                   "last_page": 3,
     *                   "from": 1,
     *                   "to": 20,
     *                   "data":[
     *                               {
     *                                   "banner_id": 1,
     *                                   "type": 1,
     *                                   "name": "无痛水光针",
     *                                   "image": "http://img01.choumei.cn/1/7/2015102714041445925884600748229.jpg",
     *                                   "behavior": 1,
     *                                   "url": "http://img01.choumei.cn/1/7/2015102714041445925884600748229.jpg",
     *                                   "created_at": 1448871460,
     *                                   "updated_at": 1448871460
     *                               },
     *                               .......
     *                          ]
     *              }
     * }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "查询banner列表失败"
     * 		}
     */
    public function index() {
        $param = $this->param;
        if (empty($param['type'])) {
            throw new ApiException('参数不齐', ERROR::MERCHANT_ERROR);
        }
        $page = isset($param['page']) ? max($param['page'], 1) : 1;
        $page_size = isset($param['page_size']) ? $param['page_size'] : 20;
        //手动设置页数
        AbstractPaginator::currentPageResolver(function() use ($page) {
            return $page;
        });

        $query = Banner::where('type', '=', $param['type'])->paginate($page_size)->toArray();
        unset($query['next_page_url']);
        unset($query['prev_page_url']);
        if ($query) {
            return $this->success($query);
        } else {
            throw new ApiException('查询banner失败', ERROR::MERCHANT_STYLIST_UPDATE_ERROR);
        }
    }

    /**
     * @api {post} /banner/create 2.主页或项目banner的添加
     * @apiName create
     * @apiGroup  Banner
     *
     * @apiParam {Number} type 必填,'banner类型 '1'主页banner； '2'项目banner'.
     * @apiParam {String} name 必填,题目.
     * @apiParam {String} image 必填,bnnaer图片的路径.
     * @apiParam {Number} behavior 必填,'链接到哪里 1H5； 2app内部； 3无跳转'(单选按钮),
     * @apiParam {Number} url (1,2)必填 3不填, banner链接地址.
     * 
     * 
     * @apiSuccessExample Success-Response:
     * 	{
     * 	    "result": 1,
     * 	    "msg": "",
     * 	    "data": {
     * 	    }
     * 	}
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "创建banner失败"
     * 		}
     */
    public function create() {
        $param = $this->param;
        if (empty($param['type']) || !isset($param['name']) || !isset($param['image']) || empty($param['behavior'])) {
            throw new ApiException('参数不齐', ERROR::MERCHANT_ERROR);
        }
        if ($param['behavior'] == 1 || $param['behavior'] == 2) {
            if (!isset($param['url'])) {
                throw new ApiException('参数不齐', ERROR::MERCHANT_ERROR);
            }
        }
        $param['created_at'] = time();
        $param['updated_at'] = time();
        $query = Banner::create($param);
        $id=$query->banner_id;
        if ($query) {
            Event::fire('banner.create','主键:'.$id);
            return $this->success();
        } else {
            throw new ApiException('创建banner失败', ERROR::MERCHANT_STYLIST_UPDATE_ERROR);
        }
    }

    /**
     * @api {post} /banner/edit/:id 3.主页或项目banner的修改
     * @apiName edit
     * @apiGroup  Banner
     *
     * @apiParam {Number} id 必填,主键.
     * @apiParam {String} name 必填,题目.
     * @apiParam {String} image 必填,bnnaer图片的路径.
     * @apiParam {Number} behavior 必填,'链接到哪里 1H5； 2app内部； 3无跳转'(单选按钮),
     * @apiParam {Number} url (1,2)必填 3不填, banner链接地址.
     * 
     * 
     * @apiSuccessExample Success-Response:
     * 	{
     * 	    "result": 1,
     * 	    "msg": "",
     * 	    "data": {
     * 	    }
     * 	}
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "修改banner失败"
     * 		}
     */
    public function edit($id) {
        $param = $this->param;
        $banner = Banner::find($id);
        if ($banner == FALSE) {
            throw new ApiException('找不到这样的banner，id有误', ERROR::MERCHANT_STYLIST_ID_ERROR);
        }
        if (!isset($param['name']) || !isset($param['image']) || empty($param['behavior'])) {
            throw new ApiException('参数不齐', ERROR::MERCHANT_ERROR);
        }
        if ($param['behavior'] == 1 || $param['behavior'] == 2) {
            if (!isset($param['url'])) {
                throw new ApiException('参数不齐', ERROR::MERCHANT_ERROR);
            }
        }
        $param['updated_at'] = time();
        $query = Banner::find($id)->update($param);
        if ($query) {
            Event::fire('banner.edit','主键:'.$id);
            return $this->success();
        } else {
            throw new ApiException('修改banner失败', ERROR::MERCHANT_STYLIST_UPDATE_ERROR);
        }
    }

    /**
     * @api {post} /banner/destroy/:id 4.主页或项目banner的删除
     * @apiName destroy
     * @apiGroup  Banner
     *
     * @apiParam {Number} id 必填,主键.
     * 
     * 
     * @apiSuccessExample Success-Response:
     * 	{
     * 	    "result": 1,
     * 	    "msg": "",
     * 	    "data": {
     * 	    }
     * 	}
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "删除banner失败"
     * 		}
     */
    public function destroy($id) {
        $banner = Banner::find($id);
        if ($banner == FALSE) {
            throw new ApiException('找不到这样的banner，id有误', ERROR::MERCHANT_STYLIST_ID_ERROR);
        }
        $query = Banner::destroy($id);
        if ($query) {
            Event::fire('banner.destroy','主键:'.$id);
            return $this->success();
        } else {
            throw new ApiException('删除banner失败', ERROR::MERCHANT_STYLIST_UPDATE_ERROR);
        }
    }

}
