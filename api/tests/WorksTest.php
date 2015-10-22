<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Works;
use App\Stylist;
use App\Exceptions\ERROR;

class WorksTest extends TestCase
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
        $Stylist=Stylist::first();
        $id=$Stylist->stylistId;
             //基本正确性验证
        $this->get("works/index/$id")            
             ->seeJson([ 'result'=>1]);
               $this->withoutEvents();
        $id1 = 9999999;
        $this->get("works/index/$id1")    
             ->seeJson([
//                    'result'=>0,
//                    'code'=>ERROR::USER_NOT_FOUND
                ]); 

    }
    
     public function testCreate(){
        $this->withoutEvents();
        //创建作品works/create
        $Stylist=Stylist::first();
        $id=$Stylist->stylistId;
        $works = [
            'stylistId'=>$id,
            'description'=>str_random(20),
            'img'=>str_random(200)
            ];
        $this->post('works/create',$works)            
           ->seeJson([
              'result'=>1
           ]);
         //判断数据库是否存在此记录
        $this->seeInDatabase('hairstylist_works', ['description' => $works['description']]); 
  
     }
     
    public function testDel_list(){
        $Stylist=Works::orderBy("recId", "DESC")->first();
        $id=$Stylist->recId;
        $this->get("works/del_list/$id")            
             ->seeJson([ 'result'=>1]);
        
        //recId不存在
        $id=999999;
        $this->get("works/del_list/$id")            
             ->seeJson([ 'result'=>0]);
    }
    
//    public function testDel(){
//        $Stylist=Works::orderBy("recId", "DESC")->first();
//        $id=$Stylist->recId;
//        $works = [
//            'img'=>str_random(200)
//            ];
//        $this->get("works/del/$id",$works)            
//             ->seeJson([ 'result'=>1]); 
//    }
    
//     public function testUpdate(){
//        $this->withoutEvents();
//        $Stylist=Works::orderBy("recId", "DESC")->first();
//        $id=$Stylist->recId;
//        $data = [
//             'img'=>str_random(200)
//        ];
//        $this->post("works/update/$id",$data)
//             ->seeJson([
//                    'result'=>1
//                ]);
//        
//        //判断数据库是否存在此记录
//        $this->seeInDatabase('hairstylist_works', ['recId' => $id]); 
//
//    }
    
    
}