<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\BookingCalendar;
use App\BookingOrder;
use App\BookingOrderItem;

class BookingCalendarRepair extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BookingCalendarRepair';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Repair Booking Calendar.';

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
        $dates = self::getAllDate();
   
        $now = date("Y-m-d H:i:s");
        foreach ($dates as $date) {
            $this->info("begin to repair : [$date]");
            $items = BookingOrder::whereIn('STATUS', [
                'RFN',
                'CSD',
                'PYD',
                'NEW'
            ])->whereRaw("((`UPDATED_BOOKING_DATE` IS NULL AND BOOKING_DATE = '{$date}') OR `UPDATED_BOOKING_DATE` = '{$date}')")->get([
                'BOOKING_DATE',
                'UPDATED_BOOKING_DATE',
                'ORDER_SN',
            ])->toArray();
            $ordersns = array_column($items, 'ORDER_SN');
    
            $update_info = BookingOrderItem::whereIn('ORDER_SN', $ordersns)->selectRaw('count(*) as `num`,`ITEM_ID`')
                ->groupBy('ITEM_ID')
                ->get()
                ->toArray();
            foreach ($update_info as $info) {
                $item_id = $info['ITEM_ID'];
                $num = $info['num'];
                $origin_query  = BookingCalendar::where('ITEM_ID', $item_id)->where('BOOKING_DATE',$date);
                $origin_query->useWritePdo();
                $is_exist = $origin_query->first();
                if(!empty($is_exist))
                {
                    $res = BookingCalendar::where('ITEM_ID', $item_id)->where('BOOKING_DATE',$date)->update([
                        'QUANTITY' => $num
                    ]);
                }
                else 
                {
                    BookingCalendar::create([
                        'BEAUTY_ID' => 1,
                        'ITEM_ID' => $item_id,
                        'BOOKING_DATE' => $date,
                        'QUANTITY' => $num,
                        'CREATE_TIME' => $now,
                        'UPDATE_TIME' => $now
                     ]);
                    
                }
            }
        }
        $this->info("all success!");
    }
    
    public static function getAllDate()
    {
        $items = BookingOrder::whereIn('STATUS',['RFN','CSD','PYD','NEW'])->get(['BOOKING_DATE','UPDATED_BOOKING_DATE'])->toArray();
        $old = array_column($items, 'BOOKING_DATE');
        $new = array_column($items, 'UPDATED_BOOKING_DATE');
        $all_dates = array_filter(array_unique(array_merge($old,$new)));
        asort($all_dates);
        return array_values($all_dates);
    }
}
