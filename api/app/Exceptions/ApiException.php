<?php namespace App\Exceptions;

use Exception;

class ApiException extends Exception {

	/**
	 * 错误码映射,号段自选
	 * 
	 * 0到-9999       系统错误
	 * -10000到-19999 参数错误
	 * -20000到-29999 数据库错误
	 * -30000到-39999 路由错误
	 * -40000到-49999 权限错误
	 */
	public $mapping = [
		-40001=>'token无效',
	];

	public function getError(){
		return array_key_exists($this->code, $this->mapping)?$this->mapping[$this->code]:'';
	}

	public function getMapping(){
		return $this->mapping;
	}

}
