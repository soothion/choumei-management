<?php  namespace  App\Http\Controllers\Stylist;

use App\Stylist;
use App\Works;
use App\Http\Controllers\Controller;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;
use DB;
class WorksController extends Controller {
    /**
     * @api {post} /Works/index 1.造型师的作品列表和所在店的其他
     * @apiName list
     * @apiGroup Works
     *
     * @apiParam {String} stylistId 必填,造型师ID.
     *
     * @apiSuccess {String} stylistId 造型师ID.
     * @apiSuccess {String} stylistName 造型师名称.
     * @apiSuccess {Number} mobilephone 手机号.
     * @apiSuccess {Numder} grade 悬赏等级 0没有等级 1美发师 2高级美发师 3造型师 4艺术总监.
     * @apiSuccess {Number} fastGrade 快剪等级 0没有等级 1普通快剪 2总监快剪.
     * @apiSuccess {Number} uploadNum 上传次数.
     * @apiSuccess {Number} num 作品数.
     * @apiSuccess {Number} recId 作品ID.
     * @apiSuccess {Number} stylistId 造型师ID.
     * @apiSuccess {String} commoditiesImg 作品集.
     * @apiSuccess {String} description 作品描述.
     * @apiSuccess {String} thumbImg 作品集缩略图.
     * @apiSuccess {String} img 作品集合  以（作品集和作品集缩略图）为一个单元.
     * 
     * 
     * @apiSuccessExample Success-Response:
     * 
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "未授权访问"
     *		}
     */
    public function index($stylistId){
        $field=['stylistId', 'salonId'];
        $stylist=Stylist::select($field)->where(array('stylistId'=>$stylistId))->first();
        if($stylist===false){
            throw new ApiException('造型师ID出错', ERROR::MERCHANT_STYLIST_ID_ERROR);  
        }
        $field1=['stylistId','stylistName', 'mobilephone','grade','fastGrade'];
        $salonStylist=Stylist::select($field1)->where(array('salonId'=>$stylist['salonId']))->get();
        $works=Works::where(array('stylistId'=>$stylistId))->orderBy('recId', 'desc')->get();
        $query=array();
        foreach ($works as $key2 =>$value) {
             if(!empty($works['img'])){
                  $works->img= json_decode($works['img'], true);
             }
        }
        
        foreach ($salonStylist as $key =>$value) {
            $num=0; 
            $works1= Works::where('stylistId','=',$value->stylistId)->get();
            foreach ($works1 as $key1 =>$value) {
                if(!empty($works1['img'])){
                    $image=  json_decode($works1['img'],true);
                    $num=$num+(count($image)/2);
                }  else {   
                    $num=$num+1;
                }
                
             }
           $salonStylist->num=$num;

         }
        $salonStylist->uploadNum=DB::table('hairstylist_works')->where('stylistId','=',$value->stylistId)->count();
        $query['works']=$works;
        $query['salon']=$salonStylist;
        return $this->success($query);  
    }
    
    /**
     * @api {post} /Works/del_list/:id  2.删除作品集合
     * @apiName del_list
     * @apiGroup  Works
     *
     * @apiParam {Number} recId 必填,作品id.
     * 
     * 
     * @apiSuccessExample Success-Response:
     *	{
     *	    "result": 1,
     *	    "msg": "",
     *	    "data": {
     *	    }
     *	}
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "删除造型师失败"
     *		}
     */
    public function  del_list($recId){
        $works=  Works::where(array('recId'=>$recId))->count();
        if($works==FALSE){
             throw new ApiException('作品ID出错', ERROR::MERCHANT_WORKS_ID_ERROR);
        }
        $query=  Works::where(array('recId'=>$recId))->delete();
        if($query){
            return $this->success();
        }else{
            throw new ApiException('删除作品失败', ERROR::MERCHANT_WORKS_DELETE_ERROR);
        }
    }
    /**
     * @api {post} /Works/del/:id  3.删除单个作品
     * @apiName del
     * @apiGroup  Works
     *
     * @apiParam {Number} recId 必填,作品id.
     * @apiParam {String} img 必填,作品集合  以（作品集和作品集缩略图）为一个单元，（没值就传空）.
     * 
     * 
     * @apiSuccessExample Success-Response:
     *	{
     *	    "result": 1,
     *	    "msg": "",
     *	    "data": {
     *	    }
     *	}
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "删除造型师失败"
     *		}
     */
    public function  del($recId){
        $param=$this->param;
        $works=  Works::where(array('recId'=>$recId))->count();
        if($works==FALSE){
             throw new ApiException('作品ID出错', ERROR::MERCHANT_WORKS_ID_ERROR);
        }
        $data=array();
        if(!empty($param['img'])){
            $data['img']=json_encode($param['img']);
            $query=  Works::where(array('recId'=>$recId))->update($data);
        }else{
            $query=  Works::where(array('recId'=>$recId))->delete();
        }
        if($query){
                return $this->success();
        }else{
                 throw new ApiException('删除单个作品失败', ERROR::MERCHANT_WORKS_DELETE_ERROR);
        }
    }
    
