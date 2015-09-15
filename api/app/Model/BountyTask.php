<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
Use PDO;
Use URL;

class BountyTask extends Model {

    protected $table = 'bounty_task';
    protected $primaryKey = 'btId';
    public $timestamps = false;

    /**
     * 订单类型中的赏金单类型
     * @var unknown
     */
    CONST BOUNTY_TYPE = 2;

    /**
     * 赏金单申请退款
     * @var unknown
     */
    CONST STATUS_APPLY_REFUND = 5;

    /**
     * 赏金单退款中
     * @var unknown
     */
    CONST STATUS_IN_REFUND = 6;

    /**
     * 赏金单 退款完成
     * @var unknown
     */
    CONST STATUS_REFUND_COMPLETED = 7;

    /**
     * 赏金单 申请退款被拒绝
     * @var unknown
     */
    CONST STATUS_APPLY_FAILED = 8;

    /**
     * 赏金单 退款失败
     * @var unknown
     */
    CONST STATUS_REFUND_FAILED = 9;

    /**
     * 退款方式 银联
     * @var unknown
     */
    CONST PAYTYPE_UNION_PAY = 1;

    /**
     * 退款方式 支付宝
     * @var unknown
     */
    CONST PAYTYPE_ALIPAY = 2;

    /**
     * 退款方式 微信
     * @var unknown
     */
    CONST PAYTYPE_WECHAT = 3;

    /**
     * 退款方式 易联
     * @var unknown
     */
    CONST PAYTYPE_YILIAN = 10;

    /**
     * 退款超时设置
     * @var unknown
     */
    CONST TIME_OUT = 30;

    /**
     * 根据用户输入获取搜索条件
     * 
     * @param array $input        	
     */
    public static function getQueryByParam($input) {
        $query = Self::getQuery();
        // 是否有输入关键字搜索 
        if (!empty($input["keyword"])) {
            $val = $input ["keyword"];
            $val = addslashes($val);
            $val = str_replace(['_', '%'], ['\_', '\%'], $val);
            switch ($input ["keywordType"]) {
                case "0" : // 订单号				    
                    $query->where('btSn', 'like', '%' . $val . '%');
                    break;
                case "1" : // 用户名					
                    $query->whereIn('user_id', function($query) {
                        $query->select('user_id')
                                ->from("user")
                                ->where('username', 'like', '%' . $val . '%');
                    });
                    break;
                case "2" : // 用户手机号
                    $query->whereIn('user_id', function($query) {
                        $query->select('user_id')
                                ->from("user")
                                ->where('mobilephone', 'like', '%' . $val . '%');
                    });
                    break;
                case "3" ://商铺名称
                    $query->whereIn('salonid', function($query) {
                        $query->select('salonid')
                                ->from("salon")
                                ->where('salonname', 'like', '%' . $val . '%');
                    });
                    break;
                default:
                    return $this->error("赏金单搜索无此类别关键词！");
            }
        }
        if (!empty($input["payType"])) {
            $payType = intval($input["payType"]);
            if ($payType > 9) {
                return $this->error("赏金单搜索暂不支持该支付方式搜索！");
            }
            switch ($payType) {
                case self::PAYTYPE_ALIPAY :
                case self::PAYTYPE_UNION_PAY:
                case self::PAYTYPE_WECHAT:
                    $query->where('payType', '=', $payType);
                    break;
            }
        }

        //付款状态
        if (!empty($input["isPay"])) {
            if ($input["isPay"] > 2) {
                return $this->error("赏金单搜索付款状态不正确！");
            }
            $isPay = intval($input["isPay"]);
            $query->where('isPay', '=', $isPay);
        }

        //赏金单状态
        if (!empty($input["btStatus"])) {
            $btStatus = intval($input["btStatus"]);
            if ($btStatus > 5 && $btStatus != 9) {
                return $this->error("赏金单搜索暂不支持该赏金单状态搜索！");
            }
            if ($btStatus == 5) { //5为不满意状态
                $query->where('satisfyType', '=', 2);
            } else if ($btStatus == 4) { //已打赏时  不包括不满意的状态		    
                $query->where('btStatus', '=', $btStatus)->where('satisfyType', '=', 0)->orWhere('satisfyType', '=', 1);
            } else {
                $query->where('btStatus', '=', $btStatus);
            }
        }

//        //是否满意
//		if (!empty($input["satisfyType"])) {
//            if($input["satisfyType"]>2)
//            {
//                return $this->error("赏金单满意类型不正确！");
//            }
//		    $satisfyType = intval($input["satisfyType"]);
////		    $condition [] = "`satisfyType` = {$satisfyType}";
//            $query->where('satisfyType','=',$satisfyType);
//            
//		}
        //退款状态
        if (!empty($input["refundStatus"])) {
            if ($input["refundStatus"] > 9 || $input["refundStatus"] < 5) {
                return $this->error("赏金单查询退款状态不正确！");
            }
            $refundStatus = intval($input["refundStatus"]);
            $query->where('refundStatus', '=', $refundStatus);
        }

        //交易时间
        if (!empty($input["minTime"])) {
            $minTime = strtotime($input["minTime"]);
            if ($minTime) {
                $query->where('addTime', '>=', $minTime);
            }
        }
        if (!empty($input["maxTime"])) {
            $maxTime = strtotime($input["maxTime"]);
            if ($maxTime) {
                $maxTime += 86399;
                $query->where('addTime', '<=', $maxTime);
            }
        }
        return $query;
    }

