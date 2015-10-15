<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Model\SalonStarConf;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;

class StarconfController extends Controller {

    public function index() {
        $levelList = SalonStarConf::getSalonLevelList();
        return $this->success($levelList);
    }

    public function update() {
        $param = $this->param;
        $id = isset($param['id']) ? $param['id'] : 0;
        $score = isset($param['score']) ? $param['score'] : 0;
        if (!$id) {
            throw new ApiException("缺失参数id", ERROR::PARAMS_LOST);
        }
        if ($score < 0) {
            throw new ApiException("请输入有效积分数值", ERROR::PARAMS_LOST);
        }
        $level = SalonStarConf::find($id);
        if ($level) {
            $updateRes = SalonStarConf::updateConf($param);
            if ($updateRes) {
                if ($updateRes == 1) {
                    throw new ApiException("请输入有效积分数值", ERROR::PARAMS_LOST);
                } elseif ($updateRes == 2) {
                    throw new ApiException("更新星级积分失败", ERROR::PARAMS_LOST);
                }
            } else {
                return $this->success();
            }
        } else {
            throw new ApiException("未找到相应的星级积分等级", ERROR::PARAMS_LOST);
        }
    }

}