    /**
     * @api {post} /Works/update/:id  4.修改作品集合
     * @apiName update
     * @apiGroup  Works
     *
     * @apiParam {Number} recId 必填,作品id.
     * @apiParam {String} description 必填,作品描述.
     * 
     * 
     * @apiSuccessExample Success-Response:
     *	{
     *	    "result": 1,
     *	    "msg": "",
     *	    "data": {
     *	    }
     *	}
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "删除造型师失败"
     *		}
     */
    public function  update($recId){
        $param=$this->param;
        $works=  Works::where(array('recId'=>$recId))->count();
        if($works==FALSE){
             throw new ApiException('作品ID出错', ERROR::MERCHANT_WORKS_ID_ERROR);
        }
        $data=array();
        if(isset($param['description'])||$param['description']){
             $data['description']=$param['description'];
        }
        $query=  Works::where(array('recId'=>$recId))->update($data);
        if($query){
             return $this->success();
        }else{
             throw new ApiException('修改作品失败', ERROR::MERCHANT_WORKS_SAVE_ERROR);
        }
    }
    
    /**
     * @api {post} /Works/create 5.新增作品集合
     * @apiName create
     * @apiGroup  Works
     *
     * @apiParam {Number} stylistId 必填,造型师ID.
     * @apiParam {String} description 必填,作品描述.
     * @apiParam {String} img 必填,作品集合.
     * 
     * 
     * @apiSuccessExample Success-Response:
     *	{
     *	    "result": 1,
     *	    "msg": "",
     *	    "data": {
     *	    }
     *	}
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "删除造型师失败"
     *		}
     */
    public function  create(){
        $param=$this->param;
        if(empty($param['img'])||empty($param['stylistId'])){
             throw new ApiException('创建作品的参数不齐', ERROR::MERCHANT_ERROR);
        }
        $data['img']=json_encode($param['img']);
        $data['stylistId']=$param['stylistId'];
        if(isset($param['description'])||$param['description']){
             $data['description']=$param['description'];
        }
        $query=  Works::create($data);
        if($query){
             return $this->success();
        }else{
             throw new ApiException('创建作品失败', ERROR::MERCHANT_WORKS_CREATE_ERROR);
        }
    }  
    
    
    /**
     * @api {post} /Works/uploadfile 6.上传作品集合
     * @apiName uploadfile
     * @apiGroup  Works
     * 
     * @apiSuccessExample Success-Response:
     * 
     * @apiErrorExample Error-Response:
     * 
     */
    
    public function uploadfile() {
        $upload = new \Think\Upload();            // 实例化上传类
        $upload->maxSize = 3145728 ;              // 设置附件上传大小
        $upload->allowExts = array('jpg', 'png'); // 设置附件上传类型
        $upload->savePath = 'menlist/';		  // 设置附件上传目录
        $return = array('res'=>0,'data'=>'上传失败');
        $file_info = @current($upload->upload()); //上传成功 获取上传文件信息
        if(!$file_info){
               exit(json_encode($return));
        }else{
            $pre  = C('IMG_PATH');
            $file = ltrim(C('UPLOAD_PATH'),'./').$file_info['savepath'].$file_info['savename'];
            $temp = getimagesize($file);
            $endName = strtolower(end(explode(".",$file_info['savename'])));
            if(!in_array($endName, array('jpg', 'png'))){
            	exit(json_encode(array('res'=>0,'data'=>'图片格式错误！')));
            }
            //if(!$temp){exit(json_encode(array('res'=>0,'data'=>'图片格式错误！')));}
            if($temp[0] != 420 || $temp[1] != 492) exit(json_encode(array('res'=>0,'data'=>'图片尺寸错误')));
            //if(filesize($file) > 800*1024) exit(json_encode(array('res'=>0,'data'=>'图片大小错误')));  5.0没有大小限制
            $img = $pre.$file;
            $return = array('res'=>1,'data'=>$img);
            exit(json_encode($return));
        }
    }
    
}
