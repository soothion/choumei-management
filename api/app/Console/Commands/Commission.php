<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Order;
use DB;

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
        $query = Order::leftJoin('commission','order.ordersn','=','commission.ordersn')
            ->join('salon','salon.salonid','=','order.salonid')
            ->where('commission.id','=',NULL)
            ->select('order.ordersn','order.priceall','order.pay_time','order.salonid','salon.salonGrade');
        $count = $query->count();
        $pageSize = 10000;
        $totalPage = ceil($count/$pageSize);
        $this->info('总共'.$count.'条订单未计算佣金');
        for ($page=0; $page < $totalPage; $page++) { 
            $offset = $page*$pageSize;
            $orders = $query->skip($offset)->take($pageSize)->get();
            foreach ($orders as $key => $order) {
                $num = $offset+$key+1;
                if($num>99999){
                    $this->error('每天只能生成99999条佣金单,亲明天再来,BYE~~');
                    die;
                }
                $this->info('正在处理第'.$num.'/'.$count.'条数据');
                $data['ordersn'] = $order->ordersn;
                $data['salonid'] = $order->salonid;
                $data['sn'] = \App\Commission::getSn();
                $rate = intval($order->pay_time)<strtotime('20150801')?9.09:9.00;
                $data['rate'] = $rate;
                $data['grade'] = $order->salonGrade;
                $amount = floatval($order->priceall)*$rate/100;
                $amount = round($amount,2);
                $data['amount'] = $amount;
                $date = date('Y-m-d H:i:s');
                $data['updated_at'] = $date;
                $data['created_at'] = $date;
                $result = \App\Commission::create($data);
                if($result)
                    $this->info('订单'.$order->ordersn.'处理成功');
                else
                    $this->error('订单'.$order->ordersn.'处理失败');
            }
        }
        
    }
}
