<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RoleTest extends TestCase
{

    use WithoutMiddleware;
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testIndex()
    {
        //默认第一页
        $this->post('role/index')            
             ->seeJson([
                'result'=>1,
             ]);

    }

    public function testCreate(){
        //创建用户,以当前时间戳为用户名
        $time = time();
        $this->post('role/create',['name'=>$time,'description'=>'测试角色'])            
             ->seeJson([
                'result'=>1,
             ]);
        //判断数据库是否存在此记录
        $this->seeInDatabase('roles', ['name' => $time]); 
    }
}
