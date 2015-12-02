<?php

namespace App\Http\Controllers\Artificer;

use App\Http\Controllers\Controller;
use DB;
use Event;
use Excel;
use App\Artificer;
use App\Exceptions\ApiException;
use Illuminate\Pagination\AbstractPaginator;
use App\Exceptions\ERROR;
use Log;

class ArtificerController extends Controller{
    /**
	 * @api {post} /artificer/index     1.专家列表
	 * @apiName index
	 * @apiGroup Artificer
	 *
	 * @apiParam {String} itemMainNum       可选,主体搜索条件类型。  1.专家姓名 2.在职编号
	 * @apiParam {String} keyword           可选,对应主体搜索关键词.
	 * @apiParam {String} orderField        可选, 排序字段目前只有两个 level 级别 status 状态
	 * @apiParam {String} orderBy           可选, 排序方式 desc 降序 asc 升序
	 * @apiParam {Number} page              可选,第几页。 默认为第一页
	 * @apiParam {Number} page_size         可选,取多少条。 默认为20条
	 *
	 *
	 * @apiSuccess {Number} total           总数据量.
	 * @apiSuccess {Number} per_page        分页大小.
	 * @apiSuccess {Number} current_page    当前页面.
	 * @apiSuccess {Number} last_page       当前页面.
	 * @apiSuccess {Number} from            起始数据.
	 * @apiSuccess {Number} to              结束数据.
	 * @apiSuccess {Number} id              专家id
	 * @apiSuccess {String} name            专家名称
	 * @apiSuccess {String} sex             性别 1男； 2女
	 * @apiSuccess {String} country         国家名称
	 * @apiSuccess {String} workingLife     工作年限
	 * @apiSuccess {String} level           级别 1明星院长； 2院长
	 * @apiSuccess {String} number          在职编号
	 * @apiSuccess {Number} status          状态标识. 1:正常启用，0:禁用
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "token": "",
	 *	    "data": {
	 *	        "total": 43353,
	 *	        "per_page": 20,
	 *	        "current_page": 1,
	 *	        "last_page": 2168,
	 *  	    "from": 1,
	 *	        "to": 20,
	 *	        "data": [
	 *	            {
	 *                  "id": 1,
     *                  "name": "满意",
     *                  "sex": 1,
     *                  "country": "韩国",
     *                  "workingLife": 5,
     *                  "level": 1,
     *                  "number": 6,
     *                  "status": 1
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
    public function index(){
        $param = $this->param;
        $itemMainNum = isset( $param['itemMainNum'] ) ? $param['itemMainNum'] : 0;
        $keyword = isset( $param['keyword'] ) ? $param['keyword'] : '';
        $orderField = isset( $param['orderField'] ) ? $param['orderField'] : '';
        $orderBy = isset( $param['orderBy'] ) ? $param['orderBy'] : '';
        $page = isset($param['page']) ? max($param['page'],1) : 1;
        $pageSize = isset($param['page_size']) ? $param['page_size'] : 20;
        if( $pageSize <= 0 ) $pageSize = 20;
        if( $pageSize > 500 ) $pageSize = 500;
        if( (!$orderField && $orderBy) || ($orderField && !$orderBy) ) return $this->error('排序传值错误');
        $field = ['artificer_id as id','name','sex','country','working_life AS workingLife','level','number','status'];
        $obj = Artificer::select( $field )->where('pid','=',NULL);
        //手动设置页数
		AbstractPaginator::currentPageResolver(function() use ($page) {
		    return $page;
		});
        if( $itemMainNum != 0 && $keyword ){
            switch( $itemMainNum ){
                case 1:
                    $obj->where(['name'=>$keyword]);
                    break;
                case 2:
                    $obj->where(['number'=>$keyword]);
                    break;
                default:
                    break;
            }
        }
        if( !$orderField || !$orderBy )   $result = $obj->orderBy('id','desc')->paginate($pageSize)->toArray();
        if( $orderField && $orderBy ){ 
            switch( $orderField ){
                case 'status':
                    // 降序为 启用（1）在前 禁用（0）在后
                    $temp = ['asc'=>'desc','desc'=>'asc'];
                    $result = $obj->orderBy( $orderField,$temp[$orderBy])->paginate($pageSize)->toArray();
                    break;
                case 'level':
                    $result = $obj->orderBy( $orderField,$orderBy)->paginate($pageSize)->toArray();
                    break;
                default:
                    $result = $obj->orderBy('id','desc')->paginate($pageSize)->toArray();
                    break;
            }
        }
//        print_r( $result );exit;
        return $this->success( $result );
    }
    /**
    * @api {Post} /artificer/add    2.专家添加
    * @apiName add
    * @apiGroup Artificer
    *
    * @apiParam {String} photo          必填,个人照片.
    * @apiParam {String} name           必填,姓名.
    * @apiParam {Number} sex            必填,性别 1.男 2.女
    * @apiParam {String} country        必填,韩国.
    * @apiParam {String} birthday       必填,生日 格式如 2015-02-22.
    * @apiParam {Number} level          必填,级别 1明星院长； 2院长.
    * @apiParam {String} number         必填,在职编号.
    * @apiParam {Number} workingLife    必填,工作年限.
    * @apiParam {String} introduce      必填,个性签名.
    * @apiParam {String} experience     必填,从业经历.
    * @apiParam {String} detail         必填,个人介绍JSON.[{"title":"我很好溜哦","content":"你好呀"},{"title":"ni我很好溜哦","content":"你好呀1"}] title:标题 content:内容
    * @apiParam {Number} credential     选填,证件类型 0无填写类型 1身份证； 2军官证； 3驾驶证； 4护照.
    * @apiParam {String} cardId         选填,证件类型所对应的证件号码.
    * @apiParam {String} mobilePhone    选填,电话.
    * @apiParam {String} wechat         选填,微信.
    * @apiParam {String} qq             选填,qq.
    * @apiParam {String} email          选填,电子邮箱.
    *
    *
    * 
    * 
    * 
    * 
    * 
    * 
    * @apiSuccessExample Success-Response:
    * 	    {
    * 	        "result": 1,
    * 	        "data": null
    * 	    }
    *
    * 
    * @apiSuccessExample Success-Response:
    *	{
    *	    "result": 1,
    *	    "token": "",
    *	    "data": []
    *	}
    *
    *
    * @apiErrorExample Error-Response:
    *		{
    *		    "result": 0,
    *		    "msg": "未授权访问"
    *		}
    */
    public function add(){
        $param = $this->param;
        $addData = $this->_formatReceiveData( $param );
        $addData['created_at'] = time();
        $lastId = Artificer::insertGetId( $addData );
        Event::fire('artificer.add','添加专家数据: '.$lastId);
        return $this->success();
    }
    /**
    * @api {Post} /artificer/update  3.专家编辑
    * @apiName update
    * @apiGroup Artificer
    *
    * @apiParam {Number} id             必填,专家id.
    * @apiParam {String} photo          必填,个人照片.
    * @apiParam {String} name           必填,姓名.
    * @apiParam {Number} sex            必填,性别 1.男 2.女
    * @apiParam {String} country        必填,韩国.
    * @apiParam {String} birthday       必填,生日 格式如 2015-02-22.
    * @apiParam {Number} level          必填,级别 1明星院长； 2院长.
    * @apiParam {String} number         必填,在职编号.
    * @apiParam {Number} workingLife    必填,工作年限.
    * @apiParam {String} introduce      必填,个性签名.
    * @apiParam {String} experience     必填,从业经历.
    * @apiParam {String} detail         必填,个人介绍JSON.
    * @apiParam {String} credential     选填,证件类型 0无填写类型 1身份证； 2军官证； 3驾驶证； 4护照.
    * @apiParam {String} cardId         选填,证件类型所对应的证件号码.
    * @apiParam {String} mobilePhone    选填,电话.
    * @apiParam {String} wechat         选填,微信.
    * @apiParam {String} qq             选填,qq.
    * @apiParam {String} email          选填,电子邮箱.
    *
    *
    * 
    * 
    * 
    * 
    * 
    * 
    * @apiSuccessExample Success-Response:
    * 	    {
    * 	        "result": 1,
    * 	        "data": null
    * 	    }
    *
    * 
    * @apiSuccessExample Success-Response:
    *	{
    *	    "result": 1,
    *	    "token": "",
    *	    "data": []
    *	}
    *
    *
    * @apiErrorExample Error-Response:
    *		{
    *		    "result": 0,
    *		    "msg": "未授权访问"
    *		}
    */
    public function save(){
        $param = $this->param;
        $id = isset( $param['id'] ) ? $param['id'] : $this->error('职工id未填写');
        $saveData = $this->_formatReceiveData( $param );
        $saveData['updated_at'] = time();
        Artificer::where(['artificer_id'=>$id])->update( $saveData );
        Event::fire('artificer.update','编辑专家数据: '.$id);
        return $this->success();
    }
    /**
    * @api {Get} /artificer/show/:id  4.专家信息
    * @apiName show
    * @apiGroup Artificer
    *
    * @apiSuccess {Number} id             专家id
    * @apiSuccess {String} photo          个人照片.
    * @apiSuccess {String} name           姓名.
    * @apiSuccess {Number} sex            性别 1.男 2.女
    * @apiSuccess {String} country        韩国.
    * @apiSuccess {String} birthday       生日 格式如 2015-02-22.
    * @apiSuccess {Number} level          级别 1明星院长； 2院长.
    * @apiSuccess {String} number         在职编号.
    * @apiSuccess {Number} workingLife    工作年限.
    * @apiSuccess {String} introduce      个性签名.
    * @apiSuccess {String} experience     从业经历.
    * @apiSuccess {String} detail         个人介绍JSON.[{"title":"我很好溜哦","content":"你好呀"},{"title":"ni我很好溜哦","content":"你好呀1"}] title:标题 content:内容
    * @apiSuccess {String} credential     证件类型 0无填写类型 1身份证； 2军官证； 3驾驶证； 4护照.
    * @apiSuccess {String} cardId         证件类型所对应的证件号码.
    * @apiSuccess {String} mobilePhone    电话.
    * @apiSuccess {String} wechat         微信.
    * @apiSuccess {String} qq             qq.
    * @apiSuccess {String} email          电子邮箱.
	* @apiSuccess {String} status         状态标识. 1:正常启用，0:禁用
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
    *	{
    *	    "result": 1,
    *	    "token": "",
    *	    "data": {
    *                   "id": 2,
    *                    "name": "LFR-T001",
    *                    "photo": "http://img01.choumei.cn/1/785973/201509281548144342652083878597311884.jpg",
    *                    "sex": 1,
    *                    "country": "韩国",
    *                    "birthday": "1968-06-15",
    *                    "credential": 1,
    *                    "cardId": "5555",
    *                    "mobilePhone": "13699854682",
    *                    "wechat": "155669877",
    *                    "qq": 888,
    *                    "email": "88@66.cn",
    *                    "level": 1,
    *                    "number": 1001,
    *                    "workingLife": 12,
    *                    "introduce": "我来介绍0001",
    *                    "experience": "6",
    *                    "detail": "[{\"title\":\"我很好溜哦\",\"content\":\"你好呀\"},{\"title\":\"ni我很好溜哦\",\"content\":\"你好呀1\"}]",
    *                    "status": 0
    *      }
    *	}
    *
    *
    * @apiErrorExample Error-Response:
    *		{
    *		    "result": 0,
    *		    "msg": "未授权访问"
    *		}
    */
    public function show($id){
        $field = [
            'artificer_id as id','name',
            'photo','sex','country',
            'birthday','credential','card_id as cardId',
            'mobilephone as mobilePhone','wechat','qq',
            'email','level','number',
            'working_life as workingLife','introduce','experience',
            'detail','status'
        ];
        $info = Artificer::select( $field )->where(['artificer_id'=>$id])->first();
        if( empty($info) ) return $this->error('没有找到专家信息哦');
        return $this->success( $info->toArray() );
    }
    /**
	 * @api {get} /artificer/up/:id 5.启用
	 * @apiName up
	 * @apiGroup Artificer
	 *
	 * @apiParam {Number} id 必填,职工id.
	 *
	 *
	 * 
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "token": "",
	 *	    "data": []
	 *	}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 */
    public function start($id){
        Artificer::where(['artificer_id'=>$id,'status'=>0])->update(['status'=>1]);
        Event::fire('artificer.up','启用专家 id: '.$id);
        return $this->success();
    }
    /**
	 * @api {get} /artificer/down/:id 6.禁用
	 * @apiName down
	 * @apiGroup Artificer
	 *
	 * @apiParam {Number} id 必填,职工id.
	 *
	 *
	 * 
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "token": "",
	 *	    "data": []
	 *	}
	 *
	 *
	 * @apiErrorExample Error-Response:
	 *		{
	 *		    "result": 0,
	 *		    "msg": "未授权访问"
	 *		}
	 */
    public function close($id){
        Artificer::where(['artificer_id'=>$id,'status'=>1])->update(['status'=>0]);
        Event::fire('artificer.down','禁用专家 id: '.$id);
        return $this->success();
    }
    /**
	 * @api {get} /artificer/checkNumberExists/:id 7.获取专家编码是否存在
	 * @apiName checkNumberExists
	 * @apiGroup Artificer
	 *
	 * @apiParam {Number} id            选填（新增的时候可不传）,职工id.
	 * @apiParam {Number} number        必填,专家编号.
	 *
	 *
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "token": "",
	 *	    "data": {
     *          "exists":0
     *      }
	 *	}
	 */
    public function checkNumberExists( $id=0 ){
        $param = $this->param;
        $number = isset( $param['number'] ) ? $param['number'] : $this->error('未填写专家编码');
        $number = '1'.$number;
        $exists = Artificer::select(['artificer_id as id'])->where(['number'=>$number])->where('pid','=',NULL)->first();
        if( empty($exists) ) return $this->success();
        $exists = $exists->toArray();
        if( $id == $exists['id'] ) return $this->success();
        return $this->error( '专家编号已存在', ERROR::ARTIFICER_NAME_EXISTS_ERROR );
    }
    /**
	 * @api {get} /artificer/checkNameExists/:id 8.获取专家名字是否存在
	 * @apiName checkNameExists
	 * @apiGroup Artificer
	 *
	 * @apiParam {Number} id            选填（新增的时候可不传）,职工id.
	 * @apiParam {Number} name          必填,专家名字.
	 *
	 *
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "token": "",
	 *	    "data": {
     *          "exists":0
     *      }
	 *	}
	 */
    public function checkNameExists( $id = 0 ){
        $param = $this->param;
        $name = isset( $param['name'] ) ? $param['name'] : $this->error('未填写专家名字');
        $exists = Artificer::select(['artificer_id as id'])->where(['name'=>$name])->where('pid','=',NULL)->first();
        if( empty($exists) ) return $this->success();
        $exists = $exists->toArray();
        if( $id == $exists['id'] ) return $this->success();
        return $this->error( '专家名称已存在', ERROR::ARTIFICER_NAME_EXISTS_ERROR );
    }
     /**
	 * @api {post} /artificer/export     9.导出专家列表
	 * @apiName     export
	 * @apiGroup    Artificer
	 *
	 * @apiParam {String} itemMainNum       可选,主体搜索条件类型。  1.专家姓名 2.在职编号
	 * @apiParam {String} keyword           可选,对应主体搜索关键词.
	 * @apiParam {String} orderField        可选, 排序字段目前只有两个 level 级别 status 状态
	 * @apiParam {String} orderBy           可选, 排序方式 desc 降序 asc 升序
	 * @apiParam {Number} page              可选,第几页。 默认为第一页
	 * @apiParam {Number} page_size         可选,取多少条。 默认为20条
	 *
	 *
	 * 
	 * 
	 * @apiSuccessExample Success-Response:
	 *	{
	 *	    "result": 1,
	 *	    "token": "",
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
    public function export(){
        $param = $this->param;
        $itemMainNum = isset( $param['itemMainNum'] ) ? $param['itemMainNum'] : 0;
        $keyword = isset( $param['keyword'] ) ? $param['keyword'] : '';
        $orderField = isset( $param['orderField'] ) ? $param['orderField'] : '';
        $orderBy = isset( $param['orderBy'] ) ? $param['orderBy'] : '';
        $page = isset($param['page']) ? max($param['page'],1) : 1;
        $pageSize = isset($param['page_size']) ? $param['page_size'] : 20;
        if( $pageSize <= 0 ) $pageSize = 20;
        if( $pageSize > 500 ) $pageSize = 500;
        if( (!$orderField && $orderBy) || ($orderField && !$orderBy) ) return $this->error('排序传值错误');
        $field = ['name','sex','country','working_life AS workingLife','level','number','status'];
        $obj = Artificer::select( $field )->where('pid','=',NULL);
        //手动设置页数
		AbstractPaginator::currentPageResolver(function() use ($page) {
		    return $page;
		});
        if( $itemMainNum != 0 && $keyword ){
            switch( $itemMainNum ){
                case 1:
                    $obj->where(['name'=>$keyword]);
                    break;
                case 2:
                    $obj->where(['number'=>$keyword]);
                    break;
                default:
                    break;
            }
        }
        if( !$orderField || !$orderBy )   $result = $obj->orderBy('artificer_id','desc')->paginate($pageSize)->toArray();
        if( $orderField && $orderBy ){ 
            switch( $orderField ){
                case 'status':
                    // 降序为 启用（1）在前 禁用（0）在后
                    $temp = ['asc'=>'desc','desc'=>'asc'];
                    $result = $obj->orderBy( $orderField,$temp[$orderBy])->paginate($pageSize)->toArray();
                    break;
                case 'level':
                    $result = $obj->orderBy( $orderField,$orderBy)->paginate($pageSize)->toArray();
                    break;
                default:
                    $result = $obj->orderBy('id','desc')->paginate($pageSize)->toArray();
                    break;
            }
        }
        $tempData = $result['data'];
        $title = '专家查询列表' .date('Ymd');
        $header = ['专家姓名','性别','国籍','工作年限','级别','在职编号','状态 '];
        $t1 = ['','男','女'];
        $t2 = ['','明星院长','院长'];
        $t3 = ['禁用','启用'];
        foreach( $tempData as &$val ){
            $val['sex'] = $t1[ $val['sex'] ];
            $val['level'] = $t2[ $val['level'] ];
            $val['status'] = $t3[ $val['status'] ];
        }
        Event::fire('artificer.export','导出专家查询列表');
        Excel::create($title, function($excel) use($tempData,$header){
            $excel->sheet('Sheet1', function($sheet) use($tempData,$header){
                $sheet->fromArray($tempData, null, 'A1', false, false);//第五个参数为是否自动生成header,这里设置为false
                $sheet->prependRow(1, $header);//添加表头
            });
        })->export('xls');
        exit;
    }
    // 接收天界或者修改的数据
    private function _formatReceiveData( $param ){
        $data = [];
        $data['photo'] = $photo = isset( $param['photo'] ) ? $param['photo'] : $this->error('个人图片未填写');
        $data['name'] = $name = isset( $param['name'] ) ? $param['name'] : $this->error('专家姓名未填写');
        $data['sex'] = $gender = isset( $param['sex'] ) ? $param['sex'] : $this->error('性别未填写');
        $data['country'] = $country = isset( $param['country'] ) ? $param['country'] : $this->error('国籍未填写');
        $data['birthday'] = $birthday = isset( $param['birthday'] ) ? $param['birthday'] : $this->error('出生日期未填写');
        $data['level'] = $level = isset( $param['level'] ) ? $param['level'] : $this->error('级别未填写');
        $data['number'] = $jobNumber = isset( $param['number'] ) ? $param['number'] : $this->error('在职编号未填写');
        $data['working_life'] = $jobYear = isset( $param['workingLife'] ) ? $param['workingLife'] : $this->error('工作年限未填写');
        $data['introduce'] = $signature = isset( $param['introduce'] ) ? $param['introduce'] : $this->error('个性签名未填写');
        $data['experience'] = $jobEmpiric = isset( $param['experience'] ) ? $param['experience'] : $this->error('从业经验未填写');
        $data['detail'] = $jobDetail = isset( $param['detail'] ) ? $param['detail'] : $this->error('个人介绍未填写');
        // 证件类型
        $credentialType = isset( $param['credential'] ) ? $param['credential'] : 0;
        $credentialValue = isset( $param['cardId'] ) ? $param['cardId'] : '';
        $data['mobilephone'] = $mobilePhone = isset( $param['mobilePhone'] ) ? $param['mobilePhone'] : '';
        $data['wechat'] = $wechat = isset( $param['wechat'] ) ? $param['wechat'] : '';
        $data['qq'] = $qq = isset( $param['qq'] ) ? $param['qq'] : '';
        $data['email'] = $email = isset( $param['email'] ) ? $param['email'] : '';
        if( $credentialType && $credentialValue ){ 
            $data['credential'] = $credentialType;
            $data['card_id'] = $credentialValue;
        }
        return $data;
    }
}