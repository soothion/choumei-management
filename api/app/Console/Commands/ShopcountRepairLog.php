<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ShopCountLog;

class ShopcountRepairLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopcount:repair_log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'repair log balance money!';

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
        $items = ShopCountLog::selectRaw("DISTINCT(`salon_id`) as `salon_id`")->get()->toArray();
        $salon_ids = array_column($items, "salon_id");
        asort($salon_ids);      
        foreach ($salon_ids as $salon_id)
        {
            $this->info("start to count NO {$salon_id} salon");
            $items = ShopCountLog::select(['id','type','money'])->where('salon_id',$salon_id)->orderBy('count_at','ASC')->orderBy('id','DESC')->get()->toArray();
            $last_balance = NULL;
            foreach ($items as $item)
            {
                $id = $item['id'];
                $type = intval($item['type']);
                $money = floatval($item['money']);
                $change_money = $money;
                if($type == ShopCountLog::TYPE_OF_COMMISSION || $type == ShopCountLog::TYPE_OF_SPEND)
                {
                    $change_money *= -1;
                }
                if(is_null($last_balance))
                {
                    $last_balance = 0;
                }
                $last_balance += $change_money;
                ShopCountLog::where('id',$id)->update(['balance_money'=>$last_balance]);
            }
        }
    }
}
