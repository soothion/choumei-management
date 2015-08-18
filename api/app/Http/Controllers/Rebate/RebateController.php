<?php namespace App\Http\Controllers;

use Illuminate\Pagination\AbstractPaginator;
use DB;
use Event;
use Excel;
use Auth;
use App\Rebate;

class RebateController extends Controller{
	/**
	 * @api {post} /rebate/index 1.返佣单列表
	 * @apiName list
	 * @apiGroup Rebate
	 *
	 * @apiParam {String} merchantname 可选,商户名关键字.
	 * @apiParam {String} salonname 可选,店铺名关键字.
	 * @apiParam {String} salonsn 可选,店铺编号.
	 * @apiParam {Number} start 可选,起始日期.
	 * @apiParam {String} end 可选,结束时间.
	 * @apiParam {Number} page 可选,页数.
	 * @apiParam {Number} page_size 可选,分页大小.
	 * @apiParam {String} sort_key 排序的键,比如:start_at,end_at;
	 * @apiParam {String} sort_type 排序方式,DESC或者ASC;默认DESC
	 *
	 *
	 * @apiSuccess {Number} total 总数据量.
	 * @apiSuccess {Number} per_page 分页大小.
	 * @apiSuccess {Number} current_page 当前页面.
	 * @apiSuccess {Number} last_page 当前页面.
	 * @apiSuccess {Number} from 起始数据.
	 * @apiSuccess {Number} to 结束数据.
	 * @apiSuccess {String} id 返佣单ID.
	 * @apiSuccess {String} salon_id 店铺ID.
	 * @apiSuccess {Number} salonsn 店铺编号.
	 * @apiSuccess {Array} salonname 店铺名.
	 * @apiSuccess {Object} sn 返佣单编号.
	 * @apiSuccess {Object} amount 返佣金额.
	 * @apiSuccess {Object} status 状态,1确认,2未确认.
	 * @apiSuccess {String} start_at 起始时间.
	 * @apiSuccess {String} end_at 结束时间.
	 * @apiSuccess {String} confirm_at 确认时间.
	 * @apiSuccess {String} confirm_by 确认人.
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "data": {
	 *	        "total": 1,
	 *	        "per_page": 20,
	 *	        "current_page": 1,
	 *	        "last_page": 1,
	 *	        "from": 1,
	 *	        "to": 1,
	 *	        "data": [
	 *	            {
	 *	                "id": 1,
	 *	                "salon_id": 1,
	 *	                "salonsn": "SZ0320001",
	 *	                "salonname": "嘉美专业烫染",
	 *	                "sn": "FY-15081400011",
	 *	                "amount": "0.00",
	 *	                "status": 2,
	 *	                "start_at": "0000-00-00 00:00:00",
	 *	                "end_at": "0000-00-00 00:00:00",
	 *	                "confirm_at": "0000-00-00 00:00:00",
	 *	                "confirm_by": "administrator"
	 *	            }
	 *	        ]
	 *	    }
	 *	}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 */
	public function index()
	{
		$param = $this->param;
		$query = Rebate::join('salon', 'salon.salonid', '=', 'rebate.salon_id');
		//商户名筛选
		if(isset($param['merchantname'])&&$param['merchantname']){
			$query = Rebate::where('merchant.name', 'like', '%' . $param['merchantname'] .'%')
			    ->join('salon', 'salon.salonid', '=', 'rebate.salon_id')
			    ->join('merchant', 'merchant.id', '=', 'salon.merchantid');
		}	

		//店铺名筛选
		if(isset($param['salonname'])&&$param['salonname']){
			$query =Rebate::whereHas('salon',function($q) use($param){
				$q->where('salonname','like','%'.$param['salonname'].'%');
			});
		}		

		//店铺编号筛选
		if(isset($param['salonsn'])&&$param['salonsn']){
			$query =Rebate::whereHas('salon',function($q) use($param){
				$q->where('salonsn','like','%'.$param['salonsn'].'%');
			});
		}

		//起始时间
		if(isset($param['start'])&&$param['start']){
			$query = $query->where('start_at','>=',$param['start']);
		}

		//结束时间
		if(isset($param['end'])&&$param['end']){
			$query = $query->where('end_at','<',date('Y-m-d',strtotime('+1 day',strtotime($param['end']))));
		}

		//排序
		if(isset($param['sort_key'])&&$param['sort_key']){
			$param['sort_type'] = empty($param['sort_type'])?'DESC':$param['sort_type'];
			$query = $query->orderBy($param['sort_key'],$param['sort_type']);
		}
		$page = isset($param['page'])?max($param['page'],1):1;
		$page_size = isset($param['page_size'])?$param['page_size']:20;

		//手动设置页数
		AbstractPaginator::currentPageResolver(function() use ($page) {
		    return $page;
		});

		$fields = array(
		    'rebate.id as id',
			'salon_id',
			'salon.sn as salonsn',
			'salon.salonname',
			'rebate.sn as sn',
			'amount',
			'rebate.status as status',
			'start_at',
			'end_at',
			'created_at',
			'confirm_at',
			'confirm_by',
			'created_by',
			'status'
		);

		//分页
	    $result = $query->select($fields)->paginate($page_size)->toArray();
	    unset($result['next_page_url']);
	    unset($result['prev_page_url']);
	    return $this->success($result);

	}


