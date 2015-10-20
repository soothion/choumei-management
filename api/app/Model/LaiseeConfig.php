<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\VoucherConf;
use App\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\AbstractPaginator;

class LaiseeConfig extends Model {

    protected $table = 'laisee_config';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;

    /*
     * 添加或修改红包活动
     */

    public static function doAdd($where, $data) {
        DB::beginTransaction();
        DB::enableQueryLog();
        if ($where) {//修改
            $vcsns = self::updateVoucherConfLaisee($data);
            if ($vcsns) {
                $gift_vcsn = self::updateVoucherConfGift($data);
                $lcData = [
                    'laisee_name' => $data['laisee_name'],
                    'lc_remark' => $data['lc_remark'],
                    'status' => 'N',
                    'vcsns' => $vcsns,
                    'gift_vcsn' => !empty($gift_vcsn) ? $gift_vcsn : '',
                    'over_time' => $data['effective'] * 24 * 60 * 60, //秒数
                    'total_money' => $data['total_money'],
                    'amount_warning' => $data['amount_warning'],
                    'warning_phone' => $data['warning_phone'],
                    'share_icon' => $data['share_icon'],
                    'share_title' => $data['share_title'],
                    'share_desc' => $data['share_desc'],
                    'bonus_bg_img' => $data['bonus_bg_img'],
                    'sms_on_gained' => $data['sms_on_gained'],
                ];
                $res = self::where('id', $data['id'])->update($lcData);
                if ($res !== false) {
                    DB::commit();
                    return true;
                } else {
                    DB::rollBack();
                    return false;
                }
            }
        } else {//添加
            //先插入到voucher_conf中  非礼包
            $vcsns = self::addVoucherConfLaisee($data);
            if ($vcsns) { //插入voucher_conf 中 礼包
                $gift_vcsn = self::addVoucherConfGift($data);
                $lcData = [
                    'laisee_name' => $data['laisee_name'],
                    'lc_remark' => $data['lc_remark'],
                    'status' => 'N',
                    'vcsns' => $vcsns,
                    'gift_vcsn' => !empty($gift_vcsn) ? $gift_vcsn : '',
                    'over_time' => $data['effective'] * 24 * 60 * 60, //秒数
                    'total_money' => $data['total_money'],
                    'amount_warning' => $data['amount_warning'],
                    'warning_phone' => $data['warning_phone'],
                    'share_icon' => $data['share_icon'],
                    'share_title' => $data['share_title'],
                    'share_desc' => $data['share_desc'],
                    'bonus_bg_img' => $data['bonus_bg_img'],
                    'sms_on_gained' => $data['sms_on_gained'],
                    'create_time' => date("Y-m-d H:i:s"),
                ];
                $id = self::insertGetId($lcData);
                if ($id) {
                    DB::commit();
                    return true;
                } else {
                    DB::rollBack();
                    return false;
                }
            } else {
                DB::rollBack();
                return false;
            }
        }
    }

//先插入到voucher_conf中  非礼包
    private static function addVoucherConfLaisee($data) {
        $vUseItemTypesArr = explode(",", $data['vUseItemTypes']);
        $vUseMoneyArr = explode(",", $data['vUseMoney']);
        $vNumberArr = explode(",", $data['vNumber']);
        $vDayArr = explode(",", $data['vDay']);
        $vUseNeedMoneyArr = explode(",", $data['vUseNeedMoney']);
        $vVoucherCount = count($vUseItemTypesArr);
        $vcsns = [];
        for ($i = 0; $i < $vVoucherCount; $i++) {
            $vcSn = VoucherConf::getVcSn();
            $vcData = [
                'vcTitle' => $data['laisee_name'],
                'vcSn' => $vcSn,
                'useEnd' =>  ($vDayArr[$i] * 24 * 60 * 60),  //TODO  有效时间需要再次确认
                'useMoney' => $vUseMoneyArr[$i],
                'useTotalNum' => $vNumberArr[$i],
                'useItemTypes' => $vUseItemTypesArr[$i],
                'useNeedMoney' => $vUseNeedMoneyArr[$i],
                'status' => 2,
                'SMS_ON_GAINED' => $data['sms_on_gained'],
                'vType' => 2,
            ];
            $vcId = VoucherConf::addVoucherConf($vcData);
            if ($vcId) {
                $vcsns[] = $vcData['vcSn'];
            }
        }
        return $vcsns ? implode(",", $vcsns) : [];
    }

