<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Model\SalonStarConf;
use App\Salon;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;

class SalonstarController extends Controller {

    public function index() {
        $options = $this->parameters([
            'salonname' => self::T_STRING,
            'level' => self::T_INT,
            'page' => self::T_INT,
            'page_size' => self::T_INT,
        ]);
        $salonname = (isset($options['salonname'])) ? $options['salonname'] : '';
        $level = !empty($options['level']) ? $options['level'] : 0;
        $page = isset($options['page']) ? max(intval($options['page']), 1) : 1;
        $size = isset($options['page_size']) ? max(intval($options['page_size']), 1) : 20;
        if ($level) {
            $score = SalonStarConf::getPrevAndNextScore4Level($level);
            if (!$score) {
                throw new ApiException("未找到相应的等级level", ERROR::PARAMS_LOST);
            }
        } else {
            $score = 0;
        }
        $salonList = SalonStarConf::getSalonList($salonname, $score, $page, $size);
        if (!empty($salonList)) {
            foreach ($salonList['data'] as $key => $val) {
                if ($level) {
                    $salonList['data'][$key]['level'] = $level;
                } else {
                    $salonList['data'][$key]['level'] = SalonStarConf::getLevelByScore($val['score']);
                }
            }
        }
        return $this->success($salonList);
    }

    /*
     * 增加/减少 积分
     */

    public function update() {
        $options = $this->parameters([
            'salonid' => self::T_INT,
            'type' => self::T_INT, //1增加 2减少
            'score' => self::T_INT,
            'msg' => self::T_STRING,
        ]);
        if (empty($options['salonid'])) {
            throw new ApiException("参数传递错误，salonid", ERROR::PARAMS_LOST);
        }
        if (!in_array($options['type'], [1, 2])) {
            throw new ApiException("参数传递错误，type", ERROR::PARAMS_LOST);
        }
        if ($options['score'] < 0) {
            throw new ApiException("参数传递错误,score", ERROR::PARAMS_LOST);
        }
        $salonInfo = Salon::find($options['salonid']);
        if (is_null($salonInfo)) {
            throw new ApiException("未找到该店铺信息", ERROR::PARAMS_LOST);
        }
        if (($options['type'] == 2) && ($options['score'] > $salonInfo->score)) {
            throw new ApiException("参数传递错误,score", ERROR::PARAMS_LOST);
        }
        if ($options['type'] == 1) {
            $score = $salonInfo->score + $options['score'];
        } else {
            $score = $salonInfo->score - $options['score'];
        }
        //更新店铺score
//        $userName=$this->user->name;
        $userName = "liyu";
        $upStatus = Salon::updateScore($salonInfo, $score, $options, $userName);
        if ($upStatus) {
            return $this->success();
        } else {
            throw new ApiException("修改店铺积分失败", ERROR::PARAMS_LOST);
        }
    }
    
    /*
     * 积分详情
     */
    public function show(){
        $options = $this->parameters([
            'start_time' => self::T_STRING,
            'end_time' => self::T_STRING, //1增加 2减少
            'salonid' => self::T_INT,
        ]);
        $startTime=  isset($options['start_time'])?$options['start_time']:0;
        $endTime=  isset($options['end_time'])?$options['end_time']:0;
        $page = isset($options['page']) ? max(intval($options['page']), 1) : 1;
        $size = isset($options['page_size']) ? max(intval($options['page_size']), 1) : 20;
        $logList=  \App\Model\SalonScoreLog::getLogList($options,$startTime,$endTime,$page,$size);
        return $this->success($logList);
    }

}
