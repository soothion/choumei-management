<?php namespace App\Http\Controllers;

use Illuminate\Pagination\AbstractPaginator;
use DB;
use Event;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;

class PlatformController extends Controller{
	/**
	 * 活动列表
	 */
	public function index()
	{
		$param = $this->param;
		$query = Promotion::getQueryByParam($param);

		$page = isset($param['page'])?max($param['page'],1):1;
		$page_size = isset($param['page_size'])?$param['page_size']:20;

		//手动设置页数
		AbstractPaginator::currentPageResolver(function() use ($page) {
		    return $page;
		});

		$fields = array(
			'title'，
			'sn',
			'sum',
			'created_at',
			'start_at',
			'end_at',
			'departments.title as department',
			'status'
		);

		//分页
	    $result = $query->select($fields)->paginate($page_size)->toArray();
	    unset($result['next_page_url']);
	    unset($result['prev_page_url']);
	    $queries = DB::getQueryLog();
	    return $this->success($result);

	}


	/**
	 * 导出活动
	 */
	public function export()
	{
		$param = $this->param;
		$query = Promotion::getQueryByParam($param);

		$page = isset($param['page'])?max($param['page'],1):1;
		$page_size = isset($param['page_size'])?$param['page_size']:20;

		//手动设置页数
		AbstractPaginator::currentPageResolver(function() use ($page) {
		    return $page;
		});

		$fields = array(
			'title'，
			'sn',
			'sum',
			'created_at',
			'start_at',
			'end_at',
			'departments.title as department',
			'status'
		);

		//分页
	    $array = $query->select($fields)->take(5000)->get();
	    $result = [];
	    foreach ($array as $key=>$value) {
	    	$result[$key]['id'] = $key+1;
	    	$result[$key]['title'] = $value->title;
	    	$result[$key]['sn'] = $value->sn;
	    	$result[$key]['sum'] = $value->sum;
	    	$result[$key]['created_at'] = $value->created_at;
	    	$result[$key]['start_at'] = $value->start_at;
	    	$result[$key]['end_at'] = $value->end_at;
	    	$result[$key]['department'] = $value->department;
	    	$result[$key]['status'] = $value->status;
	    }

		// 触发事件，写入日志
	    // Event::fire('promotion.export');
		
		//导出excel	   
		$title = '用户列表'.date('Ymd');
		$header = ['序号','活动名称','活动编码','总数上限','活动时间','申请部门','活动状态'];
		Excel::create($title, function($excel) use($result,$header){
		    $excel->sheet('Sheet1', function($sheet) use($result,$header){
			        $sheet->fromArray($result, null, 'A1', false, false);//第五个参数为是否自动生成header,这里设置为false
	        		$sheet->prependRow(1, $header);//添加表头

			    });
		})->export('xls');

	}

	/**
	 * 查看活动
	 */
	public function show($id)
	{
	
	}

	/**
	 * 下线活动
	 */
	public function offline($id)
	{
		$promotion = Promotion::find($id);
		if(!$promotion)
			throw new ApiException('活动不存在', ERROR::PROMOTION_NOT_FOUND);
		$result = $promotion->update(['statis'=>'offline']);
		if($result){
			//触发事件，写入日志
			Event::fire('user.offline',array($promotion));
			return $this->success();
		}
		throw new ApiException('活动下线失败', ERROR::PROMOTION_OFFLINE_FAILED);
	}

	/**
	 * 关闭活动
	 */
	public function close($id)
	{
		$promotion = Promotion::find($id);
		if(!$promotion)
			throw new ApiException('活动不存在', ERROR::PROMOTION_NOT_FOUND);
		$result = $promotion->update(['statis'=>'closed']);
		if($result){
			//触发事件，写入日志
			Event::fire('user.closed',array($promotion));
			return $this->success();
		}
		throw new ApiException('活动关闭失败', ERROR::PROMOTION_CLOSED_FAILED);
	}


}