    /*
     * 获取现金券活动配置信息 字段
     */

    private static function getVoucherConfField($data) {
        $res['vUseItemTypesArr'] = explode(",", $data['vUseItemTypes']);
        $res['vUseMoneyArr'] = explode(",", $data['vUseMoney']);
        $res['vNumberArr'] = explode(",", $data['vNumber']);
        $res['vDayArr'] = explode(",", $data['vDay']);
        $res['vUseNeedMoneyArr'] = explode(",", $data['vUseNeedMoney']);
        $res['vVcId'] = explode(",", $data['vVcId']);
        $res['delVcId'] = explode(",", $data['delVcId']);
        $res['vVoucherCount'] = count($res['vUseItemTypesArr']);
        return $res;
    }

    /*
     *   获取礼包活动配置信息 字段
     */

    private static function getGiftConfField($data) {
        $res['gUseItemTypesArr'] = explode(",", $data['gUseItemTypes']);
        $res['gUseMoneyArr'] = explode(",", $data['gUseMoney']);
        $res['gNumberArr'] = explode(",", $data['gNumber']);
        $res['gDayArr'] = explode(",", $data['gDay']);
        $res['gUseNeedMoneyArr'] = explode(",", $data['gUseNeedMoney']);
        $res['gVcId'] = explode(",", $data['gVcId']);
        $res['delGiftVcId'] = explode(",", $data['delGiftVcId']);
        $res['gVoucherCount'] = count($res['gUseItemTypesArr']);
        return $res;
    }

    //插入voucher_conf 中 礼包
    private static function addVoucherConfGift($data) {
        $gUseItemTypesArr = explode(",", $data['gUseItemTypes']);
        $gUseMoneyArr = explode(",", $data['gUseMoney']);
        $gNumberArr = explode(",", $data['gNumber']);
        $gDayArr = explode(",", $data['gDay']);
        $gUseNeedMoneyArr = explode(",", $data['gUseNeedMoney']);
        $gVoucherCount = count($gUseItemTypesArr);
        $gift_vcsn = [];
        if ($gVoucherCount) {
            for ($i = 0; $i < $gVoucherCount; $i++) {
                $vcSn = VoucherConf::getVcSn();
                $vcData = [
                    'vcTitle' => $data['laisee_name'],
                    'vcSn' => $vcSn,
                    'useEnd' => time() + ($gDayArr[$i] * 24 * 60 * 60),
                    'useMoney' => $gUseMoneyArr[$i],
                    'useTotalNum' => $gNumberArr[$i],
                    'useItemTypes' => $gUseItemTypesArr[$i],
                    'useNeedMoney' => $gUseNeedMoneyArr[$i],
                    'status' => 2,
                    'SMS_ON_GAINED' => $data['sms_on_gained'],
                    'vType' => 2,
                ];
                $vcId = VoucherConf::addVoucherConf($vcData);
                if ($vcId) {
                    $gift_vcsn[] = $vcData['vcSn'];
                }
            }
        }
        return $gift_vcsn ? implode(",", $gift_vcsn) : [];
    }

    public static function getLaiseeList($laiseeName, $startTime, $endTime, $page, $size) {
        $field=['id','laisee_name','create_time','start_time','status','vcsns','gift_vcsn','over_time'];  //TODO
//        $query = Self::select("*");
        $query = Self::select($field);
        if (!empty($laiseeName)) {
            $query->where('laisee_name', 'like', "%" . $laiseeName . "%");
        }
        if ($startTime) {
            $query->where('create_time', '>=', $startTime);
        }
        if ($endTime) {
            $query->where('create_time', '<=', $endTime);
        }
        AbstractPaginator::currentPageResolver(function () use($page) {
            return $page;
        });
        $laiseeList = $query->paginate($size)->toArray();
        unset($laiseeList['next_page_url']);
        unset($laiseeList['prev_page_url']);
        return $laiseeList;
    }

    /*
     *  更新代金券活动配置信息
     */