    public static function getCount($query) {
        $count = $query->count();
        return $count;
    }

    /**
     * 赏金单列表搜索显示数据
     * @param query $query        	
     * @param int $page        	
     * @param int $size        	
     * @param string $order_by        	
     */
    public static function search($query, $page, $size, $sortKey, $sortType) {

        $offset = ($page - 1) * $size;
        if ($offset < 0) {
            $offset = 0;
        }
        //赏金单
        DB::enableQueryLog();
        DB::connection()->setFetchMode(PDO::FETCH_ASSOC);
        //导出查询
        if($size<0)
        {
            $bountys = $query->orderBy($sortKey, $sortType)->get();
        }
        //列表查询
        else
        {
            $bountys = $query->orderBy($sortKey, $sortType)->skip($offset)->take($size)->get();
        }       
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info('Bounty getList getQueryLog is: ', $last_query);
        //相关的用户信息
        $uids = Utils::get_column_array("userId", $bountys);
        $uids = array_unique($uids);
        $users = [];
        if (count($uids) > 0) {
            $users = User::getUsersByIds($uids);
        }
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info('Bounty getList getQueryLog is: ', $last_query);
        //造型师信息
        $hairstylistIds = Utils::get_column_array("hairstylistId", $bountys);
        $hairstylistIds = array_map("intval", $hairstylistIds);
        $hairstylistIds = array_unique($hairstylistIds);
        $hairstylists = [];
        if (count($hairstylistIds) > 0) {
            $hairstylists = Hairstylist::getHairstylistsByIds($hairstylistIds);
        }
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info('Bounty getList getQueryLog is: ', $last_query);
        //店铺信息
        $salon_ids = Utils::get_column_array("salonId", $bountys);
        $salon_ids = array_map("intval", $salon_ids);
        $salon_ids = array_unique($salon_ids);
        $salons = [];
        if (count($salon_ids) > 0) {
            $salons = Salon::getsalonsByIds($salon_ids);
        }
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info('Bounty getList getQueryLog is: ', $last_query);
        //支付流水
        $bountySn = Utils::get_column_array("btSn", $bountys);
        $bountySn = array_unique($bountySn);
        $flows = [];
        if (count($bountySn) > 0) {
            $flows = PaymentLog::getPaymentLogsBySns($bountySn);
        }
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info('Bounty getList getQueryLog is: ', $last_query);
        $items = self::compositeAll($bountys, $users, $salons, $flows, $hairstylists);
        return $items;
    }

