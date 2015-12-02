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

class ArtificerAssistantController extends Controller{
    /**
	 * @api {post} /assistant/index     1.专家助手列表
	 * @apiName index
	 * @apiGroup Assistant
	 *
	 * @apiParam {String} itemMainNum       可选,主体搜索条件类型。  1.专家姓名 2.在职编号 3.手机号码 4.所属专家姓名
	 * @apiParam {String} keyword           可选,对应主体搜索关键词.
	 * @apiParam {String} orderField        可选, 排序字段支持 status 状态
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
	 * @apiSuccess {String} relegation      归属专家
	 * @apiSuccess {String} mobilePhone     手机号码
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
     *                  "relegation": "XIAOd，DDD",
     *                  "mobilePhone": "15988829966",
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
        $field = ['artificer_id as id','name','sex','country','working_life AS workingLife','level','number','pid','mobilephone as mobilePhone','status'];
        $obj = Artificer::select( $field )->whereRaw('pid is not NULL');
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
                case 3:
                    $obj->where(['mobilephone'=>$keyword]);
                    break;
                case 4:
                    $artificer = Artificer::select(['artificer_id'])->where(['pid'=>NULL,'status'=>'NOM','name'=>$keyword])->first();
                    if(empty($artificer)) return $this->success();
                    $artificerId = $artificer->toArray()['artificer_id'];
                    $obj->whereRaw(' pid like "%'.$artificerId.'%"');
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
                default:
                    $result = $obj->orderBy('id','desc')->paginate($pageSize)->toArray();
                    break;
            }
        }
        $result = $this->_formatListData( $result );
        return $this->success( $result );
    }
    /**
    * @api {Post} /assistant/add    2.专家助理添加
    * @apiName add
    * @apiGroup Assistant
    *
    * @apiParam {String} photo          必填,个人照片.
    * @apiParam {String} name           必填,姓名.
    * @apiParam {Number} sex            必填,性别 1.男 2.女
    * @apiParam {String} country        必填,韩国.
    * @apiParam {String} birthday       必填,生日 格式如 2015-02-22.
    * @apiParam {Number} level          必填,级别 1明星院长； 2院长.
    * @apiParam {String} number         必填,在职编号.
    * @apiParam {Number} workingLife    必填,工作年限.
    * @apiParam {String} pid            必填,所属专家. 如 1,2,3
    * @apiParam {String} introduce      必填,个性签名.
    * @apiParam {Number} credential     选填,证件类型 0无填写类型 1身份证； 2军官证； 3驾驶证； 4护照.
    * @apiParam {Number} cardId         选填,证件类型所对应的证件号码.
    * @apiParam {Number} mobilePhone    选填,电话.
    * @apiParam {Number} wechat         选填,微信.
    * @apiParam {Number} qq             选填,qq.
    * @apiParam {Number} email          选填,电子邮箱.
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
        Event::fire('assistant.add','添加专家助理 id: '.$lastId);
        return $this->success();
    }
    /**
    * @api {Post} /assistant/update  3.专家助手编辑
    * @apiName update
    * @apiGroup Assistant
    *
    * @apiParam {Number} id             必填,专家助手id.
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
        Event::fire('assistant.update','编辑专家助理 id: '.$id);
        return $this->success();
    }
    /**
    * @api {Get} /assistant/show/:id  4.专家助理信息
    * @apiName show
    * @apiGroup Assistant
    *
    * @apiSuccess {Number} id             专家助手id
    * @apiSuccess {String} photo          必填,个人照片.
    * @apiSuccess {String} name           必填,姓名.
    * @apiSuccess {Number} sex            必填,性别 1.男 2.女
    * @apiSuccess {String} birthday       必填,生日 格式如 2015-02-22.
    * @apiSuccess {Number} level          必填,级别 1明星院长； 2院长.
    * @apiSuccess {String} number         必填,在职编号.
    * @apiSuccess {Number} workingLife    必填,工作年限.
    * @apiSuccess {String} introduce      必填,个性签名.
    * @apiSuccess {Number} credential     必填,证件类型  1身份证； 2军官证； 3驾驶证； 4护照.
    * @apiSuccess {String} cardId         必填,证件类型所对应的证件号码.
    * @apiSuccess {String} mobilePhone    必填,电话.
    * @apiSuccess {String} wechat         选填,微信.
    * @apiSuccess {String} qq             选填,qq.
    * @apiSuccess {String} email          选填,电子邮箱.
	* @apiSuccess {Number} status         状态标识. 1:正常启用，0:禁用
	* @apiSuccess {String} pid            选中的专家id
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
    *                    "status": 0,
    *                    "pid": "5,1,2"
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
            'working_life as workingLife','introduce',
            'status','pid'/*'detail','experience'*/
        ];
        $info = Artificer::select( $field )->where(['artificer_id'=>$id])->first();
        if( empty($info) ) return $this->error('没有找到专家信息哦');
        return $this->success( $info->toArray() );
    }
    /**
	 * @api {get} /assistant/up/:id 5.启用
	 * @apiName up
	 * @apiGroup Assistant
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
        Event::fire('assistant.up','启用专家助理 id: '.$id);
        return $this->success();
    }
    /**
	 * @api {get} /assistant/down/:id 6.禁用
	 * @apiName     down
	 * @apiGroup    Assistant
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
        Event::fire('assistant.down','禁用专家助理 id: '.$id);
        return $this->success();
    }
    /**
	 * @api {get} /assistant/checkNumberExists/:id 7.获取助理专家编码是否存在
	 * @apiName     checkNumberExists
	 * @apiGroup    Assistant
	 *
	 * @apiParam {Number} id              选填（新增的时候可不传）,职工id.
	 * @apiParam {Number} number          必填,助理专家编号.
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
    public function checkNumberExists( $id ){
        $param = $this->param;
        $number = isset( $param['number'] ) ? $param['number'] : $this->error('未填写助理专家编码');
        $exists = Artificer::select(['artificer_id as id'])->where(['number'=>$number,'artificer_id'=>$id])->whereRaw('pid is not NULL')->first();
        if( empty($exists) ) return $this->success();
        $exists = $exists->toArray();
        if( $id == $exists['id'] ) return $this->success();
        return $this->error( '专家助理编号已存在', ERROR::ARTIFICER_NAME_EXISTS_ERROR );
    }
    /**
	 * @api {get} /assistant/checkNameExists/:id 8.获取助理专家名字是否存在
	 * @apiName checkNameExists
	 * @apiGroup Assistant
	 *
	 * @apiParam {Number} id            选填（新增的时候可不传）,职工id.
	 * @apiParam {String} name          必填,助理专家名字.
	 *
	 *
	 * @apiSuccess {Number} exists      状态标识. 0:不存在，1:存在
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
    public function checkNameExists( $id ){
        $param = $this->param;
        $name = isset( $param['name'] ) ? $param['name'] : $this->error('未填写专家名字');
        $exists = Artificer::select(['artificer_id as id'])->where(['name'=>$name,'artificer_id'=>$id])->whereRaw('pid is not NULL')->first();
        if( empty($exists) ) return $this->success();
        $exists = $exists->toArray();
        if( $id == $exists['id'] ) return $this->success();
        return $this->error( '专家助理编号已存在', ERROR::ARTIFICER_NAME_EXISTS_ERROR );
    }
    /**
	 * @api {get} /assistant/getArtificer   9.获取专家名字
	 * @apiName     getArtificer
	 * @apiGroup    Assistant
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
	 *	    "data": [
     *          {
     *              "id": 1,
     *              "name": "XIAOd"
     *          }
     *      ]
	 *	}
	 */
    public function getArtificer(){
        $field = ['artificer_id as id','name'];
        $result = Artificer::select($field)->where(['pid'=>NULL])->get()->toArray();
        return $this->success( $result );
    }
    /**
	 * @api {post} /assistant/export     10.导出专家助手列表
	 * @apiName export
	 * @apiGroup Assistant
	 *
	 * @apiParam {String} itemMainNum       可选,主体搜索条件类型。  1.专家姓名 2.在职编号 3.手机号码 4.所属专家姓名
	 * @apiParam {String} keyword           可选,对应主体搜索关键词.
	 * @apiParam {String} orderField        可选, 排序字段支持 status 状态
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
        $field = ['artificer_id as id','name','sex','country','working_life AS workingLife','level','number','pid','mobilephone as mobilePhone','status'];
        $obj = Artificer::select( $field )->whereRaw('pid is not NULL');
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
                case 3:
                    $obj->where(['mobilephone'=>$keyword]);
                    break;
                case 4:
                    $artificer = Artificer::select(['artificer_id'])->where(['pid'=>NULL,'status'=>'NOM','name'=>$keyword])->first();
                    if(empty($artificer)) return $this->success();
                    $artificerId = $artificer->toArray()['artificer_id'];
                    $obj->whereRaw(' pid like "%'.$artificerId.'%"');
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
                default:
                    $result = $obj->orderBy('id','desc')->paginate($pageSize)->toArray();
                    break;
            }
        }
        $result = $this->_formatListData( $result );
        $t = $result['data'];
        $title = '专家查询列表' .date('Ymd');
        $header = ['专家姓名','性别','国籍','工作年限','级别','在职编号','所属专家','手机号码','状态 '];
        $t1 = ['','男','女'];
        $t2 = ['','明星院长','院长'];
        $t3 = ['禁用','启用'];
        $tempData = [];
        foreach( $t as $key=>$val ){
            $tempData[$key][] = $val['name'];
            $tempData[$key][] = $t1[ $val['sex'] ];
            $tempData[$key][] = $val['country'];
            $tempData[$key][] = $val['workingLife'];
            $tempData[$key][] = $t2[ $val['level'] ];
            $tempData[$key][] = $val['number'];
            $tempData[$key][] = $val['relegation'];
            $tempData[$key][] = $val['mobilePhone'];
            $tempData[$key][] = $t3[ $val['status'] ];
            
        }
        Event::fire('assistant.export','导出专家助手查询列表');
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
        $data['birthday'] = $birthday = isset( $param['birthday'] ) ? $param['birthday'] : $this->error('出生日期未填写');
        $data['level'] = $level = isset( $param['level'] ) ? $param['level'] : $this->error('级别未填写');
        $data['number'] = $jobNumber = isset( $param['number'] ) ? $param['number'] : $this->error('在职编号未填写');
        $data['working_life'] = $jobYear = isset( $param['workingLife'] ) ? $param['workingLife'] : $this->error('工作年限未填写');
        $data['pid'] = $pid = isset( $param['pid'] ) ? $param['pid'] : $this->error('所属专家未填写');
        $data['introduce'] = $signature = isset( $param['introduce'] ) ? $param['introduce'] : $this->error('个性签名未填写');
//        $data['experience'] = $jobEmpiric = isset( $param['experience'] ) ? $param['experience'] : $this->error('从业经验未填写');
//        $data['detail'] = $jobDetail = isset( $param['detail'] ) ? $param['detail'] : $this->error('个人介绍未填写');
        // 证件类型
        $credentialType = isset( $param['credential'] ) ? $param['credential'] : 0;
        $credentialValue = isset( $param['cardId'] ) ? $param['cardId'] : '';
        $data['mobilephone'] = $mobilePhone = isset( $param['mobilePhone'] ) ? $param['mobilePhone'] : '';
        
        if( empty($credentialType) && empty($credentialValue) ) return $this->error('身份证件未填写');
        if( empty($mobilePhone)) return $this->error('手机号码未填写');
        
        $data['wechat'] = $wechat = isset( $param['wechat'] ) ? $param['wechat'] : '';
        $data['qq'] = $qq = isset( $param['qq'] ) ? $param['qq'] : '';
        $data['email'] = $email = isset( $param['email'] ) ? $param['email'] : '';
        $data['country'] = '';
        if( $credentialType && $credentialValue ){ 
            $data['credential'] = $credentialType;
            $data['card_id'] = $credentialValue;
        }
        return $data;
    }
    // 格式化返回列表信息
    private function _formatListData( $result ){
        $tempAll = [];
        $tempItem = [];
        $tempName = [];
        foreach( $result['data'] as $val ){
           $pid = explode( ',',$val['pid'] );
           array_push( $tempItem , $pid );
           $tempAll = array_merge( $tempAll , $pid );
        }
        $uniqueArr = array_unique($tempAll);
        foreach( $uniqueArr as $val ){
            $name = $this->_getArtificerNameById( $val );
            if($name === false) $this->error('获取归属专家id错误');
            $tempName[ $val ] = $name;
        }
        return $this->_formatSigleToString( $result,$tempItem,$tempName );
    }
    // 根据职工id获取职工的姓名
    private function _getArtificerNameById( $artificerId ){
        $field = ['name'];
        $name = Artificer::select($field)->where(['artificer_id'=>$artificerId])->first();
        if(empty($name)) return false;
        return $name->toArray()['name'];
    }
    // 工具方法，将 101,102 这样的格式转换为对应的名字 如 101对应"我" 102对应"他" ===> 我,他
    private function _formatSigleToString($result,$tempItem,$tempName){
        foreach( $tempItem as $key => $val ){
            $str = '';
            foreach( $val as $v ){
                if( isset( $tempName[$v] ) ) $str .= '，'.$tempName[ $v ];
            }
            $result['data'][$key]['relegation'] = ltrim($str,'，');
            unset( $result['data'][$key]['pid'] );
        }
        return $result;
    }
}