	/**
	 * @api {post} /rebate/export 2.导出返佣单
	 * @apiName export
	 * @apiGroup Rebate
	 *
	 * @apiParam {String} merchantname 可选,商户名关键字.
	 * @apiParam {String} salonname 可选,店铺名关键字.
	 * @apiParam {String} salonsn 可选,店铺编号.
	 * @apiParam {Number} start 可选,起始日期.
	 * @apiParam {String} end 可选,结束时间.
	 * @apiParam {String} sort_key 排序的键,比如:start_at,end_at;
	 * @apiParam {String} sort_type 排序方式,DESC或者ASC;默认DESC
	 *
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "data": null
	 *	}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 */
	public function export()
	{
		$param = $this->param;
		$query = Rebate::join('salon', 'salon.salonid', '=', 'rebate.salon_id');
		//商户名筛选
		if(isset($param['merchantname'])&&$param['merchantname']){
			$query = Rebate::where('merchant.name', 'like', '%' . $param['merchantname'] .'%')
			    ->join('salon', 'salon.salonid', '=', 'rebate.salon_id')
			    ->join('merchant', 'merchant.id', '=', 'salon.merchantid');
		}	

		//店铺名筛选
		if(isset($param['salonname'])&&$param['salonname']){
			$query =Rebate::whereHas('salon',function($q) use($param){
				$q->where('salonname','like','%'.$param['salonname'].'%');
			});
		}		

		//店铺编号筛选
		if(isset($param['salonsn'])&&$param['salonsn']){
			$query =Rebate::whereHas('salon',function($q) use($param){
				$q->where('salonsn','like','%'.$param['salonsn'].'%');
			});
		}

		//起始时间
		if(isset($param['start'])&&$param['start']){
			$query = $query->where('start_at','>=',$param['start']);
		}

		//结束时间
		if(isset($param['end'])&&$param['end']){
			$query = $query->where('end_at','<',date('Y-m-d',strtotime('+1 day',strtotime($param['end']))));
		}

		//排序
		if(isset($param['sort_key'])&&$param['sort_key']){
			$param['sort_type'] = empty($param['sort_type'])?'DESC':$param['sort_type'];
			$query = $query->orderBy($param['sort_key'],$param['sort_type']);
		}

		$fields = array(
		    'rebate.id as id',
			'salon_id',
			'salon.sn as salonsn',
			'salon.salonname',
			'rebate.sn as sn',
			'amount',
			'rebate.status as status',
			'start_at',
			'end_at',
			'confirm_at',
			'confirm_by',
			'created_at',
			'created_by',
		);

		//分页
	    $array = $query->select($fields)->get();
	    foreach ($array as $key => $value) {
	    	$result[$key]['id'] = $key+1;
	    	$result[$key]['salonsn'] = $value->salonsn;
	    	$result[$key]['salonname'] = $value->salonname;
	    	$result[$key]['sn'] = $value->sn;
	    	$result[$key]['start_at'] = substr($value->start_at, 0,10);
	    	$result[$key]['end_at'] = substr($value->end_at, 0,10);
	    	$result[$key]['amount'] = $value->amount;
	    	$result[$key]['created_at'] = substr($value->created_at, 0,10);
	    	$result[$key]['confirm_at'] = substr($value->confirm_at, 0,10);
	    	$result[$key]['created_by'] = $value->created_by;
	    	$result[$key]['status'] = $value->status==1?'已确认':'待确认';
	    }
		// 触发事件，写入日志
	    // Event::fire('rebate.export');
		
		//导出excel	   
		$title = '返佣单列表'.date('Ymd');
		$header = ['序号','店铺编号','店铺名称','返佣编号','结算起始日','结算截止日','金额','创建日期','确认日期','制单人','状态'];
		Excel::create($title, function($excel) use($result,$header){
		    $excel->sheet('Sheet1', function($sheet) use($result,$header){
			        $sheet->fromArray($result, null, 'A1', false, false);//第五个参数为是否自动生成header,这里设置为false
	        		$sheet->prependRow(1, $header);//添加表头

			    });
		})->export('xls');

	}

