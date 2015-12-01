<?php

namespace App\Http\Controllers\Beauty;

use App\Http\Controllers\Controller;
use Event;
use App\Exceptions\ERROR;
use App\Exceptions\ApiException;
use App\Beauty;

class BeautyController extends Controller {

    /**
     * @api {post} /beauty/index 1.定妆中心介绍
     * @apiName index
     * @apiGroup  beauty
     *
     *
     * @apiSuccess {String} title 名称.
     * @apiSuccess {String} content 介绍内容.
     * @apiSuccess {String} image 图片,json格式的图片路径.
     *
     * @apiSuccessExample Success-Response:
     * 	    {
     *          "result": 1,
     *          "token": "",
     *          "data": "[
     *          {
     *              \"title\": \"中心介绍\",
     *              \"content\": \"您好！这里是中心介绍！\",
     *              \"image\": [
     *              {
     *                  \"img\": \"http: //sm.choumei.cn/Uploads/salonbrand/2015-06-15/143434957914358.jpg\"
     *              },
     *              {
     *                  \"img\": \"http: //sm.choumei.cn/Uploads/salonbrand/2015-06-15/163434957914352.jpg\"
     *              }
     *               ]
     *              },
     *          {
     *              \"title\": \"专家介绍\",
     *              \"content\": \"您好！这里是专家介绍！\",
     *              \"image\": [
     *              {
     *                  \"img\": \"http: //sm.choumei.cn/Uploads/salonbrand/2015-06-15/143434957914358.jpg\"
     *              },
     *              {
     *                  \"img\": \"http: //sm.choumei.cn/Uploads/salonbrand/2015-06-15/163434957914352.jpg\"
     *               }
     *              ]
     *          ]"
     *      }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "参数有误！"
     * 		}
     */
    function index() {

        $bountys = Beauty::getBeauty();
        return $this->success($bountys->detail);
    }

    /**
     * @api {post} /beauty/edit 2.定妆中心介绍编辑
     * @apiName edit
     * @apiGroup  beauty
     *
     * @apiSuccess {String} msg 提交信息
     * 
     *
     *
     * @apiSuccessExample Success-Response:
     *      {
     *      "result": 1,
     *      "token": "",
     *      "data":{
     *          "msg": "保存失败！"
     *           }
     *      }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "message": "参数有误！"
     * 		}
     */
    function edit() {
        $param = $this->param;
        if (!isset($param["data"])) {
            throw new ApiException('请输入更新数据！', ERROR::BOUNTY_ID_NOT_PASS);
        }
        $data = $param["data"];
        $result = Beauty::where('beauty_id', '=', 1)->update(array("detail" => $data));
        if ($result) {
            Event::fire('beauty.edit','定妆中心介绍内容编辑为:'.$data);
            $msg['msg'] = "保存成功！";
        } else {
            $msg['msg'] = "保存失败！";
        }
        return $this->success($msg);
    }

    /**
     * @api {post} /beauty/delete 3.定妆中心介绍删除
     * @apiName delete
     * @apiGroup  beauty
     *
     * @apiSuccess {String} msg 提交信息
     * 
     *
     *
     * @apiSuccessExample Success-Response:
     *      {
     *      "result": 1,
     *      "token": "",
     *      "data":{
     *          "msg": "保存失败！"
     *           }
     *      }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "message": "参数有误！"
     * 		}
     */
    function delete() {
        $param = $this->param;
        if (!isset($param["data"])) {
            throw new ApiException('请输入更新数据！', ERROR::BOUNTY_ID_NOT_PASS);
        }
        $data = $param["data"];
        $result = Beauty::where('beauty_id', '=', 1)->update(array("detail" => $data));
        if ($result) {
            Event::fire('beauty.delete','定妆中心介绍内容删除为:'.$data);
            $msg['msg'] = "删除成功！";
        } else {
            $msg['msg'] = "删除失败！";
        }
        return $this->success($msg);
    }

}
