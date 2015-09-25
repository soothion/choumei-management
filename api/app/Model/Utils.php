<?php
namespace App;

/**
 * 公用集合小方法
 * @author zhunian
 */
class Utils
{
    /**
     * 组合数据时的一对一
     * @var unknown
     */
    CONST GROUP_MAKE_BY_ONE_TO_ONE = 1;
    
    /**
     * 组合数据时的一对多
     * @var unknown
     */
    CONST GROUP_MAKE_BY_ONE_TO_MANY = 2;
    
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
    
    /**
	 * 获取数组或map某列的值,返回数组
	 */
	public static function get_column_array($column_name, $inputs = array())
	{
		if(version_compare(PHP_VERSION, '5.5.0')>0)
		{
			return array_column($inputs,$column_name);
		}
		else
		{
			$result = array();
			if (!empty($inputs)) {
				$result = array();
				$i = 0;
				foreach ($inputs as $k => $v) {
					$result[$i] = $v[$column_name];
					$i++;
				}
			}
			return $result;
		}
	}
    
    /**
	 * 将数组某列元素转化为key值，用作map
	 */
	public static function column_to_key($column_name, $inputs = array())
	{
		$result = array();
		if (!empty($inputs)) {
			$count = count($inputs);
			for ($i = 0; $i < $count; $i++) {
				$result[$inputs[$i][$column_name]] = $inputs[$i];
			}
		}
		return $result;
	}
	
	public static function column_to_group($column_name,$inputs =array())
	{
	    $result = array();
	    if (!empty($inputs)) {
	        $count = count($inputs);
	        for ($i = 0; $i < $count; $i++) {
	            $group_key = $inputs[$i][$column_name];
	            if(!isset($result[$group_key]))
	            {
	                $result[$group_key] = [];
	            }
	            $result[$group_key][] = $inputs[$i];
	        }
	    }
	    return $result;
	}
	
	/**
	 * CURL发送POST请求
	 * @param string $url
	 * @param array $data
	 * @param number $timeout
	 * @return boolean
	 */
	public static function HttpPost($url,$data,$timeout = 30)
	{	    
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_TIMEOUT,$timeout);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    curl_setopt($curl, CURLOPT_POST, 1);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($curl);
	    curl_close($curl);
	    return $output;
	}
	
	/**
	 * 简单记录log的方法
	 * @param string $content
	 * @param string $dir
	 */
	public static function log($dirname,$content,$filename = '')
	{
	    $dir = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."storage").DIRECTORY_SEPARATOR.$dirname.DIRECTORY_SEPARATOR;
	    
	    $old_mask = umask(0);
	    //check & make dir
	    if (!is_dir($dir)) {
	        mkdir($dir, 0777, true);
	    }
	    
	    //write file
	    $file_name = empty($filename) ? date('Ymd') : $filename;
	    $file = $dir . $file_name;
	    file_put_contents($file . '.log', $content, FILE_APPEND | LOCK_EX);
	    
	    //keep small than 1G
	    if (filesize($file . '.log') > 1000000000) {
	        rename($file . '.log', $file . '.' . date('YmdHis') . '.log');
	    }	    
	    umask($old_mask);
	}
	
	public static function groupMake($bases,$others)
	{
	    $res = [];
	    foreach ($bases as $key => $base)
	    {
	        $tmp = $base;
	        foreach($others as $type => $other)
	        {	
	            $column_name = isset($other['as'])?$other['as']:$type;
	            $key_name = $other['relation'];
	            $key_value = $tmp[$key_name];
	            if(isset($other['datas'][$key_value]))
	            {
	                $tmp[$column_name] = $other['datas'][$key_value];	             
	                if(isset($other['add_to_base']) && count($other['add_to_base'])>0)
	                {
	                    foreach($other['add_to_base'] as $from_key => $to_key)
	                    {
	                        if(is_int($from_key))
	                        {
	                            $from_key = $to_key;
	                        }
	                        $tmp[$to_key] = isset($other['datas'][$key_value][$from_key])?$other['datas'][$key_value][$from_key]:'';
	                    }
	                }
	            }
	            else 
	            {
	                $tmp[$column_name] = isset($other['make_by'])&&$other['make_by'] == self::GROUP_MAKE_BY_ONE_TO_MANY?[]:null;
	                if(isset($other['add_to_base']) && count($other['add_to_base'])>0)
	                {
	                    foreach($other['add_to_base'] as $from_key => $to_key)
	                    {	                        
	                        $tmp[$to_key] = '';
	                    }
	                }
	            }
	        }	        
	        $res[$key] =  $tmp;
	    }
	    return $res;
	}	
}
