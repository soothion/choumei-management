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
       $this->commissionLog();  
       $this->commission();
    }

    /**
     * 佣金单流水
     */
    public function commissionLog(){
        $query = Order::leftJoin('commission_log','order.ordersn','=','commission_log.ordersn')
            ->join('salon','salon.salonid','=','order.salonid')
            ->where('commission_log.id','=',NULL)
            ->select('order.ordersn','order.priceall','order.pay_time','order.use_time','order.salonid','salon.salonGrade');
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
                if($result)
                    $this->info('订单'.$order->ordersn.'处理成功');
                else
                    $this->error('订单'.$order->ordersn.'处理失败');
            }
        }
    }


    /**
     *生成佣金单
     */
    public function commission(){
        $salons =  \App\CommissionLog::select(DB::raw('distinct salonid as salonid'))->lists('salonid');
        $this->info('总共'.count($salons).'家店铺待结算');
        foreach ($salons as $key => $salon) {
           $num = $key+1;
           $this->info('正在结算第'.$num.'家店铺');
           $commissionLogs = \App\CommissionLog::where('salonid','=',$salon)
                ->select(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d') as date,sum(`amount`) as amount"))
                ->groupBy('date')
                ->get();
            foreach ($commissionLogs as $commissionLog) {
                $data['sn'] = \App\Commission::getSn();
                $data['salonid'] = $salon;
                $data['amount'] = $commissionLog->amount;
                $data['date'] = $commissionLog->date;
                $date = date('Y-m-d H:i:s');
                $data['updated_at'] = $date;
                $data['created_at'] = $date;
                $result = \App\Commission::create($data);
                if($result)
                    $this->info('店铺'.$salon.'处理成功');
                else
                    $this->error('店铺'.$salon.'处理失败');
            }
        }
    }
}
