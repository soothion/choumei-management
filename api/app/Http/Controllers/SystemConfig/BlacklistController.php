<?php

namespace App\Http\Controllers\SystemConfig;

use App\Http\Controllers\Controller;
use App\Blacklist;
use Event;
use Excel;
use DB;
Use PDO;
use Request;
use Storage;
use File;
use Fileentry;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Redis as Redis;
use App\Exceptions\ERROR;
use Log;

class BlacklistController extends Controller {

    /**
     * @api {post} /blacklist/index 1.黑名单列表
     * @apiName index
     * @apiGroup blacklist
     *
     * @apiParam {Number} page 可选,页码，默认为1.
     * @apiParam {Number} page_size 可选,默认为20.
     * @apiParam {String} keyword 可选,搜索关键词.
     * @apiParam {Number} keywordType 必选,搜索关键词类型，可取0 用户手机号/1 设备号/2 openid.
     * @apiParam {String} minTime 进入黑名单最小时间 YYYY-MM-DD
     * @apiParam {String} maxTime 进入黑名单最大时间 YYYY-MM-DD
     *
     * @apiSuccess {Number} total 总数据量.
     * @apiSuccess {Number} per_page 分页大小.
     * @apiSuccess {Number} current_page 当前页面.
     * @apiSuccess {Number} last_page 当前页面.
     * @apiSuccess {Number} from 起始数据.
     * @apiSuccess {Number} to 结束数据.
     * @apiSuccess {Number} id 黑名单的id.
     * @apiSuccess {String} mobilephone 手机号.
     * @apiSuccess {String} device_uuid 设备号
     * @apiSuccess {String} openid 微信openid
     * @apiSuccess {String} create_at 进入黑名单时间
     * @apiSuccess {String} note 备注
     *
     * @apiSuccessExample Success-Response:
     * {
     *       result": 1,
     *      "token": "",
     *      "data":{
     *      "total": 6,
     *       "per_page": 20,
     *      "current_page": 1,
     *      "last_page": 1,
     *      "next_page_url": null,
     *      "prev_page_url": null,
     *      "from": 1,
     *      "to": 6,
     *      "data":[
     *      {
     *      "id": 4,
     *      "mobilephone": "13856961253",
     *      "device_uuid": "",
     *      "openid": "",
     *      "created_at": "2015-11-18 17:22:14",
     *      "updated_at": "0000-00-00 00:00:00",
     *      "note": ""
     *      },...
     *                 ]
     * }
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "未授权访问"
     * 		}
     */
    public function index() {
        $param = $this->param;

        if (isset($param['page']) && !empty($param['page'])) {
            $page = $param['page'];
        } else {
            $page = 1;
        }
        if (isset($param['page_size']) && !empty($param['page_size'])) {
            $size = $param['page_size'];
        } else {
            $size = 20;
        }
        DB::connection()->setFetchMode(PDO::FETCH_ASSOC);
        $query = Blacklist::getQueryByParam($param);
        $blacklists = Blacklist::search($query, $page, $size);
        return $this->success($blacklists);
    }

