<?php

namespace App\Http\Controllers\Laisee;

use App\Http\Controllers\Controller;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;
use App\Model\LaiseeConfig;
use App\Model\Laisee;
use Illuminate\Support\Facades\DB;

class BonusController extends Controller {

    /**
     * @api {post} /bonus/index 1.红包列表
     * @apiName index
     * @apiGroup bonus
     *
     * @apiParam {String} bonusSn 可选,红包编号.
     * @apiParam {String} bonusStatus 红包状态,全部时为0;已过期 为N；进行中为Y
     * @apiParam {String} mobilephone 可选，分享手机号
     * @apiParam {String} laisee_name 可选,红包名称
     * @apiParam {String} start_time 可选，生成时间开始
     * @apiParam {String} end_time 可选，生成时间结束
     * @apiParam {Number} page 可选,页数.
     * @apiParam {Number} page_size 可选,分页大小.
     *
     * 
     * @apiSuccess {Number} total 总数据量.
     * @apiSuccess {Number} per_page 分页大小.
     * @apiSuccess {Number} current_page 当前页面.
     * @apiSuccess {Number} last_page 当前页面.
     * @apiSuccess {Number} from 起始数据.
     * @apiSuccess {Number} to 结束数据.
     * @apiSuccess {String} laisee_name 红包名称.
     * @apiSuccess {Number} order_ticket_id 评论唯一标示Id.
     * @apiSuccess {String} add_time 生成时间.
     * @apiSuccess {String} over_time 结束时间.
     * @apiSuccess {String} status 红包状态(N已过期 Y进行中).
     * @apiSuccess {String} bonusSn 红包编号.
     * @apiSuccess {Number} bonusAmount 红包总金额.
     * @apiSuccess {Number} voucherNum 现金券总数.
     * @apiSuccess {Number} receiveNum 已领现金券数量.
     * 
     * 
     * @apiSuccessExample Success-Response:
     * 
     * 	{
     * 	    "result": 1,
     * 	    "data": {
     * 	        "total": 5,
     * 	        "per_page": 20,
     * 	        "current_page": 1,
     * 	        "last_page": 1,
     * 	        "from": 1,
     * 	        "to": 5,
     * 	        "data": [
     * 	            {
     *                  "laisee_name": "顶顶顶顶",
     *                   "id": 1,
     *                   "order_ticket_id": 1307,
     *                   "add_time": "2015-10-19 16:23:22",
     *                   "over_time": "2015-10-19 16:23:22",
     *                   "status": "N",
     *                   "bonusSn": "dh1307",
     *                   "bonusAmount": 0,
     *                   "voucherNum": 0,
     *                   "receiveNum": 0
     *               }
     * 	        ]
     * 	    }
     * 	}
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "未找到相应的等级level"
     * 		}
     */
    public function index() {
        $param = $this->param;
        $data['bonusSn'] = isset($param['bonusSn']) ? $param['bonusSn'] : '';
        $data['bonusStatus'] = isset($param['bonusStatus']) ? $param['bonusStatus'] : 0;
        $data['mobilephone'] = isset($param['mobilephone']) ? $param['mobilephone'] : 0;
        $data['laisee_name'] = isset($param['laisee_name']) ? $param['laisee_name'] : 0;
        $data['start_time'] = isset($param['start_time']) ? $param['start_time'] : 0;
        $data['end_time'] = isset($param['end_time']) ? $param['end_time'] : 0;
        $page = isset($options['page']) ? max(intval($options['page']), 1) : 1;
        $size = isset($options['page_size']) ? max(intval($options['page_size']), 1) : 20;
        $laiseeList = Laisee::getLaiseeList($data, $page, $size);
        foreach ($laiseeList['data'] as &$val) {
            $val['bonusSn'] = "dh" . $val['order_ticket_id'];
            $val['bonusAmount'] = Laisee::where('order_ticket_id', $val['order_ticket_id'])->sum('value');
            $val['voucherNum'] = Laisee::where('order_ticket_id', $val['order_ticket_id'])->count('value');
            $val['receiveNum'] = Laisee::where('order_ticket_id', $val['order_ticket_id'])->whereNotNull('mobilephone')->count();
            $over_time = $val['over_time'] + $val['add_time'];
            $val['over_time'] = date("Y-m-d", $over_time) . " 23:59:59";
            $val['add_time'] = date("Y-m-d H:i:s", $val['add_time']);
        }
        return $this->success($laiseeList);
    }

