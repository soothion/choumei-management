<?php


use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CommissionTest  extends TestCase {
     use WithoutMiddleware;
    /**
     * A basic functional test example.
     *
     * @return void
     */
     
      public function testIndex(){
        //默认第一页
        $this->post('commission/index')            
             ->seeJson([
                     'result'=>1,
                   ]);

        //筛选salonsn
        $this->post('commission/index',['salonsn'=>'12345'])            
             ->seeJson([
                       'salonsn'=>'12345',
                  ]);

        //筛选salonsn,搜索不存在的店铺编号,返回空数据
        $this->post('commission/index',['salonsn'=>'none'])            
             ->seeJson([
                  'data'=>[],
                 ]);
     }
     
       public function testExport(){
         //导出佣金单
        $this->post('commission/export')            
             ->seeJson([
                         'result'=>1,
                   ]);
     }
     
        public function testShow(){
         //查看佣金单信息
        $this->post('commission/show/1')            
             ->seeJson([
                         'result'=>1,
                   ]);
     }
     

}