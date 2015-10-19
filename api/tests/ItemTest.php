<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Item;
use DB;
use App\Exceptions\ERROR;

class ItemTest extends TestCase
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
        $this->post('item/index')            
             ->seeJson([
                'result'=>1,
                'current_page'=>1,
             ]);
        
        //筛选店铺名称,搜索不存在的店铺名称,返回空数据
        $this->post('item/index',['itemname'=>str_random(20)])            
             ->seeJson([
                'data'=>[],
             ]);
    }
    
    
    public function testUpdate(){
         $this->withoutEvents();
         $item = Item::first();
         $id=$item->itemid;
         
//         $token=[$id,
//                'salonid'=>36,
//                'typeid'=>666,
//                'logo'=>1,'desc'=>1,
//                'itemType'=>1,
//               'itemname'=>str_random(20),
//                'priceStyle'=>1,
//                'price'=>1,
//                'priceDis'=>1,
//                'normMenu'=>str_random(20),
//                'normarr'=>str_random(20)
//             ];
//         $this->post("itemInfo/update/$id",$token)            
//             ->seeJson([
//                'result'=>1
//             ]); 
//         
         
    }
    
     public function testUpdateSpecialItem(){
          $this->withoutEvents();
          
          
     }
     public function testCrateSpecialItem(){
                    $this->withoutEvents();
//          $item = Item::first();
//          $item->itemname=str_random(20);
//          $field=['salonid','typeid','useLimit','logo','desc','itemType','itemname','priceStyle','price','priceDis','normMenu','normarr'];
//          $item = DB::table('salon_item')->select($field)->first();
     
          
//          $token=[
//                'salonid'=>36,
//                'typeid'=>666,
//                'logo'=>1,'desc'=>1,
//                'itemType'=>1,
//               'itemname'=>str_random(20),
//                'priceStyle'=>1,
//                'price'=>1,
//                'priceDis'=>1,
//                'normMenu'=>str_random(20),
//                'normarr'=>str_random(20)
//             ];
//          $this->post('itemInfo/create',$token)            
//             ->seeJson([
//                'result'=>1
//             ]); 
          
          
     }

}
