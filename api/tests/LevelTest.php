<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LevelTest  extends TestCase
{

    use WithoutMiddleware;
    /**
     * A basic functional test example.
     *
     * @return void
     */
    
      public function testIndex(){
        //查询等级
        $this->post('level/index')            
             ->seeJson([
                     'result'=>1,
                   ]);
      }
   
       public function testUpdate(){
        //修改等级
        $this->post('level/update/11')            
             ->seeJson([
                    'date'=>[],
                   ]);
      }
      
}
