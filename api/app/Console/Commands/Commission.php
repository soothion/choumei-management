<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Order;
use DB;
use App\ShopCount;

class Commission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '初始化佣金';
    
    protected $controller = null;

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
       $this->commission();
    }

    /**
     * 佣金单流水
     */
    public function commission(){
        $query = Order::leftJoin('commission_log','order.ordersn','=','commission_log.ordersn')
            ->join('salon','salon.salonid','=','order.salonid')
            ->where('commission_log.id','=',NULL)
            ->select('order.ordersn','order.priceall','order.pay_time','order.use_time','order.salonid','salon.salonGrade','salon.merchantId');
        $count = $query->count();
        $pageSize = 10000;
        $totalPage = ceil($count/$pageSize);
        $this->info('总共'.$count.'条订单未计算佣金');
        for ($page=0; $page < $totalPage; $page++) { 
            $offset = $page*$pageSize;
            $orders = $query->skip($offset)->take($pageSize)->get();
            foreach ($orders as $key => $order) {
                $num = $offset+$key+1;
                $this->info('正在处理第'.$num.'/'.$count.'条数据');
                $data['ordersn'] = $order->ordersn;
                $data['salonid'] = $order->salonid;
                $rate = intval($order->pay_time)<strtotime('20150801')?9.09:9.00;
                $data['rate'] = $rate;
                $data['grade'] = $order->salonGrade;
                $amount = floatval($order->priceall)*$rate/100;
                $amount = round($amount,2);
                $data['amount'] = $amount;
                $date = date('Y-m-d H:i:s',$order->use_time);
                $data['updated_at'] = $date;
                $data['created_at'] = $date;
                $result = \App\CommissionLog::create($data);
                if($result){
                    $commission = \App\Commission::where('salonid','=',$order->salonid)->where('date','=',date('Y-m-d',$order->use_time))->first();
                    if($commission){
                        $commission->update(['amount'=>$amount+$commission->amount]);
                    }
                    else{
                        $commission = new \App\Commission;
                        $commission->sn = $commission::getSn();
                        $commission->salonid = $order->salonid;
                        $commission->amount = $amount;
                        $commission->date = $date;
                        $now = date('Y-m-d H:i:s');
                        $data['updated_at'] = $now;
                        $data['created_at'] = $now;
                        $commission->save();
                    }
                    ShopCount::count_bill_by_commission_money($order->salonid,$order->merchantId,$amount,'佣金率'.$rate.'%',$date);
                    $this->info('订单'.$order->ordersn.'处理成功');
                }                    
                else
                    $this->error('订单'.$order->ordersn.'处理失败');
            }
        }
    }
}
