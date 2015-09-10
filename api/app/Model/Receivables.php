<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis as Redis;
use Illuminate\Pagination\AbstractPaginator;
use DB;
class Receivables extends Model {

	protected $table = 'receivables';
	
	public $timestamps = false;
	
	/**
	 * 查询列表
	 * */
	public static function getList( $where = '' , $page=1, $page_size=20,$orderName = ' r.addTime  ',$order = 'desc' )
	{
		$fields = array(
				's.salonid',
				's.salonname',
				's.sn',
				'm.name',
				'r.type',
				'r.paymentStyle',
				'r.money',
				'r.addTime',
				'r.singleNumber',
				'r.status',
				'r.receiptDate',
				'r.id',
				'mg.name as preparedByName',
			);
		//手动设置页数
		AbstractPaginator::currentPageResolver(function() use ($page) {
		    return $page;
		});
		$query =  self::getQueryByParam($where,$orderName,$order,$fields);
         
        $salonList =    $query->paginate($page_size);
        $result = $salonList->toArray();
        return $result;
	}
	
	/**
	 * 获取查询对象
	 * */
	
	private static function getQueryByParam($where = '',$orderName = ' r.addTime  ',$order = 'desc',$fields)
	{
		$query =  DB::table('receivables as r')
		->leftjoin('salon as s', 's.salonid', '=', 'r.salonid')
		->leftjoin('merchant as m', 'm.id', '=', 's.merchantId')
		->leftjoin('managers as mg', 'mg.id', '=', 'r.preparedBy')
		->leftjoin('managers as mgs', 'mgs.id', '=', 'r.cashier')
		->select($fields)
		->orderBy($orderName,$order);

		if($orderName == 'receiptDate')//收款日期排序 时 先按状态排序，eq：  待确认 默认不显示收款日期
		{
			$query =  $query ->orderBy('status','desc');
		}
		$query =  $query ->where('r.status','!=',3);//删除
		if(isset($where['type']) && $where['type'])
		{
			$query =  $query ->where('r.type','=',$where['type']);
		}
		if(isset($where['paymentStyle']) && $where['paymentStyle'])
		{
			$query =  $query ->where('r.paymentStyle','=',$where['paymentStyle']);
		}
		if(isset($where['status']) && $where['status'])
		{
			$query =  $query ->where('r.status','=',$where['status']);
		}
		if(isset($where['startTime']) && $where['startTime']  && $where['endTime'])
		{
			$query =  $query ->where('r.receiptDate','>=',$where['startTime']);
			$query =  $query ->where('r.receiptDate','<=',$where['endTime']);
			if(!$where['status'])
			{
				$query =  $query ->where('r.status','=',2);//只要确认的收款
			}
		}
		if(isset($where['salonname']) && $where['salonname'])
		{
			$keyword = '%'.$where['salonname'].'%';
			$query = $query->where('s.salonname','like',$keyword);
		}
		if(isset($where['salonSn']) && $where['salonSn'])
		{
			$keyword = '%'.$where['salonSn'].'%';
			$query = $query->where('s.sn','like',$keyword);
		}
		if(isset($where['merchantName']) && $where['merchantName'])
		{
			$keyword = '%'.$where['merchantName'].'%';
			$query = $query->where('m.name','like',$keyword);
		}
			
		return $query;
	}
	
	/**
	 * 导出
	 * 
	 * */
	public static function getListExport($where = '',$orderName = ' r.addTime  ',$order = 'desc')
	{
		$fields = array(
				's.salonid',
				's.salonname',
				's.sn',
				'm.name',
				'r.type',
				'r.paymentStyle',
				'r.money',
				'r.addTime',
				'r.singleNumber',
				'r.status',
				'r.receiptDate',
				'r.id',
				'r.checkTime',
				'r.payCode',
				'r.paySingleCode',
				'mg.name as preparedByName',
				'mgs.name as cashierName',
		);
		$query = self::getQueryByParam($where,$orderName,$order,$fields);
		return  $query->get();
	}
	
	/**
	 * 生成单号
	 * */
	public static function createSingleNumber($type)
	{
		$tps = '';
		$redis = Redis::connection();
		$query = self::getQuery();
		if($type == 1)
		{
			$key = 'STZ';
		}
		elseif($type == 2)
		{
			$key = 'SDS';
		}
		$value = Redis::hget('receivables',$key);
		if (!$value)//redis 不存在 查询数据库总数
		{
			$query->where('addTime','>=',strtotime(date('Y-m-d')));
			$query->where('type','=',$type);
			$value = $query->count();
			Redis::hset('receivables',$key,$value);
		}
		$value += 1;
		Redis::HINCRBY('receivables',$key,1);//写入redis 加1
		Redis::EXPIREAT('receivables',strtotime(date('Y-m-d').'23:59:59'));//当天过期
		
		for($i=5;$i>strlen($value);$i--)
		{
			$tps .= 0;
		}
		return $key.'-'.date('ymd').$tps.$value;//随机测试
	}
	
	/**
	 * 添加修改 操作
	 * */
	public static function dosave($save,$id = 0,$user = 0)
	{
		$query = self::getQuery();

		if($id)
		{
			$save['upTime'] = time();
			$status = $query->where('id',$id)->update($save);
		}
		else
		{
			$save['addTime'] = time();
			$save['preparedBy'] = $user;
			$save['singleNumber'] = self::createSingleNumber($save['type']);//收款单号
			$status = $query->insertGetId($save);
		}
		return $status;
	}
	
	/**
	 * 获取单条记录
	 * 
	 */
	public static  function  getOneById($id)
	{
		if(!$id)
		{
			return false;
		}
		$fields = array(
				'r.salonid',
				's.salonname',
				's.sn',
				'm.name',
				'r.type',
				'r.paymentStyle',
				'r.money',
				'r.addTime',
				'r.singleNumber',
				'r.status',
				'r.receiptDate',
				'r.preparedBy',
				'r.status',
				'r.cashier',
				'r.id',
				'r.checkTime',
				'r.payCode',
				'r.paySingleCode',
				'mg.name as preparedByName',
				'mgs.name as cashierName',
		);
		$query =  DB::table('receivables as r')
					->leftjoin('salon as s', 's.salonid', '=', 'r.salonid')
					->leftjoin('merchant as m', 'm.id', '=', 's.merchantId')
					->leftjoin('managers as mg', 'mg.id', '=', 'r.preparedBy')
					->leftjoin('managers as mgs', 'mgs.id', '=', 'r.cashier')
					->select($fields);
		$query =  $query ->where('r.id','=',$id);
		return $query->first();
	}
	
	/**
	 * 删除
	 * */
	public static function dodel($id)
	{
		if(!$id)
		{
			return false;
		}
		$query = self::getQuery();
		$save['upTime'] = time();
		$status = $query->where('id',$id)->update(['status'=>3,'upTime'=>time()]);//删除
		return $status;
	}
	
	/**
	 * 删除修改时 检测状态
	 * */
	public static function getCheckRecRsStatus($id)
	{
		if(!$id)
		{
			return false;
		}
		$status = self::select(['status'])->where("id","=",$id)->first();
		if($status->status != 1)
		{
			return false;
		}
		else
		{
			return true;
		}
		
	}
	
	
	
	
}
