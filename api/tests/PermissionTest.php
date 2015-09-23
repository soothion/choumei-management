<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PermissionTest extends TestCase {
     use WithoutMiddleware;
    /**
     * A basic functional test example.
     *
     * @return void
     */
      public function testIndex()
    {
        //默认第一页
        $this->post('permission/index')            
             ->seeJson([
                     'result'=>1,
                   ]);

        //筛选role_id
        $this->post('permission/index',['role_id'=>1])            
             ->seeJson([
                        'role_id'=>1,
                  ]);

        //筛选role_id,搜索不存在的角色编号,返回空数据
        $this->post('permission/index',['role_id'=>'none'])            
             ->seeJson([
                  'data'=>[],
                 ]);
     }
     public function  testShow(){
         //用户id筛选
        $this->post('permission/show/1')            
             ->seeJson([
                      'result'=>1,
                 ]);
     }
     public function  testUpdate(){
         //修改权限
        $this->post('permission/update/1',['inherit_id'=>66,'name'=>'用户管理','slug'=>'manager.index','descrition'=>'','note'=>''])            
             ->seeJson([
                        'data'=>[],
                 ]);
     }
     public function testCreate(){
         //创建权限
        $this->post('permission/create',['inherit_id'=>66,'name'=>'用户管理','slug'=>'manager.index','descrition'=>'ww','note'=>'aa'])            
             ->seeJson([
                        'data'=>[],
                 ]);
         
     }
     public function testExport(){
         //导出权限
        $this->post('permission/export')            
             ->seeJson([
                         'result'=>1,
                   ]);
     }
     
}
