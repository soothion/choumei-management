<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator;
use DB;
class StylistMsgConf extends Model {

	protected $table = 'stylist_msg_conf';
	
	public $timestamps = false;
	
	/**
	 * 
	 * 列表查询
	 * 
	 */
	public static function getList($where,$page,$page_size,$sort_key,$sort_type)
	{
		$fields = array(
				'id',
				'receive_type',
				'receivers',
				'title',
				'description',
				'img',
				'url',
				'status',
				'addtime',
				'onlinetime',
		);
		//手动设置页数
		AbstractPaginator::currentPageResolver(function() use ($page) {
			return $page;
		});
		$query =  self::getQueryByParam($where,$sort_key,$sort_type,$fields);
		$list =    $query->paginate($page_size);
		$result = $list->toArray();
		return $result;
	}
	
	/**
	 * 查询操作
	 * */
	private static function getQueryByParam($where,$orderName,$order,$fields)
	{
		$query = self::query();
		if(isset($where['status']) && $where['status'] != '')
		{
			$query = $query->where('status','=',$where['status']);
		}
		if($where['status'] == '')
		{
			$query = $query->where('status','!=',2);
		}
		if(isset($where['starttime']) && $where['starttime']  && $where['endtime'])
		{
			$query =  $query ->where('onlinetime','>=',$where['starttime']);
			$query =  $query ->where('onlinetime','<=',$where['endtime']);
		}
		if(isset($where['title']) && $where['title'])
		{
			$keyword = '%'.$where['title'].'%';
			$query = $query->where('s.title','like',$keyword);
		}
		$query = $query->select($fields);
		return $query;
		
	}
	
	/**
	 * 添加修改 操作
	 * */
	public static function dosave($save,$id = 0)
	{
		$query = self::getQuery();
		if($id)
		{
			$status = $query->where('id',$id)->update($save);
		}
		else
		{
			$save['addtime'] = time();
			$status = $query->insertGetId($save);
		}
		return $status;
	}
	
	
	/**
	 * 状态操作  status 0初始 1 上线 2删除
	 * 
	 * */
	public static function doOperating($id,$status=2)
	{
		if(!$id)
		{
			return false;
		}
		$query = self::getQuery();
		$data = [];
		$data['status'] = $status;
		if($status == 1)
		{
			$data['onlinetime'] = time();
		}
		$status = $query->where('id',$id)->update($data);
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
		$status = self::select(['status'])->where('id','=',$id)->first();
		if($status->status != 1)
		{
			return false;
		}
		else
		{
			return true;
		}
		
	}
	
	/**
	 * 获取单条数据
	 * */
	public static function getOnebyId($id)
	{
		if(!$id)
		{
			return false;
		}
		$fields = array(
				'id',
				'receive_type',
				'receivers',
				'title',
				'description',
				'img',
				'url',
				'status',
				'addtime',
				'onlinetime',
				'content',
		);
		$result = self::select($fields)->where('id','=',$id)->first();
		return $result;
	}
	
	
	
	
}