	 /**
	 * @api {post} /rebate/create 3.新增返佣单
	 * @apiName create
	 * @apiGroup Rebate
	 *
	 * @apiParam {Number} salon_id 店铺ID.
	 * @apiParam {String} start_at 结算起始日.
	 * @apiParam {String} end_at 结算截止日.
	 * @apiParam {Number} amount 金额.
	 *
	 * @apiSuccessExample Success-Response:
	 *	    {
	 *	        "result": 1,
	 *	        "data": null
	 *	    }
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "创建失败"
	 *		}
	 */
	public function create()
	{
		$param = $this->param;
		$rebate = new Rebate;
		$param['created_by'] = $this->user->name;
		$param['status'] = 2;
		$sn = $rebate->getSn();
		$param['sn'] = $sn;
		$rebate = Rebate::create($param);
		if($rebate)
			return $this->success();
		else 
			return $this->error('创建失败');
	}


	/**
	 * @api {post} /rebate/show/:id 4.查看返佣单信息
	 * @apiName show
	 * @apiGroup User
	 *
	 * @apiParam {Number} id 必填,返佣单ID.
	 *
	 * @apiSuccess {Number} id 返佣单ID.
	 * @apiSuccess {String} salon_id 店铺ID.
	 * @apiSuccess {String} sn 返佣单编号.
	 * @apiSuccess {String} amount 金额.
	 * @apiSuccess {String} status 状态,1已确认,2未确认.
	 * @apiSuccess {String} start_at 起始日期.
	 * @apiSuccess {String} end_at 结束日期.
	 * @apiSuccess {String} confirm_by 确认人.
	 * @apiSuccess {String} confirm_at 确认日期.
	 * @apiSuccess {String} created_by 创建人.
	 * @apiSuccess {Number} created_at 创建日期.
	 * @apiSuccess {String} update_at 更新时间.
	 * @apiSuccess {Array} salon 店铺信息.
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	  "result": 1,
	 *	  "data": {
	 *	    "id": 1,
	 *	    "salon_id": 1,
	 *	    "sn": "FY-15081400011",
	 *	    "amount": "0.00",
	 *	    "status": 2,
	 *	    "start_at": "0000-00-00 00:00:00",
	 *	    "end_at": "0000-00-00 00:00:00",
	 *	    "confirm_by": null,
	 *	    "confirm_at": "0000-00-00 00:00:00",
	 *	    "created_by": "这是用户名Admin",
	 *	    "created_at": "2015-08-14 16:38:46",
	 *	    "updated_at": "2015-08-14 16:38:46",
	 *	    "salon": {
	 *	      "salonid": 1,
	 *	      "salonname": "嘉美专业烫染",
	 *	      "sn": "SZ0320001"
	 *	    }
	 *	  }
	 *	}	
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 */
	public function show($id)
	{
		$rebate = Rebate::with(['salon'=>function($q){
			$q->select('salonid','salonname','sn');
		}])->find($id);
		return $this->success($rebate); 
	}

	/**
	 * @api {post} /rebate/confirm 5.确认返佣单
	 * @apiName confirm
	 * @apiGroup Rebate
	 */
	public function confirm()
	{
		$param = $this->param;
		if(empty($param['rebate']))
			return $this->erryr('必须指定返佣单ID');
		DB::beginTransaction();
		$result = Rebate::whereIn('id',$param['rebate'])->update(['status'=>1,'confirm_at'=>date('Y-m-d H:m:s'),'confirm_by'=>$this->user->name]);
		if($result==count($param['rebate']))
		{
			DB::commit();
			return $this->success();
		}
		else
		{
			DB::rollback();
			return $this->error('确认失败');
		}
	}


}