    private static function updateVoucherConfLaisee($data) {
        $conf = self::getVoucherConfField($data);
        extract($conf);
        $vcsns = [];
        //先修改 
        if ($vVoucherCount) {
            for ($i = 0; $i < $vVoucherCount; $i++) {
                if ($vVcId[$i]) {  //修改
                    $vcData = [
                        'vcTitle' => $data['laisee_name'],
                        'useEnd' => ($vDayArr[$i] * 24 * 60 * 60),
                        'useMoney' => $vUseMoneyArr[$i],
                        'useTotalNum' => $vNumberArr[$i],
                        'useItemTypes' => $vUseItemTypesArr[$i],
                        'useNeedMoney' => $vUseNeedMoneyArr[$i],
                        'SMS_ON_GAINED' => $data['sms_on_gained'],
                    ];
                    $vcId = VoucherConf::where('vcId', $vVcId[$i])->update($vcData);
                    $vcsns[] = VoucherConf::find($vVcId[$i])->vcSn();
                } else {
                    //添加
                    $vcSn = VoucherConf::getVcSn();
                    $vcData = [
                        'vcTitle' => $data['laisee_name'],
                        'vcSn' => $vcSn,
                        'useEnd' => ($vDayArr[$i] * 24 * 60 * 60),
                        'useMoney' => $vUseMoneyArr[$i],
                        'useTotalNum' => $vNumberArr[$i],
                        'useItemTypes' => $vUseItemTypesArr[$i],
                        'useNeedMoney' => $vUseNeedMoneyArr[$i],
                        'status' => 2,
                        'SMS_ON_GAINED' => $data['sms_on_gained'],
                        'vType' => 2,
                    ];
                    $vcId = VoucherConf::addVoucherConf($vcData);
                    if ($vcId) {
                        $vcsns[] = $vcData['vcSn'];
                    }
                }
            }
        }

        //删除
        if ($delVcId) {
            VoucherConf::whereIn("vcId", $delVcId)->delete();
        }
        return $vcsns ? implode(",", $vcsns) : '';
    }

    /*
     * 更新礼包活动配置信息
     */

    private static function updateVoucherConfGift($data) {
        $conf = self::getGiftConfField($data);
        extract($conf);
        $vcsns = [];
        //先修改 
        if ($gVoucherCount) {
            for ($i = 0; $i < $gVoucherCount; $i++) {
                if ($gVcId[$i]) {  //修改
                    $vcData = [
                        'vcTitle' => $data['laisee_name'],
                        'useEnd' => ($gDayArr[$i] * 24 * 60 * 60),
                        'useMoney' => $gUseMoneyArr[$i],
                        'useTotalNum' => $gNumberArr[$i],
                        'useItemTypes' => $gUseItemTypesArr[$i],
                        'useNeedMoney' => $gUseNeedMoneyArr[$i],
                        'SMS_ON_GAINED' => $data['sms_on_gained'],
                    ];
                    $vcId = VoucherConf::where('vcId', $gVcId[$i])->update($vcData);
                    $vcsns[] = VoucherConf::find($gVcId[$i])->vcSn();
                } else {
                    //添加
                    $vcSn = VoucherConf::getVcSn();
                    $vcData = [
                        'vcTitle' => $data['laisee_name'],
                        'vcSn' => $vcSn,
                        'useEnd' => ($gDayArr[$i] * 24 * 60 * 60),
                        'useMoney' => $gUseMoneyArr[$i],
                        'useTotalNum' => $gNumberArr[$i],
                        'useItemTypes' => $gUseItemTypesArr[$i],
                        'useNeedMoney' => $gUseNeedMoneyArr[$i],
                        'status' => 2,
                        'SMS_ON_GAINED' => $data['sms_on_gained'],
                        'vType' => 2,
                    ];
                    $vcId = VoucherConf::addVoucherConf($vcData);
                    if ($vcId) {
                        $vcsns[] = $vcData['vcSn'];
                    }
                }
            }
        }

        //删除
        if ($delVcId) {
            VoucherConf::whereIn("vcId", $delVcId)->delete();
        }
        return $vcsns ? implode(",", $vcsns) : '';
    }

}
