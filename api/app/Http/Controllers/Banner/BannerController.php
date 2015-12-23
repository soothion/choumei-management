<?php

namespace App\Http\Controllers\Banner;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\AbstractPaginator;
use App\Banner;
use App\Exceptions\ERROR;
use App\Exceptions\ApiException;
use Log;
use Event;

class BannerController extends Controller {

    /**
     * @api {post} /banner/index 1.主页banner列表    banner/index2 项目banner列表
     * @apiName index
     * @apiGroup  Banner
     *
     * @apiParam {Number} type 必填,''banner类型  ‘1’‘ 代表主页banner；’2‘ 代表定妆banner；’3‘ 代表迁体banner；’4‘ 代表水光针banner； 
     * 
     * @apiSuccess {Number} total 总数据量.
     * @apiSuccess {Number} per_page 分页大小.
     * @apiSuccess {Number} current_page 当前页面.
     * @apiSuccess {Number} last_page 当前页面.
     * @apiSuccess {Number} from 起始数据.
     * @apiSuccess {Number} to 结束数据.
     * @apiSuccess {Number} banner_id  主键.    
     * @apiSuccess {Number} type  ''banner类型  ‘1’‘ 代表主页banner；’2‘ 代表定妆banner；’3‘ 代表迁体banner；’4‘ 代表水光针banner；  
     * @apiSuccess {String} name 'banner名称',(即项目名称)
     * @apiSuccess {String} image bnnaer图片.
     * @apiSuccess {String} salonName  salon店的名称
     * @apiSuccess {Number} behavior  链接到哪里  0无跳转;1H5； 2app内部',
     * @apiSuccess {Json}    url  'banner链接地址',  (behavior为’1‘或‘3’ 类型为String ,behavior为'2'类型为json {"type":"SPM","itemId":1}且type只有四种类型：SPM - 半永久,FFA - 快时尚',salons-美发店铺主页,artificers-专家主页,itemId:主键 (SPM - 半永久,FFA - 快时尚'是itemId, ,salons-美发店铺主页-则是salonName))
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
     *                               {
     *                                   "banner_id": 60,
     *                                   "type": 1,
     *                                   "name": "无痛水光针",
     *                                   "image": "http://img01.choumei.cn/1/7/2015102714041445925884600748229.jpg",
     *                                   "behavior": 2,
     *                                   "url": {"type":"SPM","itemId":1},
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
            throw new ApiException('参数不齐', ERROR::BEAUTY_ITEM_ERROR);
        }
        $page = isset($param['page']) ? max($param['page'], 1) : 1;
        $page_size = isset($param['page_size']) ? $param['page_size'] : 20;
        //手动设置页数
        AbstractPaginator::currentPageResolver(function() use ($page) {
            return $page;
        });
            $query = Banner::where('type', '=', $param['type'])->orderBy('sort', 'asc')->orderBy('created_at', 'asc')->paginate($page_size)->toArray();
            foreach ($query['data'] as $key =>$value) {
                if(isset($value['url'])){
                    $temp = json_decode($value['url'],true);
                    if(isset($temp['salonId'])){
                        $query['data'][$key]['salonId'] = $temp['salonId'];
                    }
                }
            }
        unset($query['next_page_url']);
        unset($query['prev_page_url']);
        if ($query) {
            return $this->success($query);
        } else {
            throw new ApiException('查询banner失败', ERROR::BEAUTY_BANNER_SELECT_ERROR);
        }
    }

    /**
     * @api {post} /banner/create 2.banner的添加
     * @apiName create
     * @apiGroup  Banner
     *
     * @apiParam {Number} type 必填,  ''banner类型  ‘1’‘ 代表主页banner；’2‘ 代表定妆banner；’3‘ 代表迁体banner；’4‘ 代表水光针banner；  
     * @apiParam {String} name 必填,题目.
     * @apiParam {String} image 必填,bnnaer图片的路径.
     * @apiParam {String} salonName 可选, salon店的名称
     * @apiParam {Number} behavior 必填, 链接到哪里  0无跳转; 1H5； 2app内部',
     * @apiParam {Json}    url  'banner链接地址',  (behavior为’1‘或‘3’ 类型为String ,behavior为'2'类型为json {"type":"SPM","itemId":1}且type只有四种类型：SPM - 半永久,FFA - 快时尚',salons-美发店铺主页,artificers-专家主页,同上 )
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
        if (empty($param['type']) || !isset($param['name']) || !isset($param['image']) || !isset($param['behavior'])) {
            throw new ApiException('参数不齐', ERROR::BEAUTY_ITEM_ERROR);
        }
        if ($param['behavior'] == 1 || $param['behavior'] == 2) {
            if (empty($param['url'])) {
                throw new ApiException('参数不齐', ERROR::BEAUTY_ITEM_ERROR);
            }
        }
        $date['type']=$param['type'];
        $date['sort']=10;
        $date['name']=$param['name'];
        $date['image']=$param['image'];
        $date['behavior']=$param['behavior'];
        if(!empty($param['salonId'])){
            $temp=json_decode($param['url']);
            $temp->salonId=$param['salonId'];
            $param['url']=  json_encode($temp);
        }
        if (!empty($param['url'])) {
            $date['url']=$param['url'];
        }
        if (!empty($param['salonName'])) {
            $date['salonName']=$param['salonName'];
        }
        $date['created_at'] = time();
        $date['updated_at'] = time();
        $id = Banner::insertGetId($date);
        if ($id) {
            Event::fire('banner.create','主键:'.$id);
            return $this->success();
        } else {
            throw new ApiException('创建主页banner失败', ERROR::BEAUTY_BANNER_CREATE_ERROR);
        }
    }
    


    /**
     * @api {post} /banner/edit/:id 3.banner的修改 
     * @apiName edit
     * @apiGroup  Banner
     *
     * @apiParam {Number} id 必填,主键.
     * @apiParam {String} name 必填,题目.
     * @apiParam {String} salonName 可选, salon店的名称
     * @apiParam {String} image 必填,bnnaer图片的路径.
     * @apiParam {Number} behavior 必填, 链接到哪里  0无跳转; 1H5； 2app内部',(单选按钮),
     * @apiParam {Json}    url  'banner链接地址',  (behavior为’1‘或‘3’ 类型为String ,behavior为'2'类型为json {"type":"SPM","itemId":1}且type只有四种类型：SPM - 半永久,FFA - 快时尚',salons-美发店铺主页,artificers-专家主页,同上 )
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
            throw new ApiException('找不到这样的banner，id有误', ERROR::BEAUTY_BANNER_NOT_ID);
        }       
        if (!isset($param['name']) || !isset($param['image']) || empty($param['behavior'])) {
            throw new ApiException('参数不齐', ERROR::BEAUTY_ITEM_ERROR);
        }          
        if ($param['behavior'] == 1 || $param['behavior'] == 2) {
            if (empty($param['url'])) {
                throw new ApiException('参数不齐', ERROR::BEAUTY_ITEM_ERROR);
            }
        }
        if(!empty($param['salonId'])){
            $temp=json_decode($param['url']);
            $temp->salonId=$param['salonId'];
            $param['url']=  json_encode($temp);
        }
        if(!array_key_exists('salonName',$param)){
            $param['salonName']="";
        }
        $data['type']=$param['type'];
        $data['image']=$param['image'];
        $data['salonName']=$param['salonName'];
        $data['name']=$param['name'];
        if (isset($param['behavior'])) {
            $data['behavior']=$param['behavior'];
            if ($param['behavior'] == 0) {
                $data['url'] = "";
            }
        }
        $data['updated_at']=time();        
        if (!empty($param['url'])) {
            $data['url']=$param['url'];
        }
        $query = Banner::where('banner_id',$id)->update($data);
        if ($query) {
            Log::info("param is ",$data);
          //  Event::fire('banner.edit','主键:'.$id);
            return $this->success();
        } else {
            throw new ApiException('修改banner失败', ERROR::BEAUTY_BANNER_UPDATE_ERROR);
        }
    }

    /**
     * @api {post} /banner/destroy/:id 4.banner的删除
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
            throw new ApiException('找不到这样的banner，id有误', ERROR::BEAUTY_BANNER_NOT_ID);
        }
        $query = Banner::destroy($id);
        if ($query) {
            Event::fire('banner.destroy','主键:'.$id);
            return $this->success();
        } else {
            throw new ApiException('删除banner失败', ERROR::BEAUTY_BANNER_DELETE_ERROR);
        }
    }

    /**
     * @api {post} /banner/sort 5.banner的排序
     * @apiName sort
     * @apiGroup  Banner
     *
     * @apiParam {Json} sort 必填,顺序  [{"id":11,"sort":1},{"id":5,"sort":2}...].  排序,是sort的升序(id 是表的主键)
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
     * 		    "msg": "修改失败"
     * 		}
     */
    public function sort() {
        $param = $this->param;
        $date[] = array();
        $banner[] = array();
        $date = json_decode($param['sort']);
        for ($i = 0; $i < count($date); $i++) {
            $banner = $date[$i];
            $query1 = Banner::where('banner_id', '=', $banner->id)->first();
            if ($query1 == FALSE) {
                throw new ApiException('集合中有id不存在', ERROR::BEAUTY_BANNER_NOT_ID);
            }
        }
        for ($i = 0; $i < count($date); $i++) {
            $banner = $date[$i];
            $date2['updated_at'] = time();
            $date2['sort'] = $banner->sort;
            $query = Banner::where('banner_id', '=', $banner->id)->update($date2);
            if ($query == FALSE) {
                throw new ApiException('修改失败', ERROR::BEAUTY_BANNER_UPDATE_ERROR);
            }
        }
        return $this->success();
    }

}
