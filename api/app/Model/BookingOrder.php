<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
Use PDO;
use Illuminate\Pagination\AbstractPaginator;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;

class BookingOrder extends Model
{
    protected $table = 'booking_order';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    
    public function booking_order_item()
    {
        return $this->hasMany("","ORDER_SN","ORDER_SN");
    }
    
    public static function search($params)
    {
        $bases = self::getCondition($params);
        // 页数
        $page = isset($params['page']) ? max(intval($params['page']), 1) : 1;
        $size = isset($params['page_size']) ? max(intval($params['page_size']), 1) : 20;
        AbstractPaginator::currentPageResolver(function () use($page)
        {
            return $page;
        });
        $res = $bases->paginate($size)->toArray();
        
        $res['data'] = self::makeTicketOtherInfo(json_decode(json_encode($res['data'],true),true));
        //         $money_info = ['priceall_ori'=>'','actuallyPay'=>''];
        //         if($res['total']<=2000)
            //         {
            //             $money_info = self::countOfTicket($params);
            //         }
        //         $res['all_amount'] = $money_info['priceall_ori'];
        //         $res['paied_amount'] = $money_info['actuallyPay'];
        unset($res['next_page_url']);
        unset($res['prev_page_url']);
        return $res;
    }
    
    public static function detail($id)
    {
        
    }
    
    public static function getCondition($params)
    {
        $select_fields = [
            'cm_booking_order.*',
            'cm_user.user_id as user_id',
            'cm_user.nickname as nickname',
            'cm_user.mobilephone as mobilephone',
            'cm_present_article_code.recommend_code as recommend_code',
            'cm_fundflow.pay_type as pay_type',
        ];
        
        $base = self::selectRaw(implode(',',$select_fields));
        
        
        self::makeWhereOfTicket($base, $params);
        $booking_order_item_fields = [
            'ORDER_SN',
            'ITEM_NAME'
        ];
        
       $base->with(['voucher' => function ($q) use($booking_order_item_fields)
                    {
                        $q->get($booking_order_item_fields);
                    }
        ]);
        $base->orderBy('order_ticket_id','DESC');
        return $base;
    }
}