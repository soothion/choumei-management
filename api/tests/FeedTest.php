<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FeedTest  extends TestCase {
     use WithoutMiddleware;
    /**
     * A basic functional test example.
     *
     * @return void
     */
     
      public function testIndex(){
        //默认第一页
        $this->post('feed/index')            
             ->seeJson([
                    'result'=>1,
                   ]);
     }
     
      public function testDestroy(){
        //删除反馈
        $this->post('feed/destroy/1')            
             ->seeJson([
                    'date'=>[],
                   ]);
     }
     
}