    /**
     * 组合显示信息
     * @param unknown $bountys
     * @param unknown $users
     */
    public static function compositeAll($bountys, $users, $salons, $flows, $hairstylists) {
        $users = Utils::column_to_key("user_id", $users);
        $flows = Utils::column_to_key("ordersn", $flows);
        $salons = Utils::column_to_key("salonid", $salons);
        $hairstylists = Utils::column_to_key("stylistId", $hairstylists);
        foreach ($bountys as &$bounty) {

            $bounty['userName'] = "";
            $bounty['userMobile'] = "";
            $bounty['hairStylistMobile'] = "";
            $bounty['tn'] = "";
            $bounty['salonName'] = "";

            $id = $bounty['btId'];
            $uid = $bounty['userId'];
            $bounty_sn = $bounty['btSn'];
            $salon_id = $bounty['salonId'];
            $hairstylistId = $bounty['hairstylistId'];
//			$url = U("/Bounty/detail/no/{$bounty_sn}");           
//			$refund_url = U("/Bounty/refund_detail/no/{$bounty_sn}");
            $url = URL::action('Bounty\BountyController@detail');
            $refund_url = URL::action('Bounty\BountyController@refundDetail');
            $bounty["operations"] = "<a href=\"{$url}\" target=\"_blank\">查看</a>";
            $bounty["refund_operations"] = "<a href=\"{$refund_url}\" target=\"_blank\">查看</a>";
            $status = intval($bounty['refundStatus']);

            $tmp = self::compositeSingle($bounty);
            $bounty = array_merge($bounty, $tmp);
            if (isset($users[$uid])) {
                $bounty['userName'] = $users[$uid]['username'];
                $bounty['userMobile'] = $users[$uid]['mobilephone'];
            }

            if (isset($salons[$salon_id])) {
                $bounty['salonName'] = $salons[$salon_id]['salonname'];
            }

            if (isset($flows[$bounty_sn])) {
                $bounty['tn'] = $flows[$bounty_sn]['tn'];
            }

            if (isset($hairstylists[$hairstylistId])) {
                $bounty['hairStylistMobile'] = $hairstylists[$hairstylistId]['mobilephone'];
            }

            if ($status == self::STATUS_APPLY_REFUND) {
                $bounty["refund_operations"] .= "&nbsp;&nbsp;<a href=\"javascript:;\" class=\"accept_refund\" refund_id=\"{$id}\">通过</a>";
                $bounty["refund_operations"] .= "&nbsp;&nbsp;<a href=\"javascript:;\" class=\"reject_refund\" refund_id=\"{$id}\">拒绝</a>";
            }

            if ($status == self::STATUS_IN_REFUND) {
                $bounty["refund_operations"] .= "&nbsp;&nbsp;<a href=\"javascript:;\" class=\"accept_refund\" refund_id=\"{$id}\">重新退款</a>";
            }
        }
        return $bountys;
    }

    /**
     * 组合显示信息 (单条)
     * @param unknown $bountys
     */
    public static function compositeSingle($task) {
        $item = [];
        $item['payType'] = self::getPayName($task['payType']);
        $item['isPay'] = self::getIsPayStr($task['isPay']);
        $status = self::getBtStatusName($task['btStatus']);
        $item['btStatus'] = self::getBtStatusStr($task['btStatus'], $task['satisfyType']);
        $item['selectType'] = self::getSelectTypeName($task['selectType']);
        $satisfy = self::getSatisfyTypeStr($task['satisfyType']);
        $item['refundStatus'] = self::getStatusName($task['refundStatus']);

        if ($task['btStatus'] == 4 && $task['satisfyType'] == 2) {
            $item['btStatus'] = "不打赏";
        }
        if (!empty($task['addTime'])) {
            $item['addTime'] = date("Y-m-d H:i:s", intval($task['addTime']));
        } else {
            $item['addTime'] = "";
        }
        if (!empty($task['payTime'])) {
            $item['payTime'] = date("Y-m-d H:i:s", intval($task['payTime']));
        } else {
            $item['payTime'] = "";
        }

        if (!empty($task['selectTime'])) {
            $item['selectTime'] = date("Y-m-d H:i:s", intval($task['selectTime']));
        } else {
            $item['selectTime'] = "";
        }
        if (!empty($task['serviceTime'])) {
            $item['serviceTime'] = date("Y-m-d H:i:s", intval($task['serviceTime']));
        } else {
            $item['serviceTime'] = "";
        }
        if (!empty($task['endTime'])) {
            $item['endTime'] = date("Y-m-d H:i:s", intval($task['endTime']));
        } else {
            $item['endTime'] = "";
        }
        return $item;
    }

    /**
     * 计算总金额
     * @param unknown $bountys
     */
    public static function getAmount($bountys) {
        $amount = 0;
        foreach ($bountys as $bounty) {
            $amount += floatval($bounty['money']);
        }
        return $amount;
    }