    /**
     * @api {post} /blacklist/export 2.黑名单导出
     * @apiName export
     * @apiGroup blacklist
     *
     * @apiParam {Number} page 可选,页码，默认为1.
     * @apiParam {Number} page_size 可选,默认为20.
     * @apiParam {String} keyword 可选,搜索关键词.
     * @apiParam {Number} keywordType 必选,搜索关键词类型，可取0 用户手机号/1 设备号/2 openid.
     * @apiParam {String} minTime 进入黑名单最小时间 YYYY-MM-DD
     * @apiParam {String} maxTime 进入黑名单最大时间 YYYY-MM-DD
     *
     * @apiErrorExample Error-Response:
     * {
     * "result": 0,
     * "msg": "未授权访问"
     * }
     */
    public function export() {
        $param = $this->param;

        if (isset($param['page']) && !empty($param['page'])) {
            $page = $param['page'];
        } else {
            $page = 1;
        }
        if (isset($param['page_size']) && !empty($param['page_size'])) {
            $size = $param['page_size'];
        } else {
            $size = 20;
        }
        if ($param['keywordType'] == '') {
            throw new ApiException('请设置关键词类型！', 1);
        }
        DB::connection()->setFetchMode(PDO::FETCH_ASSOC);
        $query = Blacklist::getQueryByParam($param);
        $blacklists = Blacklist::search($query, $page, $size);
        $keywordName = "";

        switch ($param ["keywordType"]) {
            case "0" : // 用户手机号				
                $keywordName = "手机号";
                break;
            case "1" : // 设备号
                $keywordName = "设备号";
                break;
            case "2" ://openid
                $keywordName = "微信OpenId";
                break;
            default:
                throw new ApiException('黑名单无此类别！', 1);
        }

        $header = [
            '序号',
            $keywordName,
            '进入黑名单时间',
            '备注',
        ];
        $res = Blacklist::format_export_data($blacklists["data"], $param['keywordType']);
        if (!empty($res)) {
            Event::fire("blacklist.export");
        }
        @ini_set('memory_limit', '256M');
        $this->export_xls("黑名单列表" . date("Ymd"), $header, $res);
    }

    /**
     * @api {post} /blacklist/upload 3.上传黑名单
     * @apiName upload
     * @apiGroup blacklist
     *
     * @apiParam {File} blacklist 必填,excel文件.
     * @apiParam {Number} keywordType 必选,搜索关键词类型，可取0 用户手机号/1 设备号/3 openid.
     *
     * @apiSuccess {String} mobilephone 手机号.
     * @apiSuccess {String} device_uuid 设备号
     * @apiSuccess {String} openid 微信openid
     * @apiSuccess {String} add_time 进入黑名单时间
     * @apiSuccess {String} note 备注
     * @apiSuccess {Number} blacklistStatus 黑名单状态 0 不存在黑名单/ 1 已存在黑名单
     * @apiSuccess {Number} isMobilephone 检测手机号 0 手机号码不符合规则/ 1 手机号码不符合规则
     * @apiSuccess {String} redisKey 数据缓存key
     * @apiSuccessExample Success-Response:
     * 	    {
     * 	        "result": 1,
     * 	        "data": null
     * 	    }
     * 
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "未授权访问"
     * 		}
     */
    public function upload() {
        $param = $this->param;
        if (!isset($param['keywordType'])) {
            throw new ApiException('请设置关键词类型！', 1);
        }
        $file = Request::file('blacklist');
        if (!$file)
            throw new ApiException('请上传文件', ERROR::FILE_EMPTY);
        $extension = $file->getClientOriginalExtension();
        if (!in_array($extension, ['xls', 'xlsx']))
            throw new ApiException('请上传xls或者xlsx文件', ERROR::FILE_FORMAT_ERROR);

        $data = [];
        $redisKey = 'blacklist';
        $available = 1;
        Excel::load($file->getPathname(), function($reader)use($param, &$data, &$redisKey) {
            $reader = $reader->getSheet(0);
            $array = $reader->toArray();
            array_shift($array);
            foreach ($array as $key => $value) {
                if (empty($value[1]))
                    continue;
                switch ($param['keywordType']) {
                    case "0" : // 用户手机号				
                        $data[$key]['mobilephone'] = $value[1];
                        if (preg_match("/1[3458]{1}\d{9}$/", $value[1])) {
                            $data[$key]['isMobilephone'] = 1;
                        } else {
                            $data[$key]['isMobilephone'] = 0;
                        }

                        $data[$key]["blacklistStatus"] = Blacklist::getStatusbyUserMobile($value[1]);
                        if ($data[$key]['isMobilephone'] || $data[$key]["blacklistStatus"]) {
                            $available = 0;
                        }

                        break;
                    case "1" : // 设备号
                        $data[$key]['device_uuid'] = $value[1];
                        $data[$key]["blacklistStatus"] = Blacklist::getStatusbyUserDevice($value[1]);
                        if ($data[$key]["blacklistStatus"]) {
                            $available = 0;
                        }
                        break;
                    case "2" ://openid
                        $data[$key]['openid'] = $value[1];
                        $data[$key]["blacklistStatus"] = Blacklist::getStatusbyOpenId($value[1]);
                        if ($data[$key]["blacklistStatus"]) {
                            $available = 0;
                        }
                        break;
                    default:
                        throw new ApiException('黑名单无此类别！', 1);
                }
                $data[$key]['add_time'] = $value[2];
                $data[$key]['updated_at'] = $value[2];
                $data[$key]['note'] = $value[3];
                $redisKey = $redisKey . $value[1];
            }
        }, 'UTF-8');
        $redisKey = md5($redisKey);
        $redis = Redis::connection();
        if ($available) {
            $redis->setex($redisKey, 3600 * 24, json_encode($data));
        } else {
            $redis->setex($redisKey, 3600 * 24, 0);
        }

        $name = Blacklist::getName();
        $folder = date('Y/m/d') . '/';
        $src = $folder . $name . '.' . $extension;
        Storage::disk('local')->put($src, File::get($file));

        $result["redisKey"] = $redisKey;
        $result["data"] = $data;

        return $this->success($result);
    }

