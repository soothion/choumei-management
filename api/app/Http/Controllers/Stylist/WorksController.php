<?php  namespace  App\Http\Controllers\Stylist;

use App\Stylist;
use App\StylistWorks;
use App\FileImage;
use App\Http\Controllers\Controller;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;
use DB;
use PDO;
class WorksController extends Controller {
    /**
     * @api {post} /works/index/:id 1.����ʦ����Ʒ�б�����ڵ������
     * @apiName list
     * @apiGroup Works
     *
     * @apiParam {String} stylistId ����,����ʦID.
     *
     * @apiSuccess {String} stylistId ����ʦID.
     * @apiSuccess {String} stylistName ����ʦ����.
     * @apiSuccess {Number} mobilephone �ֻ���.
     * @apiSuccess {Numder} grade ���͵ȼ� 0û�еȼ� 1����ʦ 2�߼�����ʦ 3����ʦ 4�����ܼ�.
     * @apiSuccess {Number} fastGrade ����ȼ� 0û�еȼ� 1��ͨ��� 2�ܼ���.
     * @apiSuccess {Number} uploadNum �ϴ�����.
     * @apiSuccess {Number} num ��Ʒ��.
     * @apiSuccess {Number} recId ��ƷID.
     * @apiSuccess {Number} stylistId ����ʦID.
     * @apiSuccess {String} commoditiesImg ��Ʒ��.
     * @apiSuccess {String} description ��Ʒ����.
     * @apiSuccess {String} thumbImg �ϰ汾��Ʒ������ͼ.
     * @apiSuccess {String} works['img'] �°汾��Ʒ����  �ԣ���Ʒ������Ʒ������ͼ��Ϊһ����Ԫ.
     * @apiSuccess {String} salon['img'] ����ʦͷ������ͼ
     * 
     * @apiSuccessExample Success-Response:
     * {
     *    "result":1,
     *    "token":"",
     *    "data":
     *    {
     *           "works":
     *           [
     *              {
     *                   "id":1,
     *                   "stylist_id":38,
     *                   "image_ids":"1,2,3,4",
     *                   "status":"ON",
     *                   "add_time":1444820916,
     *                   "description":"\u8fd9\u662f\u4e00\u4e2a\u63cf\u8ff01",
     *                   "img":
     *                   [
     *                           {
     *                               "worksId":1,
     *                               "originImg":"http:\/\/img01.choumei.cn\/1\/785973\/201510081147144427607355578597387596.jpg"
     *                           },
     *                          ......
     *                           {
     *                               "worksId":4,
     *                               "originImg":"http:\/\/img01.choumei.cn\/1\/785973\/201509281548144342652083878597311884.jpg"
     *                           }
     *                   ]
     *               }
     *            ],
     *           "salonSelf":
     *               {
     *                   "stylistId":26,
     *                   "stylistName":"\u4f1a\u64b8\u7684\u5b69\u5b50\u4e0d\u4f24\u8eab",
     *                   "mobilephone":"13545108420",
     *                   "grade":4,
     *                   "fastGrade":2,
     *                   "num":0,
     *                   "stylistImg":"www.douyuTV.com"��
     *                   "img":null,
     *                   "uploadNum":0,
     *                   "salonname":"choumeitest_salon"
     *                },
     *           "salon":
     *           [
     *               {
     *                   "stylistId":25,
     *                   "stylistName":"\u4f1a\u64b8\u7684\u5b69\u5b50\u4e0d\u4f24\u8eab",
     *                   "mobilephone":"13545108420",
     *                   "grade":4,
     *                   "fastGrade":2,
     *                   "num":0,
     *                   "stylistImg":"www.douyuTV.com"��
     *                   "img":null,
     *                   "uploadNum":0,
     *                },
     *               {
     *                   "stylistId":27,
     *                   "stylistName":"\u4f1a\u64b8\u7684\u5b69\u5b50\u4e0d\u4f24\u8eab",
     *                   "mobilephone":"19441001801",
     *                   "grade":0,
     *                   "fastGrade":2
     *                   "num":0,
     *                   "uploadNum":0,
     *               }
     *           ]
     *    }
     *  }
     *
     *
     * @apiErrorExample Error-Response:
     *		{
     *		    "result": 0,
     *		    "msg": "δ��Ȩ����"
     *		}
     */
    public function index($stylistId){
        $field=['stylistId','stylistName','stylistImg','mobilephone','grade','fastGrade','salonId','img'];
        $stylist=Stylist::select($field)->where(array('stylistId'=>$stylistId))->first();
        if($stylist===false){
            throw new ApiException('����ʦID����', ERROR::MERCHANT_STYLIST_ID_ERROR);  
        }
        
        $salonStylist=Stylist::select($field)->where('salonId','=',$stylist['salonId'])->where('stylistId','<>',$stylistId )->get();
        $field2=['salonname']; 
        DB::connection()->setFetchMode(PDO::FETCH_ASSOC);
        $salon=DB::table('salon')->select($field2)->where(array('salonId'=>$stylist['salonId']))->first();
          
        $works=StylistWorks::getQuery()->where('stylist_id',$stylistId)->orderBy('add_time', 'desc')->get();
        $stylist->uploadnum = count($works);
        $stylist->num = 0;
        $stylist->salonname = $salon['salonname'];
        $query=array();
        foreach ($works as $key2 =>$value) {
            if($value['status'] == 'OFF') 
                continue;
            $stylist->num++;
            if(!empty($value["image_ids"])){
                $imageArr = explode(',', $value["image_ids"]);
                  
                $works[$key2]['img']=[];
                $imagecount=count($imageArr);
                for ($i = 0; $i <$imagecount; $i++) {
                    $image =FileImage::getQuery()->where('id',$imageArr[$i])->first();
                    $img['worksId']=$image["id"];
                    $img['originImg']=$image["url"];
                    $works[$key2]['img'][]=$img;
   
                }
                  
            }
        }
    

        foreach ($salonStylist as $key =>$value) {
            $salonStylist[$key]->num = 0;
            $works1= StylistWorks::where('stylist_id','=',$value->stylistId)->get();
            foreach ($works1 as $key1 =>$value) {
                if($value['status'] == 'ON')
                    $salonStylist[$key]->num++;
            }  
            $salonStylist[$key]->uploadNum= count($works1);
        }
         
         
         /*
        if ($stylist) {
            $num=0; 
            $works3= StylistWorks::where('stylist_id','=',$stylistId)->get();
            foreach ($works3 as $key7 =>$value) {
              if(!empty($value['image_ids'])){
                    $imageArr = explode(',', $value['image_ids']);
                    $num=$num+(count($imageArr));
                }  
             }
           $stylist->num=$num;
           $stylist->uploadNum=StylistWorks::where('stylist_id','=',$stylistId)->count();
           $stylist->salonname=$salon["salonname"];
         }
          */
         
        $query['works']=$works;
        $query['salonSelf']=$stylist;
        $query['salon']=$salonStylist;
        
        return $this->success($query);  
    }
    