    /**
     * @api {post} /bonus/export 2.导出红包
     * @apiName export
     * @apiGroup bonus
     *
     * @apiParam {String} bonusSn 可选,红包编号.
     * @apiParam {String} bonusStatus 红包状态,全部时为0;已过期 为N；进行中为Y
     * @apiParam {String} mobilephone 可选，分享手机号
     * @apiParam {String} laisee_name 可选,红包名称
     * @apiParam {String} start_time 可选，生成时间开始
     * @apiParam {String} end_time 可选，生成时间结束
     * @apiParam {Number} page 可选,页数.
     * @apiParam {Number} page_size 可选,分页大小.
     *
     * 
     * @apiSuccessExample Success-Response:
     * 	{
     * 	    "result": 1,
     * 	    "data": null
     * 	}
     *
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "未授权访问"
     * 		}
     */
    public function export() {
        $param = $this->param;
        $data['bonusSn'] = isset($param['bonusSn']) ? $param['bonusSn'] : '';
        $data['bonusStatus'] = isset($param['bonusStatus']) ? $param['bonusStatus'] : 0;
        $data['mobilephone'] = isset($param['mobilephone']) ? $param['mobilephone'] : 0;
        $data['laisee_name'] = isset($param['laisee_name']) ? $param['laisee_name'] : 0;
        $data['start_time'] = isset($param['start_time']) ? $param['start_time'] : 0;
        $data['end_time'] = isset($param['end_time']) ? $param['end_time'] : 0;
        $page = isset($options['page']) ? max(intval($options['page']), 1) : 1;
        $size = isset($options['page_size']) ? max(intval($options['page_size']), 1) : 20;
        $laiseeList = Laisee::getLaiseeList($data, $page, $size);
        $result = [];
        $num = 1;
        foreach ($laiseeList['data'] as $key => $val) {
            $result[$key]['num'] = $num;
            $result[$key]['bonusSn'] = "dh" . $val['order_ticket_id'];
            $result[$key]['laisee_name'] = $val['laisee_name'];
            $result[$key]['bonusAmount'] = Laisee::where('order_ticket_id', $val['order_ticket_id'])->sum('value');
            $result[$key]['voucherNum'] = Laisee::where('order_ticket_id', $val['order_ticket_id'])->count('value');
            $result[$key]['receiveNum'] = Laisee::where('order_ticket_id', $val['order_ticket_id'])->whereNotNull('mobilephone')->count();
            $result[$key]['add_time'] = date("Y-m-d H:i:s", $val['add_time']);
            $over_time = $val['over_time'] + $val['add_time'];
            $val['over_time'] = date("Y-m-d", $over_time) . " 23:59:59";
            $result[$key]['status'] = $val['status'] == "Y" ? "进行中" : "已过期";
            $num++;
        }
        //导出excel	   
        $title = '红包列表' . date('Ymd');
        $header = ['序号', '红包编号', '红包名称', '红包总金额', '现金券总数', '已领现金券数', '生成时间', '有效天数', '红包状态'];
        Excel::create($title, function($excel) use($data, $header) {
            $excel->sheet('Sheet1', function($sheet) use($data, $header) {
                $sheet->fromArray($data, null, 'A1', false, false); //第五个参数为是否自动生成header,这里设置为false
                $sheet->prependRow(1, $header); //添加表头
            });
        })->export('xls');
    }