    /**
     * @api {post} /blacklist/submit 4.提交黑名单
     * @apiName submit
     * @apiGroup blacklist
     *
     * @apiParam {File} blacklist 必填,excel文件.
     * @apiParam {Number} keywordType 必选,搜索关键词类型，可取0 用户手机号/1 设备号/3 openid.
     * @apiParam {String} redisKey 必选，缓存数据key
     *
     * @apiSuccess {String} msg 提交信息
     * @apiSuccessExample Success-Response:
     * 	    {
     * 	        "result": 1,
     * 	        "data": [
     *              "msg":"黑名单提交成功!"
     *              ]
     *              
     * 	    }
     * 
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "未授权访问"
     * 		}
     */
    public function submit() {
        $param = $this->param;
        if (empty($param['redisKey'])) {
            throw new ApiException('请提供数据key！', 1);
        }
        $redis = Redis::connection();
        $data = $redis->get($param['redisKey']);
        if (!$data) {
            throw new ApiException('黑名单提交失败!', ERROR::REBATE_UPLOAD_FAILED);
        }
        $data = json_decode($data);
        Log::info('BlackList data is: ', $data);

        $result = Blacklist::insert($data);
        if ($result)
            return $this->success($data["msg"] = "黑名单导入成功!");
        throw new ApiException('黑名单提交失败!', ERROR::REBATE_UPLOAD_FAILED);
    }

    /**
     * @api {get} /blacklist/remove/{id} 5 .移出黑名单
     * @apiName remove
     * @apiGroup  blacklist
     *
     * @apiParam {Number} id 必选,黑名单的Id.	   
     *  
     * @apiSuccess {String} msg 移除信息
     *
     * @apiSuccessExample Success-Response:
     *      {
     *          result": 1,
     *          "token": "",
     *          "data":{
     *          "msg": "成功移出黑名单！"
     *      }
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    result": 0,
     *          "code": 1,
     *          "msg": "移出黑名单失败",
     *          "token": ""
     * 		}
     */
    function remove($id) {
        $id = intval($id);
        if (empty($id)) {
            throw new ApiException('找不到id！', ERROR::BOUNTY_ID_NOT_PASS);
        }
        $result = Blacklist::where('id', $id)->delete();
        if ($result) {
            $res['msg'] = "成功移出黑名单！";
            return $this->success($res);
        }
        throw new ApiException('移出黑名单失败', 1);
    }

}
