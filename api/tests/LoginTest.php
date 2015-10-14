<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class LoginTest extends TestCase {
     use WithoutMiddleware;
    /**
     * A basic functional test example.
     *
     * @return void
     */
     public function  testCaptcha(){
       //获取验证码
       $this->post('captcha',['uniqid'=>'唯一标识'])            
                ->seeJson([
                           'result'=>1,
                   ]);
}


public function testLogin(){
     //登录，数据正确   //没有数据可以验证
      $this->post('login',['username'=>'13312345678','password'=>'123456','captcha'=>'123456','uniqid'=>'123456'])            
           ->seeJson([
                      'result'=>1,
             ]);
      
      //登录，验证码错误
      $this->post('login',['username'=>'13312345678','password'=>'123456','captcha'=>'re21324','uniqid'=>'123456'])            
             ->seeJson([
                      'uid'=>'验证码错误',
             ]);
}

public function  testLogout(){
     //退出登录
     $this->post('logout')            
          ->seeJson([
                     'data'=>[],
             ]);
}
}