    /**
     * @api {post} /works/del_list/:id  2.ɾ����Ʒ����
     * @apiName del_list
     * @apiGroup  Works
     *
     * @apiParam {Number} recId ����,��Ʒid.
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
     *		    "msg": "ɾ����Ʒ����ʧ��"
     *		}
     */
    public function  del_list($recId){
        $works=StylistWorks::where('id',$recId)->first();
        if($works==FALSE){
             throw new ApiException('��ƷID����', ERROR::MERCHANT_WORKS_ID_ERROR);
        }
        //����cm_file_image��������
        self::del_list2($works->image_ids);
        $query=  StylistWorks::where(array('id'=>$recId))->delete();
        if($query){
            return $this->success();
        }else{
            throw new ApiException('ɾ����Ʒʧ��', ERROR::MERCHANT_WORKS_DELETE_ERROR);
        }
    }
    
    public function  del_list2($image_ids){
        $imageArr = explode(',',$image_ids);
        for ($i = 0; $i < count($imageArr); $i++) {
            FileImage::where('id',$imageArr[$i])->delete();
        }
    }
    
    /**
     * @api {post} /works/del/:id  3.ɾ��������Ʒ
     * @apiName del
     * @apiGroup  Works
     *
     * @apiParam {Number} recId ����,��Ʒid.
     * @apiParam {String} img  ����,[woeksId]��Ʒ����.eg:  "1,2,3"  ��ƷID���ϣ��Զ��Ÿ���
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
     *		    "msg": "ɾ����Ʒʧ��"
     *		}
     */
    public function  del($recId){
        $param=$this->param;
        $works=  StylistWorks::where(array('id'=>$recId))->count();
        if($works==FALSE){
             throw new ApiException('��ƷID����', ERROR::MERCHANT_WORKS_ID_ERROR);
        }
        $data=array();
        if(!empty($param['img'])){
            $data['image_ids']=$param['img'];
            $query=  StylistWorks::where(array('id'=>$recId))->update($data);
        }else{
            $query=  StylistWorks::where(array('id'=>$recId))->delete();
        }
        if($query){
                return $this->success();
        }else{
                 throw new ApiException('ɾ��������Ʒʧ��', ERROR::MERCHANT_WORKS_DELETE_ERROR);
        }
    }
    