    /**
     * @api {post} /bonus/show/:id 3.红包详情
     * @apiName show
     * @apiGroup bonus
     *
     * @apiParam {Number} id 必填,此值为order_ticket_id.
     *
     * @apiSuccess {String} bonusSn 红包编号.
     * @apiSuccess {String} add_time 生成时间.
     * @apiSuccess {String} over_time 到期时间.
     * @apiSuccess {String} mobilephone 红包分享用户.
     * @apiSuccess {Number} voucherNum 现金券总数.
     * @apiSuccess {Number} receiveNum 被领取现金券总数.
     * @apiSuccess {Number} receiveGiftNum 被领取大礼包.
     * @apiSuccess {Number} bonusAmount 红包总金额.
     * @apiSuccess {Array} voucher 现金券详情.
     * @apiSuccess {Array} voucherGift 礼包详情.
     * @apiSuccess {Array} receive_voucher 被领取现金券详情.
     * @apiSuccess {Array} giftUser 领取礼包用户.
     * @apiSuccess {String} status 红包状态.
     * 
     * 
     * @apiSuccessExample Success-Response:
     * {
     *   "result": 1,
     *   "token": "",
     *   "data": {
     *       "bonusSn": "hb1111",
     *      "add_time": "2015-08-26 21:12:34",
     *       "over_time": "2015-08-28 23:59:59",
     *      "mobilephone": "18617163658",
     *      "voucherNum": 8,
     *     "receiveNum": 0,
     *       "receiveGiftNum": 3,
     *     "bonusAmount": 0,
     *     "voucher": [
     *         {
     *             "useMoney": 10,
     *            "useItemTypes": "洗剪吹",
     *            "useTotalNum": 3
     *        },
     *       {
     *         "useMoney": 69,
     *         "useItemTypes": "烫发",
     *          "useTotalNum": 3
     *        }
     *    ],
     *    "receive_voucher": [],
     *    "voucherGift": [
     *       {
     *           "useMoney": 10,
     *              "useItemTypes": "",
     *              "useTotalNum": 0
     *          }
     *       ],
     *        "giftUser": [],
     *        "status": "Y"
     *       }
     *   }
     *
     * @apiErrorExample Error-Response:
     * 		{
     * 		    "result": 0,
     * 		    "msg": "未授权访问"
     * 		}
     */
    public function show($id) {
        $laisee = Laisee::where('order_ticket_id', $id)->first();
        $salonItemComment = DB::table("salon_itemcomment")->where('order_ticket_id', $id)->first();
        if (!$salonItemComment) {
            throw new ApiException('未找到相关评论信息', ERROR::UNKNOWN_ERROR);
        }
        $result['bonusSn'] = "hb" . $id;
        $result['add_time'] = date("Y-m-d H:i:s", $salonItemComment->add_time);
        if ($laisee) {
            $laiseeConfig = LaiseeConfig::where('id', $laisee->laisee_config_id)->first();
            if ($laiseeConfig) {
                $over_time = $laiseeConfig->over_time + $salonItemComment->add_time;
                $result['over_time'] = date("Y-m-d", $over_time) . " 23:59:59";
                $result['mobilephone'] = \App\User::where("user_id", $salonItemComment->user_id)->first()->mobilephone;
                $result['voucherNum'] = Laisee::whereIn("vcsn", explode(",", $laiseeConfig->vcsns))->count();
                $result['receiveNum'] = Laisee::whereIn("vcsn", explode(",", $laiseeConfig->vcsns))->whereNotNull("mobilephone")->count();
                $result['receiveGiftNum'] = Laisee::whereIn("vcsn", explode(",", $laiseeConfig->gift_vcsn))->whereNotNull("mobilephone")->count();
                $result['bonusAmount'] = Laisee::where('order_ticket_id')->sum('value');
                $result['voucher'] = Laisee::getVoucher($laiseeConfig);  //现金券详情
                $result['receive_voucher'] = Laisee::getReceiveVoucher($id, $laiseeConfig);  //被领取 现金券详情
                $result['voucherGift'] = Laisee::getGiftVoucher($laiseeConfig);  //礼包详情
                $result['giftUser'] = Laisee::getGiftUser($id, $laiseeConfig);  //领取礼包用户
                $result['status'] = $laiseeConfig->status;
                return $this->success($result);
            } else {
                throw new ApiException('未找到红包活动', ERROR::UNKNOWN_ERROR);
            }
        } else {
            throw new ApiException('未找到红包信息', ERROR::UNKNOWN_ERROR);
        }
    }

    /**
     * @api {post} /bonus/close/:id 4,红包失效
     * @apiName close
     * @apiGroup bonus
     *
     * @apiParam {Number} id 必填,此值为order_ticket_id.
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
     * 		    "msg": "未授权访问"
     * 		}
     */
    public function close($id) {
        $laisee = Laisee::where('order_ticket_id', $id)->first();
        if ($laisee) {
            $res = Laisee::where('order_ticket_id', $id)->update(['end_time' => date("Y-m-d H:i:s")]);
            if ($res !== false) {
                return $this->success();
            } else {
                throw new ApiException('红包更改为失效失败', ERROR::UNKNOWN_ERROR);
            }
        } else {
            throw new ApiException('未找到红包信息', ERROR::UNKNOWN_ERROR);
        }
    }

}