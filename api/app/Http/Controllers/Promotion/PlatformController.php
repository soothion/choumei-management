<?php 
namespace App\Http\Controllers\Promotion;

use App\Http\Controllers\Controller;
use DB;
use Excel;
use Event;
use App\Voucher;
use App\Exceptions\ApiException;
use Illuminate\Pagination\AbstractPaginator;
use App\Exceptions\ERROR;


class PlatformController extends Controller{
    private static $downloadUrl = "http://t.cn/RZXyLPg";
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
			'title',
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
			'title',
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

    /***
	 * @api {get} /platform/getItemType 1.获取项目分类名称
	 * @apiName getItemType
	 * @apiGroup Platform
	 *
	 *
	 * 
	 * @apiSuccess {Number} typeid 分类id.
	 * @apiSuccess {String} typename 分类名称
	 * 
	 * @apiSuccessExample Success-Response:
	 *		{
	 *		    "result": 1,
	 *		    "data": [{
     *               "typeid": 1,
     *               "typename": "洗剪吹"
	 *		    },
     *          ...
     *          ]
	 *		}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 ***/
    public function getItemType(){
        return $this->success( $this->_getItemType() );
    }
    /***
	 * @api {get} /paltfrom/add 2.添加平台代金劵活动
	 * @apiName add
	 * @apiGroup Platform
	 *
	 *@apiParam {String} actName                必填        活动名称
	 *@apiParam {String} actNo                  必填        活动编号
	 *@apiParam {String} actIntro               必填        活动介绍
	 *@apiParam {Number} departmentId           必填        部门id
	 *@apiParam {Number} managerId              必填        部门负责人id
	 *@apiParam {Number} money                  必填        代金券金额
	 *@apiParam {Number} getSingleLimit         必填        每个用户获取的劵上限
     *@apiParam {Number} selectItemType         必填        选择用户    1. 新用户   2. 指定用户  3. 全平台用户   4. H5用户
     *@apiParam {Number} selectUseType          可选        选择用户类型    1.新用户 2. 首次消费  3.指定手机号  4.指定集团码用户  5.指定活动码用户 6.指定店铺码用户
     *@apiParam {String} code                   可选        输入码（集团码|活动码|店铺码）     
     *@apiParam {String} getItemTypes           可选        制定消费的项目获取格式如 ",1,3,"
     *@apiParam {String} useLimitTypes          可选        限制首单使用值为2
     *@apiParam {String} phoneList              可选        制定手机号时，手机号码列格式如 "13210553366,18566554455"
     *@apiParam {Number} enoughMoeny            可选        满额可用
     *@apiParam {Number} totalNumber            可选        劵总数
     *@apiParam {Number} singleEnoughMoney      可选        单个项目满额可用
     *@apiParam {String} getTimeStart           可选        劵获取开始时间如 2015-10-16 00:00:00
     *@apiParam {String} getTimeEnd             可选        卷获取结束时间   2015-10-16 23:59:59
	 *@apiParam {String} addActLimitStartTime   可选        代金劵可使用开始时间 2015-10-16 00:00:00
	 *@apiParam {String} addActLimitEndTime     可选        代金劵可使用结束时间 2015-10-16 23:59:59
     *@apiParam {Number} fewDay                 可选        劵获取多少天内可用 （和上面劵可使用时间必须达到传其一）
     *@apiParam {String} limitItemTypes         可选        可使用的项目格式如 ",2,3,"
     *@apiParam {String} sendSms                可选        发送的短信内容
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *		{
	 *		    "result": 1,
	 *		    "data": "",
	 *		}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "添加失败"
	 *		}
	 ***/
    public function addVoucherConf(){
        $post = $this->param;
        // 必须填写的参数
        $mustKey = array(
            'actName','actIntro','departmentId','managerId',
            'money','getSingleLimit','actNo','selectItemType'
        );
        // 返回前端数组
        $result = array();
        foreach( $mustKey as $val ){
            if( empty($post[ $val ]) )
                return $this->error('必填写项未填写' );
        }
        
        if( $post['selectItemType'] == 2 && $post['selectUseType'] == 3 && empty($post['phoneList']) )
            return $this->error( '选中手机号码,未提交手机号' );
        if( (empty($post['addActLimitStartTime']) && empty($post['addActLimitStartTime'])) && empty($post['fewDay']) )
            return $this->error('必填写项未填写' );
        
        $data = array();
        $data['vcTitle'] = $post['actName'];
        $data['vcSn'] = $post['actNo'];
        $data['vcRemark'] = $post['actIntro'];
        
        if( $post['selectItemType'] == 1 && in_array($post['selectItemType'],[1,2]) )
            $data['getTypes'] = $post['selectItemType'];
        elseif( $post['selectItemType'] == 2 && $post['selectUseType'] == 3 && !empty($post['phoneList']) ){
            $phoneList = explode(',',$post['phoneList']);
            $data['getTypes'] = 3;
        }elseif( $post['selectItemType'] == 2 && in_array($post['selectUseType'],[4,5,6]) && !empty($post['code']) ){
            $tt = $data['getTypes'] = $post['selectItemType'];
            // 检验验证码是否存在
            if( in_array( $tt , array(4,5,6) ) ){
                $tempArr = [4=>'getGroupExists',5=>'getActivityExists',6=>'getDividendExists'];
                $code = $post['code'];
                if( $this->{$tempArr[ $tt ]}( $code ) == 0 )
                    return $this->error('码不存在！');
                $tempArr1 = [4=>2,5=>3,6=>1];
                $data['getCodeType'] = $tempArr1[ $tt ];
                $data['getCode'] = $code;
                $data['getItemTypes'] = $post['getItemTypes'];
            }
        }elseif( $post['selectItemType'] == 3 ){
            $data['getTypes'] = 4;
            $data['getItemTypes'] = $post['getItemTypes'];
        }elseif( $post['selectItemType'] == 4 ){
            $code = $post['code'];
            $data['getTypes'] = 5;
            if(!empty($code)) $data['getCode'] = $code;
        }else
            return $this->error('选择用户获取错误');
        // 定义代金劵
        $data['useMoney'] = $post['money'];
        $data['getNumMax'] = $post['getSingleLimit'];
        $data['DEPARTMENT_ID'] = $post['departmentId'];
        $data['MANAGER_ID'] = $post['managerId'];
        if( isset($post['singleEnoughMoney']) ) $data['USE_SINGLE_ITEM_ENOUGH_MONEY'] = $post['singleEnoughMoney'];
        if( isset($post['totalNumber']) ) $data['useTotalNum'] = $post['totalNumber'];
        if( isset($post['getTimeStart']) ) $data['getStart'] = strtotime($post['getTimeStart']);
        if( isset($post['getTimeEnd']) ) $data['getEnd'] = strtotime($post['getTimeEnd']);
        if( isset($post['fewDay']) ) $data['FEW_DAY'] = $post['fewDay'];
        if( isset($post['addActLimitStartTime']) ) $data['useStart'] = $post['addActLimitStartTime'];
        if( isset($post['addActLimitEndTime']) ) $data['useEnd'] = $post['addActLimitEndTime'];
        if( isset($post['limitItemTypes']) ) $data['useItemTypes'] = $post['limitItemTypes'];
        if( isset($post['useLimitTypes']) ) $data['useLimitTypes'] = $post['useLimitTypes'];
        if( isset($post['sendSms']) )
            $data['SMS_ON_GAINED'] = '【臭美美发】您已成功兑换一张'.$data['useMoney'].'元现金券，快去使用吧！退订回TD，下载臭美最新APP '. self::$downloadUrl;
        else
            $data['SMS_ON_GAINED'] = '亲爱的，'.$data['useMoney'].'元现金券已存入你的账户，登录臭美美发APP在个人中心查看，APP下载地址' . self::$downloadUrl;
        
        $data['status'] = 2;
        
//        $addRes = M('voucher_conf')->add($data);
        $addRes = \App\VoucherConf::insertGetId( $data );
//        print_r( $data );
        if( $data['getTypes'] == '3' ){
//            $voucherModel = M('voucher');
            $phoneArr = $phoneList;
//            var_dump( $phoneArr );
            $voucherData = array(
                'vcId' =>   $addRes,
                'vSn'=> $this->getVoucherSn(),
                'vcSn' =>   $data['vcSn'],
                'vcTitle' => $data['vcTitle'],
                'vcTitle' => $data['vcTitle'],
                'vUseMoney' => $data['useMoney'],
                'vUseItemTypes' => isset($data['useItemTypes'])? $data['useItemTypes'] : '',
                'vUseLimitTypes' => isset($data['useLimitTypes']) ? $data['useLimitTypes'] : '',
                'vUseNeedMoney' => isset($data['useNeedMoney']) ? $data['useNeedMoney'] : '',
                'vUseStart' => isset($data['useStart'])? $data['useStart'] :'',
                'vUseEnd' => isset($data['useEnd']) ? $data['useEnd'] : '',
                'vAddTime' => time(),
            );
            // 记录已经发放的劵的数量
            $sendNum = 0;
            foreach( $phoneArr as $key => $val ){
                $voucherData['vMobilephone'] = $val;
                $voucherData['vStatus'] = 10;
                // 获取的劵的数量如果是多张需要插入多次
                $signerNum = $data['getNumMax']; // 每个用户最多领取的张数
                $limitNum = isset($data['useTotalNum']) ? $data['useTotalNum'] : 0; // 劵的可领的张数
                if( empty( $limitNum ) ){ // 未设置可领的张数即无限制
                    for( $i=0;$i<$signerNum;$i++ ){
                        $addVoucher = \App\Voucher::insertGetId( $voucherData );
                        if(empty($addVoucher)){
                            file_put_contents('../voucher.log', print_r($voucherData,true),FILE_APPEND);
                        }
                    }
                }
                // 如果 劵可领总数小于或等于个人可领数的情况
                if( !empty($limitNum) && $limitNum<=$signerNum){
                    for( $i=0;$i<$limitNum;$i++ ){
                        $addVoucher = \App\Voucher::insertGetId( $voucherData );
                        if(empty($addVoucher)){
                            file_put_contents('../voucher.log', print_r($voucherData,true),FILE_APPEND);
                        }
                    }
                }

                // 如果 劵可领总数大于个人可领数的情况
                if( !empty($limitNum) && $limitNum>$signerNum){
                    for( $i=0;$i<$signerNum;$i++ ){
                        if( ($sendNum+1)<= $limitNum ){
                            $addVoucher = \App\Voucher::insertGetId( $voucherData );
                            $sendNum++;
                            if(empty($addVoucher)){
                                file_put_contents('../voucher.log', print_r($voucherData,true),FILE_APPEND);
                            }
                        }
                    }
                }
            }
        }
        if( empty($addRes) )
            return $this->error('插入数据失败，请稍后再试');
        return $this->success();
    }
    /***
	 * @api {get} /platform/getRequestDepartment 3.获取申请部门分类
	 * @apiName getRequestDepartment
	 * @apiGroup Platform
	 *
	 *
	 * 
	 * @apiSuccess {Number} id 部门id.
	 * @apiSuccess {String} title 部门名称
	 * 
	 * @apiSuccessExample Success-Response:
	 *		{
	 *		    "result": 1,
	 *		    "data": [{
     *                  "id": 1,
     *                  "title": "总裁办"
	 *		    },
     *          ...
     *          ]
	 *		}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 ***/
    public function getRequestDepartment(){
        $departmant = \App\Department::select(['id','title'])->get();
        return $this->success( $departmant );
    }
    /***
	 * @api {post} /paltform/getDepartmentManager/:id 4.获取部门分类的负责人
	 * @apiName getDepartmentManager
	 * @apiGroup Platform
	 *
	 * @apiParam {Number} id 部门id.
     * 
     * 
	 * 
	 * @apiSuccess {Number} id 部门负责人.
	 * @apiSuccess {String} name 部门负责人.
	 * 
	 * @apiSuccessExample Success-Response:
	 *		{
	 *		    "result": 1,
	 *		    "data": [{
     *              "id": 117,
     *              "name": "1"
	 *		    },
     *          ...
     *          ]
	 *		}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 ***/
    public function getDepartmentManager($id){
        if( !isset($id) )
            throw new ApiException('获取信息失败', ERROR::RECEIVABLES_ERROR);
        $manager = \App\Manager::select(['id','name'])->where('department_id','=',$id)->get();
        return $this->success( $manager );
    }
    /***
	 * @api {post} /platform/getActNum 5.获取活动编码
	 * @apiName getActNum
	 * @apiGroup Platform
	 *
     * 
     * 
	 * 
	 * @apiSuccess {String} code 活动码编号.
	 * 
	 * @apiSuccessExample Success-Response:
	 *		{
	 *		    "result": 1,
	 *		    "data":  {
     *               "code": "cm475526"
     *           }
	 *		}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 ***/
    public function getActNum(){
        //11位  减小重复的几率
        $pre = substr(time(), 7);
        $end = '';
        for ($i = 0; $i <3; $i++) {
            $end .= rand(0, 9);
        }
        $code = "cm" . $pre  . $end;
        $count = \App\VoucherConf::where( 'vcSn' , '=' , $code )->count();
        if($count) 
            return $this->getActNum();
        $code = ['code'=>$code];
        return $this->success($code);
   }
    /***
	 * @api {post} /platform/checkSerial 6.获取 集团码|活动码|推荐码 是否存在
	 * @apiName checkSerial
	 * @apiGroup Platform
	 *
	 * @apiParam {Number} type 验证类型 1： 店铺码 2. 集团码 3. 活动码
	 * @apiParam {String} code 各类码.
     * 
     * 
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *		{
	 *		    "result": 1,
	 *		    "data": {
     *              'exists':0
	 *		    }
	 *		}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 ***/
    public function checkSerial(){
        $post = $this->param;
        if( !isset($post['type']) || !isset($post['code']) )
            throw new ApiException('参数错误', ERROR::RECEIVABLES_ERROR);
        $type = $post['type'];
        $code = $post['code'];
        $exists = 0;
        if( $type == 1 ) 
            $exists = $this->getDividendExists( $code );
        if( $type == 2 )
            $exists = $this->getGroupExists( $code ); 
        if( $type == 3 )
            $exists = $this->getActivityExists( $code ); 
        $return = ['exists'=>$exists];
        return $this->success( $return );
    }
    // 校验集团码
    private function getGroupExists( $code ){
        $count = \App\CompanyCode::where( 'code' ,'=', $code )
                ->where('status','=',1)
                ->count();
        return empty( $count ) ? 0 : 1;
    }
    // 校验推荐码
    private function getDividendExists( $code ){
        $count = \App\Dividend::where( 'status','=','0' )
                ->where('recommend_code','=',$code)
                ->where('activity','=',2)
                ->count();
        return empty( $count ) ? 0 : 1;
    }
    // 校验活动码
    private function getActivityExists( $code ){
        $count = \App\Dividend::where( 'status','=','0' )
                ->where('recommend_code','=',$code)
                ->where('activity','=',1)
                ->count();
        return empty( $count ) ? 0 : 1;
    }
    // 获取分类
    private function _getItemType(){
        // 这里用于 代金劵和配置中会和前端约定 增加一个项目特价类型为typeid为101
        $itemType = \App\SalonItemtype::select(['typeid','typename'])
                ->where('status','=',1)
                ->orderBy('sortIt','DESC')
                ->get()
                ->toArray();
        array_unshift( $itemType , array('typeid'=>101,'typename'=>'限时特价 ') );
        return $itemType;
    }
    // 获取代金劵编号
    private function getVoucherSn( $p = 'CM' ) {
        $pre = substr(time(), 2);
        $end = '';
        for ($i = 0; $i <3; $i++) {
            $end .= rand(0, 9);
        }
        $code = $p . $pre  . $end;
        $count = Voucher::where('vSn','=',$code)->count();
        if ($count) 
            return $this->getVoucherSn();
        return $code;
          
   }
}
