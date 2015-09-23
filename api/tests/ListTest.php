<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ListTest extends TestCase {
     use WithoutMiddleware;
    /**
     * A basic functional test example.
     *
     * @return void
     */
     public function  testCity(){
       //获取城市列表
       $this->post('list/city')            
            ->seeJson([
                            'result'=>1,
                     ]);
}
      public function  testDepartment(){
        //获取部门列表
        $this->post('list/department')            
             ->seeJson([
                            'result'=>1,
                      ]);
}
      public function  testPosition(){
        //获取职位列表
        $this->post('list/position/1')            
             ->seeJson([
                            'result'=>1,
                      ]);
}

      public function  testPermission(){
        //获取权限列表
        $this->post('list/permission')            
             ->seeJson([
                          'result'=>1,
                      ]);
}

      public function  testMenu(){
        //获取用户菜单
        $this->post('list/menu')            
             ->seeJson([
                             'result'=>1,
                      ]);
}
}
