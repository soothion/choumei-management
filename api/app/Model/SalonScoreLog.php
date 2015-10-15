<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\AbstractPaginator;

class SalonScoreLog extends Model {

    protected $table = 'salon_score_log';
    protected $fillable = array('salon_id', 'score', 'msg', 'status', 'add_time', 'update_time');
    public $timestamps = false;

    public static function getLogList($options, $startTime, $endTime, $page, $size) {
        $query = self::select(['score', 'msg', 'add_time']);
        DB::enableQueryLog();
        $query->where('salon_id', $options['salonid']);
        if (!empty($startTime)) {
            $startTime = strtotime($startTime);
            $query->where('add_time', '>', $startTime);
        }
        if (!empty($endTime)) {
            $endTime = strtotime($endTime);
            $query->where('add_time', '<', $endTime);
        }
        AbstractPaginator::currentPageResolver(function () use($page) {
            return $page;
        });
        $salonList = $query->paginate($size)->toArray();
//        print_r(DB::getQueryLog());
//        exit;
        unset($salonList['next_page_url']);
        unset($salonList['prev_page_url']);
        return $salonList;
    }

}