    /**
     * 获取付款方式信息
     * @param int $pay_id
     */
    public static function getPayName($pay_id) {
        $pay_id = intval($pay_id);
        $name = "";
        switch ($pay_id) {
            case self::PAYTYPE_ALIPAY:
                $name = "支付宝";
                break;
            case self::PAYTYPE_UNION_PAY:
                $name = "银行卡";
                break;
            case self::PAYTYPE_WECHAT:
                $name = "微信";
                break;
        }
        return $name;
    }

    public static function getSelectTypeName($selectType) {
        $selectType = intval($selectType);
        $name = "";
        switch ($selectType) {
            case 1:
                $name = "自己选";
                break;
            case 2:
                $name = "臭美代选";
                break;
            case 3:
                $name = "为我服务过的";
                break;
        }
        return $name;
    }

    public static function getSatisfyTypeStr($satisfyType) {
        $satisfyType = intval($satisfyType);
        $name = "";
        switch ($satisfyType) {
            case 1:
                $name = "满意";
                break;
            case 2:
                $name = "不满意";
                break;
        }
        return $name;
    }

    public static function getIsPayStr($isPay) {
        $isPay = intval($isPay);
        $name = "";
        switch ($isPay) {
            case 1:
                $name = "未支付";
                break;
            case 2:
                $name = "已支付";
                break;
        }
        return $name;
    }

    public static function getBtStatusStr($status, $satisfyType) {
        $status = intval($status);
        $name = "";
        switch ($status) {
            case 1:
                $name = "待抢单";
                break;
            case 2:
                $name = "待服务";
                break;
            case 3:
                $name = "已服务";
                break;
            case 4:
                $name = "已打赏";
                break;
            case 9:
                $name = "取消";
                break;
        }
        return $name;
    }

    public static function getBtStatusName($status) {
        $status = intval($status);
        $name = "";
        switch ($status) {
            case 1:
                $name = "未完成";
                break;
            case 2:
                $name = "未完成";
                break;
            case 3:
                $name = "已完成";
                break;
            case 4:
                $name = "已完成";
                break;
            case 9:
                $name = "取消";
                break;
        }
        return $name;
    }

    public static function getHairstyGradeName($grade) {
        $grade = intval($grade);
        $name = "";
        switch ($grade) {
            case 1:
                $name = "高级设计师";
                break;
            case 2:
                $name = "资深设计师";
                break;
            case 3:
                $name = "设计总监";
                break;
            case 4:
                $name = "美发大师";
                break;
        }
        return $name;
    }

    public static function getSalonBountyTypeName($bountyType) {
        $bountyType = intval($bountyType);
        $name = "";
        switch ($bountyType) {
            case 1:
                $name = "店铺类型A";
                break;
            case 2:
                $name = "店铺类型B";
                break;
            case 3:
                $name = "店铺类型C";
                break;
            case 4:
                $name = "店铺类型D";
                break;
        }
        return $name;
    }

    /**
     * 获取对应状态信息
     * @param unknown $status
     * @return string
     */
    public static function getStatusName($status) {
        $status = intval($status);
        $name = "";
        switch ($status) {
            case self::STATUS_APPLY_REFUND:
                $name = "申请退款";
                break;
            case self::STATUS_IN_REFUND:
                $name = "退款中";
                break;
            case self::STATUS_REFUND_COMPLETED:
                $name = "已退款";
                break;
            case self::STATUS_APPLY_FAILED:
                $name = "拒绝退款";
                break;
            case self::STATUS_REFUND_FAILED:
                $name = "失败";
                break;
        }
        return $name;
    }