    /**
     * @api {post} /works/update/:id  4.�޸���Ʒ����
     * @apiName update
     * @apiGroup  Works
     *
     * @apiParam {Number} recId ����,��Ʒid.
     * @apiParam {String} img ����,[woekId]��Ʒ����.eg:  "1,2,3"  ��ƷID���ϣ��Զ��Ÿ���
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
     *		    "msg": "�޸���Ʒʧ��"
     *		}
     */
    public function  update($recId){
        $param=$this->param;
        $works=  StylistWorks::where(array('id'=>$recId))->count();
        if($works==FALSE){
             throw new ApiException('��ƷID����', ERROR::MERCHANT_WORKS_ID_ERROR);
        }
        $data=array();
        if(empty($param['img'])){
             throw new ApiException('��������', ERROR::MERCHANT_ERROR);
        }
        $data['image_ids']=$param['img'];
        $query=  StylistWorks::where(array('id'=>$recId))->update($data);
        if($query){
                return $this->success();
        }else{
                 throw new ApiException('�޸ĵ�����Ʒʧ��', ERROR::MERCHANT_WORKS_SAVE_ERROR);
        }
    }
    
    /**
     * @api {post} /works/create 5.������Ʒ����
     * @apiName create
     * @apiGroup  Works
     *
     * @apiParam {Number} stylistId ����,����ʦID.
     * @apiParam {String} description ����,��Ʒ����.
     * @apiParam {String} img  ����,[originImg]��Ʒ����. eg:"www,ee,qq,bb"    ��Ʒԭͼ·�����ϣ��Զ��Ÿ���
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
     *		    "msg": "������Ʒʧ��"
     *		}
     */
    public function  create(){
        $param=$this->param;
        if(empty($param['img'])||empty($param['stylistId'])){
             throw new ApiException('������Ʒ�Ĳ�������', ERROR::MERCHANT_ERROR);
        }
      
         $imageArr =json_decode($param['img'],true);
         $fileIds = array();
         $imagecount=count($imageArr);
         for ($i = 0; $i < $imagecount; $i++) {
             $data2['url']=$imageArr[$i]; 
             $fileImage=FileImage::create($data2);
             $fileIds[] = $fileImage->id;

         }
        $data['image_ids']= implode(',', $fileIds);
        $data['stylist_id']=$param['stylistId'];
        $data['add_time']= time();
        
        if(isset($param['description'])||$param['description']){
             $data['description']=$param['description'];
        }
        $query=  StylistWorks::create($data);
        if($query){
             return $this->success();
        }else{
             throw new ApiException('������Ʒʧ��', ERROR::MERCHANT_WORKS_CREATE_ERROR);
        }
    }  
}
