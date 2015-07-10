<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Input;
use Response;
use Route;
use Request;
use Event;
use App\Manager;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;
    
    
    const T_RAW = 0x0;
    const T_STRING = 0x1;
    const T_INT = 0x2;
    const T_FLOAT = 0x4;
    const T_BOOL = 0x8;
    const T_MAP = 0x10;
    
    const T_STRIP_ER = 0x20; //strip \r
    const T_STRIP_NL = 0x40; //strip \n and \r
    const T_STRIP_TAGS = 0x80; //strip html tags
    
    const T_EMAIL = 0x1000;
    const T_URL = 0x2000;
    const T_PHONE = 0x4000;
    const T_MOBILE = 0x8000;

	public $param;
	public $user;

	public function __construct(){
		$this->param = Input::all();
		$this->user = Manager::first();		
	}

	public function error($msg,$code=0){
		return Response::json([
			'result'=>0,
			'msg'=>$msg,
			'code'=>$code
		]);
	}

	public function success($data=[]){
		return Response::json([
			'result'=>1,
			'data'=>$data
		]);
	}
	
	
	//生成树型结构
	public function tree($array){   
        foreach( array_keys( $array ) as $key )
		{
		       if( $array[$key]['inherit_id'] == null )
		       {
		            continue;
		       }
		       if( $this->child( $array , $array[$key] ) )
		       {
		            unset( $array[$key] );
		       }
		}
		$array = array_values($array);
		return $array;
	}

	public function child( &$list , $tree ){
	    if( empty( $list ) )
	    {
	        return false;
	    }
	    foreach( $list as $key => $val )
	    {
	        if( $tree['inherit_id'] == $val['id'] && $tree['id'] != $tree['inherit_id'] )
	        {
	            $list[$key]['child'][] = $tree;
	            return true;
	        }
	        if( isset( $val['child'] ) && is_array( $val['child'] ) && !empty( $val['child'] ) )
	        {
	            if( $this->child( $list[$key]['child'] , $tree ) )
	            {
	                return true;
	            }
	        }
	    }
	    return false;
	}

	//二维数据去重
	public function array_multiuniue($array){
		foreach ($array as $key => $value) {
			$value = json_encode($value);
			$temp[] = $value;
		}
		$temp = array_unique($temp);
		foreach ($temp as $key => $value) {
			$temp[$key] = json_decode($value,TRUE);
		}
		return $temp;
	}


	public function parameters($definition, $required = false, $source =null , $prefix = null)
	{
	    $parameters = array();
	    if(empty($source))
	    {
	        $source = $this->param;
	    }
	    foreach ($definition as $key => $filter) {
	        if (isset($source[$key]) ) {
	            $result = self::filter($source[$key], $filter);
	            if ($result === false) {
	                throw new \Exception("Parameter '{$key}' is invalid");
	            }
	        }
	        else {
	            if ($required) {
	                throw new \Exception("Parameter '{$key}' is required");
	            }
	            continue;
	        }
	
	        //parameter key prefix
	        if ($prefix) {
	            $key =  $prefix . $key;
	        }
	
	        $parameters[$key] = $result;
	    }
	
	    return $parameters;
	}
	

	/**
	 * filter from souce data by type
	 *
	 * @param $var source data
	 * @param $type filter type list
	 *
	 * @return array
	 */
	public static function filter($var, $type)
	{
	    if ($type === self::T_RAW) {
	        return $var;
	    }
	
	    //map
	    if ($type & self::T_MAP) {
	        if (is_array($var) ) {
	            $tmp_type = $type ^ self::T_MAP;
	            if ($tmp_type) {
	                //filter to every item
	                foreach ($var as $tmp_key => $tmp_value) {
	                    $var[$tmp_key] = self::filter($tmp_value, $tmp_type);
	                }
	            }
	
	            return $var;
	        }
	
	        if (!empty($var)) {
	            return false;
	        }
	
	        return array();
	    }
	
	    //int
	    if ($type & self::T_INT) {
	        if (!is_numeric($var)) {
	            return false;
	        }
	
	        $var = intval($var);
	        return $var;
	    }
	
	    //float
	    if ($type & self::T_FLOAT) {
	        if (!is_numeric($var)) {
	            return false;
	        }
	
	        $var = doubleval($var);
	        return $var;
	    }
	
	    //boolean
	    if ($type & self::T_BOOL) {
	        $var = empty($var) ? 0 : 1;
	        return $var;
	    }
	
	    //string above
	    if ($type & self::T_STRING) {
	        if (is_string($var)) {
	            return $var;
	        }
	
	        return false;
	    }
	
	    if ($type & self::T_EMAIL) {
	        $var = filter_var($var, FILTER_VALIDATE_EMAIL);
	        if ($var === false) {
	            return false;
	        }
	
	        return $var;
	    }
	
	    if ($type & self::T_URL) {
	        $var = filter_var($var, FILTER_VALIDATE_URL);
	        if ($var === false) {
	            return false;
	        }
	
	        return $var;
	    }
	
	    if ($type & self::T_PHONE) {
	        if (preg_match('/^(\+?[0-9]{2,3})?[0-9]{3,7}\-[0-9]{6,8}(\-[0-9]{2,6})?$/', $var)) {
	            return false;
	        }
	
	        return $var;
	    }
	
	    if ($type & self::T_MOBILE) {
	        if (preg_match('/^(\+?[0-9]{2,3})?[0-9]{6,11}$/', $var)) {
	            return false;
	        }
	
	        return $var;
	    }
	
	    //strip \r
	    if ($type & self::T_STRIP_ER) {
	        $var = str_replace("\r", '', $var);
	    }
	
	    //strip \n & \r
	    if ($type & self::T_STRIP_NL) {
	        $var = str_replace("\r", '', $var);
	        $var = str_replace("\n", '', $var);
	    }
	
	    //strip html tags
	    if ($type & self::T_STRIP_TAGS) {
	        $var = strip_tags($var);
	    }
	
	    return $var;
	}
}
