<?php
/**
 * 数据库字段 数字对应的意思
 * 命名规范  getTablenameColumnname
 * 
 */
namespace App;

class Mapping
{
    /**
     * 获取支付方式
     * @param int $pay_type
     * @return string
     */
    public static function getPayTypeName($pay_type)
    {
        $res = "";
        switch (intval($pay_type)) {
            case 1:
                $res = "银行存款";
                break;
            case 2:
                $res = "账扣支付 ";
                break;
            case 3:
                $res = "现金";
                break;
            case 4:
                $res = "支付宝";
                break;
            case  5:
                $res = "财付通";
                break;
            case  6:
                $res = "其他";
                break;
        }
        return $res;
    }
    
    /**
     * 获取店铺类型
     * @param int $shop_type
     * @return string
     */
    public static function getShopTypeName($shop_type)
    {
        $res = "";
        switch (intval($shop_type)) {
            case 1:
                $res = "预付款店";
                break;
            case 2:
                $res = "投资店";
                break;
            case 3:
                $res = "金字塔店";
                break;
        }
        return $res;
    }
    
    public static function getPayManageStateName($state)
    {
        $res = "";
        switch (intval($state)) {
            case PayManage::STATE_OF_TO_SUBMIT:
                $res = "待提交";
                break;
            case PayManage::STATE_OF_TO_CHECK:
                $res = "待审批";
                break;
            case PayManage::STATE_OF_TO_PAY:
                $res = "待付款";
                break;
            case PayManage::STATE_OF_PAIED:
                $res = "已付款";
                break;
        }
        return $res;
    }
    
    public static function getPrepayStateName($state)
    {
        $res = "";
        switch (intval($state)) {
            case PrepayBill::STATE_OF_PREVIEW:
                $res = "预览";
                break;
            case PrepayBill::STATE_OF_TO_SUBMIT:
                $res = "待提交";
                break;
            case PrepayBill::STATE_OF_TO_CHECK:
                $res = "待审批";
                break;
            case PrepayBill::STATE_OF_TO_PAY:
                $res = "待付款";
                break;
            case PrepayBill::STATE_OF_COMPLETED:
                $res = "已付款";
                break;
        }
        return $res;
    }
    
    public static function getfFundflowRereason($rereason)
    {
        $res = "";
        switch (intval($rereason)) {
            case 0:
                $res = "去过了，不太满意";
                break;
            case 1:
                $res = "朋友/网上评价不好";
                break;
            case 2:
                $res = "买多了/买错了";
                break;
            case 3:
                $res = "计划有变,没时间去";
                break;
            case 4:
                $res = "后悔了,不想要了";
                break;
            case 5:
                $res = "其他";
                break;
        }
        return $res;
    }
}