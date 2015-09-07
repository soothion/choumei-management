<?php namespace App\Http\Controllers\Image;

use App\Http\Controllers\Controller;
use App\ImageStyle;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;

/**
 * Description of ImageStyleController
 *
 * @author zhengjiangang
 */
class ImageStyleController extends Controller{
     /**
	 * @api {post} /ImageStyle/getAllImage 1.图片列表
	 * @apiGroup Image
	 *
	 * @apiParam {Number} style 可选,风格.
	 * @apiParam {Number} length 可选,长度.
	 * @apiParam {Number} curl 可选,卷度.
	 * @apiParam {Number} color 可选,颜色.
	 * @apiParam {Number} page 可选,页数.
	 * @apiParam {Number} page_size 可选,分页大小.
	 * 
	 * @apiSuccess {Number} total 总数据量.
	 * @apiSuccess {Number} per_page 分页大小.
	 * @apiSuccess {Number} current_page 当前页面.
	 * @apiSuccess {Number} last_page 当前页面.
	 * @apiSuccess {Number} from 起始数据.
	 * @apiSuccess {Number} to 结束数据.
         * @apiSuccess {Number} id  主键.
	 * @apiSuccess {Number} style 风格.
	 * @apiSuccess {Number} length 长度.
	 * @apiSuccess {Number} curl 卷度.
	 * @apiSuccess {Number} color 颜色.
	 * @apiSuccess {String} img 图片路径
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 * 
	 *	{
	 *	    "result": 1,
	 *	    "data": {
	 *	        "total": 5,
	 *	        "per_page": 20,
	 *	        "current_page": 1,
	 *	        "last_page": 1,
	 *	        "from": 1,
	 *	        "to": 5,
	 *	        "data": [
	 *	            {"id":3,
         *                   "style":1,
         *                   "length":1,
         *                   "curl":1,
         *                   "color":1,
         *                   "img":"1"
	 *	            }
	 *	        ]
	 *	    }
	 *	}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 */
       
    public function getAllImage()
    {
           $param = $this->param; 
           $query =ImageStyle::getAllImage($param);
           return $this->success($query);
     }
     
     
     /**
	 * @api {post} ImageStyle/insertImage 2.添加风格
	 * @apiName insertImage
	 * @apiGroup Image
	 * @apiParam {Number} style 必填,风格.
	 * @apiParam {Number} length 必填,长度.
	 * @apiParam {Number} curl 必填,卷度.
	 * @apiParam {Number} color 必填,颜色.
	 * @apiParam {String} img 必填,图片路径.
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "msg": "",
	 *	    "data": {
	 *	    }
	 *	}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "图片风格插入失败"
	 *		}
	 */
    public function insertImage()
    {
          $param = $this->param; 
          $data=[];
          $data['style']=$param['style'];
          $data['length']=$param['length'];
          $data['curl']=$param['curl'];
          $data['color']=$param['color'];
          $data['img']=$param['img'];
          $data['status']=1;
          $result=ImageStyle::insertImage($data);
          if($result){
			return $this->success();
          }else{
		throw new ApiException('图片风格插入失败', ERROR::REBATE_CREATE_FAILED);
          }
     }
     /**
	 * @api {post} /ImageStyle/deleteImage 4.停用风格
	 * @apiName Image
	 * @apiGroup  Image
	 *
	 *@apiParam {Number} id 必填,主键.
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "msg": "",
	 *	    "data": {
	 *	    }
	 *	}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "图片风格删除失败"
	 *		}
	 */
    public function deleteImage()
    {  
           $param = $this->param; 
           $id=$param['id'];
           $result=ImageStyle::deleteImage($id);
           if($result){
			return $this->success();
            }else{
                  throw new ApiException('图片风格删除失败', ERROR::REBATE_DELETE_FAILED);
            }
     }
     
       /**
	 * @api {post} /ImageStyle/updateImage 4.更新风格
	 * @apiName Image
	 * @apiGroup  Image
	 *
	 * @apiParam {Number} id 必填,主键.
	 * @apiParam {Number} style 必填,风格.
	 * @apiParam {Number} length 必填,长度.
	 * @apiParam {Number} curl 必填,卷度.
	 * @apiParam {Number} color 必填,颜色. 
         * @apiParam {String} img 必填,图片路径.
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "msg": "",
	 *	    "data": {
	 *	    }
	 *	}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "图片风格更新失败"
	 *		}
	 */
      
    public function updateImage()
    { 
            $param = $this->param;  
            $id=$param['id'];
            $data=[];
            $data['style']=$param['style'];
            $data['length']=$param['length'];
            $data['curl']=$param['curl'];
            $data['color']=$param['color'];
            $data['img']=$param['img']; 
            $result=ImageStyle::updateImage($id, $data);
             if($result){
			return $this->success();
            }else{
                  throw new ApiException('图片风格更新失败', ERROR::REBATE_UPDATE_FAILED);
            }
     }
        /**
	 * @api {post} /ImageStyle/getOneImage 1.查找一张图片
	 * @apiGroup Image
	 *
	 * @apiParam {Number} ID 必填，主键.

         * @apiSuccess {Number} id  主键.
	 * @apiSuccess {Number} style 风格.
	 * @apiSuccess {Number} length 长度.
	 * @apiSuccess {Number} curl 卷度.
	 * @apiSuccess {Number} color 颜色.
	 * @apiSuccess {String} img 图片路径
         * 
         * @apiSuccessExample Success-Response:
         * {"result":1,
         * "token":"",
         * "data":{
         * "id":2,
         * "style":1,
         * "length":1,
         * "curl":1,
         * "color":1,
         * "img":"1"
         * }
         * }
         */
    public function getOneImage()
    {
         $param = $this->param; 
         $id=$param['id'];
         $query=ImageStyle::getOneImage($id);
         return $this->success($query);
     }
       
       
}
