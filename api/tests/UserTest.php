<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
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
        $this->post('user/index')            
             ->seeJson([
                'result'=>1,
             ]);

        //筛选username
        $this->post('user/index',['username'=>'soothion'])            
             ->seeJson([
                'username'=>'soothion',
             ]);

        //筛选username,不存在的用户名
        $this->post('user/index',['username'=>'none'])            
             ->seeJson([
                'data'=>[],
             ]);
    }

    public function testCreate(){
        //创建用户,以当前时间戳为用户名
        $time = time();
        $this->post('user/create',['username'=>$time,'password'=>'cm123456','roles'=>[1]])            
             ->seeJson([
                'result'=>1,
             ]);
        //判断数据库是否存在此记录
        $this->seeInDatabase('managers', ['username' => $time]);
        //触发user.create事件
        $this->expectsEvents(App\Events\UserRegistered::class);   
    }
}
