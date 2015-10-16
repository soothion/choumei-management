<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Stylist;
use JWTAuth;
use App\Exceptions\ERROR;

class StylistTest extends TestCase
{

    /**
     * 屏蔽中间件
     */
    use WithoutMiddleware;
    /**
     * 每次执行完之后清理垃圾数据
     */
    use DatabaseTransactions;

    public function testIndex()
    {
         $this->post('stylist/index')            
             ->seeJson([
                'result'=>1,
                'current_page'=>1,
             ]);

        $stylist = Stylist::first();
        //测试条件筛选
        $this->post('stylist/index',[
                'mobilephone'=>$stylist->mobilephone
                ])            
             ->seeJson([
                'stylistName'=>$stylist->stylistName,
             ]);

        //筛选username,搜索不存在的用户名,返回空数据
        $this->post('stylist/index',['mobilephone'=>str_random(20)])            
             ->seeJson([
                'data'=>[],
             ]);
    }
    
    public function testShow(){
        $stylist = Stylist::first();
        $id = $stylist->stylistId;
        $this->get("stylist/show/$id")
             ->seeJson($stylist->toArray());

        $id = 999999;
        $this->get("stylist/show/$id") 
             ->seeJson([
                    'result'=>0
                ]); 
    }
    
    
     public function testDel(){
        $Stylist=Stylist::orderBy("stylistId", "DESC")->first();
        $id=$Stylist->stylistId;
        $this->get("stylist/destroy/$id")         
             ->seeJson([ 'result'=>1]);
        
        //stylistId不存在
        $id=999999;
        $this->get("stylist/destroy/$id")            
             ->seeJson([ 'result'=>0]);
    }
    
     public function testDisabled(){
        $Stylist=Stylist::where('status','=',1)->orderBy("stylistId", "DESC")->first();
        $id=$Stylist->stylistId;
        $this->get("stylist/disabled/$id")         
             ->seeJson([ 'result'=>1]);
        
        //以禁用就不能被禁用
        $this->get("stylist/disabled/$id")            
             ->seeJson([ 'result'=>0]);
    }
     public function testEnable(){
        $Stylist=Stylist::where('status','=',2)->orderBy("stylistId", "DESC")->first();
        $id=$Stylist->stylistId;
        $this->get("stylist/enable/$id")         
             ->seeJson([ 'result'=>1]);
        
        //以禁用就不能被禁用
        $this->get("stylist/enable/$id")            
             ->seeJson([ 'result'=>0]);
    }
    
    
    public function testCreate(){
        $this->withoutEvents();
        //创建用户
        $salon=DB::table("salon")->first();
        $id=$salon->salonid;
        $stylist = [
            'salonId'=>630,
            'stylistName'=>"ww",
            'img'=>"ee",
            'mobilephone'=>13454546698,
            'signature'=>"ewwe",
            'sex'=>1,
            'stylistImg'=>"qwee",
            'birthday'=>"1434337405",
            'IDcard'=>"sdqwrdsaf",
            'sNumber'=>88,
            'job'=>"wrqwer",
            'workYears'=>8,
           ];
//        $field=['stylistId', 'salonId', 'stylistName','stylistImg','job','addTime','likeNum','signature','status','sex','qq','email','birthday','IDcard','sNumber','workYears','grade','workExp','educateExp','description','gradeType','osType','fastGrade'];
//        $stylist1=  Stylist::select($field)->first();
//        $stylist1['mobilephone']=13756565346;
//        $stylist= json_decode($stylist1,true);
        $this->post('stylist/create',$stylist)            
             ->seeJson([
                'result'=>1
             ]);
        //判断数据库是否存在此记录
        $this->seeInDatabase('hairstylist', ['mobilephone' => $stylist['mobilephone']]); 

        //判断电话重复
        $this->post('stylist/create',$stylist)            
             ->seeJson([
                'result'=>0,
                'code'=>ERROR::USER_EXIST,
             ]);
    }
     

}
