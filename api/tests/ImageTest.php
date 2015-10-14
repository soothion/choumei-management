<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ImageTest  extends TestCase {
     use WithoutMiddleware;
    /**
     * A basic functional test example.
     *
     * @return void
     */
     
       public function testIndex(){
        //默认第一页
        $this->post('ImageStyle/index')            
             ->seeJson([
                    'result'=>1,
                   ]);
     }
     
     
       public function testCreate(){
        //添加风格
        $this->post('ImageStyle/create',['style'=>2,'length'=>3,'curl'=>1,'color'=>2,'original'=>'wwww','thumb'=>'www'])            
             ->seeJson([
                     'date'=>[],
                   ]);
     }
     
     
     
     public function testDestroy(){
           $this->post('ImageStyle/destroy/3333')            
             ->seeJson([
                     'date'=>[],
                   ]);
     }
     
     
     public  function  testUpdate(){
          $this->post('ImageStyle/update/3666')            
             ->seeJson([
                     'date'=>[],
                   ]);
     }
     
     
     public function  testShow(){
          $this->post('ImageStyle/show/6666')            
             ->seeJson([
                  'result'=>1,
                   ]);
     }
}
