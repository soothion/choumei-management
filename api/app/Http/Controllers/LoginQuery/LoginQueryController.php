<?php  namespace App\Http\Controllers\LoginQuery;

use App\Http\Controllers\Controller;
use App\RequestLog;
use Log;

/**
 * Description of LoginQueryController
 *
 * @author zhengjiangang
 */

$fields=['mobilephone','username','bundle','updateTime','deviceOs','version'];
   /**
	 * @api {post} /LoginQuery/index 1.登录日志列表
	 * 
	 * @apiName index
	 * @apiGroup LoginQuery
	 *
	 * @apiParam {String} mobilephone 可选,用户手机号.
	 * @apiParam {String} username 可选,用户臭美号.
	 * @apiParam {String} bundle 可选,用户设备号.
	 * @apiParam {String} minTime 可选,最小时间.
    	 * @apiParam {String} maxTime 可选,最大时间.
	 * @apiParam {Number} page 可选,页数.
	 * @apiParam {Number} page_size 可选,分页大小.
	 * 
	 * @apiSuccess {Number} total 总数据量.
	 * @apiSuccess {Number} per_page 分页大小.
	 * @apiSuccess {Number} current_page 当前页面.
	 * @apiSuccess {Number} last_page 当前页面.
	 * @apiSuccess {Number} from 起始数据.
	 * @apiSuccess {Number} to 结束数据.
	 * @apiSuccess {String} mobilephone 可选,用户手机号.
	 * @apiSuccess {String} username 可选,用户臭美号.
	 * @apiSuccess {String} bundle 可选,用户设备号.
	 * @apiSuccess {String} updateTime 登录时间.
	 * @apiSuccess {String} deviceOs 手机系统.
	 * @apiSuccess {String} version APP版本.
         * 
         * 
         * @apiSuccessExample Success-Response:
         * {
         *      "result":1,
         *      "token":"",
         *      "data":
         *          {
         *              "total":1,
         *              "per_page":20,
         *              "current_page":1,
         *              "last_page":1,
         *              "from":1,"to":1,
         *              "data":[
         *                      {
         *                           "mobilephone":"15102011866",
         *                           "username":"10000000",
         *                           "bundle":"100000",
         *                           "updateTime":"0000-00-00 00:00:00",
         *                           "deviceOs":"ios,
         *                           "version":"5.4.2"
         *                      }
         *                   ]
         *           }
         * }

         *  @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
    */
class LoginQueryController  extends Controller {
     public function index()
    {
           $param = $this->param; 
           Log::info('LoginQueryController index param is: ', $param);
           $query=RequestLog::getLogSelect($param);
           return $this->success($query);
     }
}