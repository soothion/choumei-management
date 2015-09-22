<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogTest {
    use WithoutMiddleware;
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testIndex(){
        //默认第一页
        $this->post('log/index')            
             ->seeJson([
                     'result'=>1,
                   ]);

        //筛选username
        $this->post('log/index',['username'=>'soothion'])            
             ->seeJson([
                        'username'=>'soothion',
                  ]);

        //筛选username,搜索不存在的用户名,返回空数据
        $this->post('log/index',['username'=>'none'])            
             ->seeJson([
                  'data'=>[],
                 ]);
     }
    
    public function testExport(){
         //导出日志
        $this->post('log/export')            
             ->seeJson([
                         'result'=>1,
                   ]);
     }
    
}