    /**
     * 赏金单详情显示数据
     * 
     * @param unknown $id        	
     */
    public static function detail($id) {

        DB::connection()->setFetchMode(PDO::FETCH_ASSOC);
        $task = self:: getBountyTaskBySn($id);
        if (!$task) {
            return null;
        }
        $payment = PaymentLog::getBountyPaymentLogBySn($id, self::BOUNTY_TYPE);

        $uid = $task['userId'];
        $salon_id = $task['salonId']; //店铺
        $hairstylist_id = $task['hairstylistId']; //发型师
        $district_id = $task['district']; //区域
        $task['district_id'] = $district_id;
        $zone = $task['zone']; //区域
        $user = [];
        $salon = [];
        $hairsty = [];
        $district = [];
        $salon_area = [];
        if (!empty($uid)) {
            $user = User::getUserById($uid);
        }
        if (!empty($salon_id)) {
            $salon = Salon::getSalonById($salon_id);
        }
        if (!empty($hairstylist_id)) {
            $hairsty = Hairstylist::getHairstylistById($hairstylist_id);
        }

        if (!empty($district_id)) {
            $district = Town::getTownById($district_id);
        } else if ($district_id == 0) {
            $district['tname'] = "全城全区";
        }

        //商圈
        if (!empty($zone)) {
            $salon_area = SalonArea::getSalonAreaById($zone);
        }

        $salon['bountyType'] = self::getSalonBountyTypeName($salon['bountyType']);
        $hairsty['grade'] = self::getHairstyGradeName($hairsty['grade']);

        $task_tmp = self::compositeSingle($task);

        $task = array_merge($task, $task_tmp);
        $task['user'] = $user;
        $task['salon'] = $salon;
        $task['hairsty'] = $hairsty;
        $task['district'] = $district;
        $task['salon_area'] = $salon_area;

        $detail['btId'] = $task['btId'];
        $detail['btSn'] = $task['btSn'];

        $detail['needsStr'] = $task['needsStr'];
        $detail['remark'] = $task['remark'];
        $detail['selectType'] = $task['selectType'];
        $detail['payType'] = $task['payType'];
        $detail['money'] = $task['money'];
        $detail['isPay'] = $task['isPay'];
        $detail['addTime'] = $task['addTime'];
        $detail['payTime'] = $task['payTime'];
        $detail['endTime'] = $task['endTime'];
        $detail['btStatus'] = $task['btStatus'];

        if (!empty($user)) {
            $detail['userName'] = $user['username'];
            $detail['userMobile'] = $user['mobilephone'];
        }
        if (!empty($payment)) {
            $detail['tn'] = $payment['tn'];
        }
        if (!empty($salon)) {
            $detail['salonName'] = $salon['salonname'];
            $detail['bountyType'] = $salon['bountyType'];
        }

        $detail['district'] = $district['tname'];

        if (!empty($hairsty)) {
            $detail['grade'] = $hairsty['grade'];
            $detail['stylistName'] = $hairsty['stylistName'];
            $detail['hairStylistMobile'] = $hairsty['mobilephone'];
        }
        return $detail;
    }

    public static function refundDetail($id) {
        DB::connection()->setFetchMode(PDO::FETCH_ASSOC);
        $task = self:: getBountyTaskBySn($id);
        if (!$task) {
            return null;
        }
        $payment = PaymentLog::getBountyPaymentLogBySn($id, self::BOUNTY_TYPE);
        $uid = $task['userId'];
        $salon_id = $task['salonId']; //店铺

        $user = [];
        $salon = [];

        if (!empty($uid)) {
            $user = User::getUserById($uid);
        }
        if (!empty($salon_id)) {
            $salon = Salon::getSalonById($salon_id);
        }

        $task_tmp = self::compositeSingle($task);
        $task = array_merge($task, $task_tmp);
        $task['refund_status'] = self::getStatusName($task['refundStatus']);

        $refundDetail['btID'] = $task['btId'];
        $refundDetail['btSn'] = $task['btSn'];
        $refundDetail['btStatus'] = $task['btStatus'];
        $refundDetail['endTime'] = $task['endTime'];
        $refundDetail['payType'] = $task['payType'];
        $refundDetail['money'] = $task['money'];
        $refundDetail['refundStatus'] = $task['refundStatus'];

        if (!empty($payment)) {
            $refundDetail['tn'] = $payment['tn'];
        }

        if (!empty($user)) {
            $refundDetail['userName'] = $user['username'];
            $refundDetail['userMobile'] = $user['mobilephone'];
        }
        $refundDetail['refundType'] = "原路返还";
        return $refundDetail;
    }

    public static function getBountyTaskBySn($btSn) {
        $bountyTask = Self::getQuery()->where("btSn", "=", $btSn)->get();
        if (empty($bountyTask)) {
            return [];
        } else {
            return $bountyTask[0];
        }
    }

}
