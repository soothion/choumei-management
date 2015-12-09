<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Log;

class PresentArticleCodeExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'set articleCode status as expire';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info("更新赠送券开始执行时间:".  date('Y-m-d H:i:s',time()));
        $affected = DB::update('update cm_present_article_code set status = 3 where NOW() > expire_at and status = 2');
        if($affected === false){
            Log::info(date('Y-m-d H:i:s',time())."更新赠送券状态失败,请联系管理员");
        }
        Log::info("更新赠送券执行完毕:". date('Y-m-d H:i:s',time()).";共更新数据量：".$affected);
        echo "执行完毕".date('Y-m-d H:i:s',time()).";共更新数据量：".$affected;
    }
    
    
}
