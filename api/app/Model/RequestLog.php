<?php  namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator;

/**
 * Description of RequestLog
 *
 * @author choumei
 */
class RequestLog  extends Model{
    protected $table = 'request_log';
    protected $primaryKey = 'id'; 
    protected $fillable = ['userId','bundle','version','deviceType','deviceDpi','deviceModel','deviceNetwork','deviceUuid','deviceCpu','deviceOs','timestamp','sequence','type','createTime','updateTime'];
    public $timestamps = false;
    
    public static function getLogSelect($param){
         $query = Self::getQuery();
         if(!empty($param['mobilephone'])){
	        $query = $query->where('mobilephone','=',$param['mobilephone']);
	 }
         if(isset($param['username']) && $param['username']){
	        $query = $query->where('username','=',$param['username']);
	 }
	 if(isset($param['bundle']) && $param['bundle']){
	        $query = $query->where('bundle','=',$param['bundle']);
	 }
         
         if(isset($param['minTime']) && $param['minTime'] && isset($param['maxTime']) && $param['maxTime'] ){
                $minTime=  strtotime( $param['minTime']);
                $maxTime=  strtotime($param['maxTime']);
                if( $minTime){
                    $query = $query->where('updateTime','>=',$minTime); 
                }
                if( $maxTime){
                    $query = $query->where('updateTime','<=',$maxTime);
                 }
         }
         
         $page = isset($param['page'])?max($param['page'],1):1;
	 $page_size = isset($param['page_size'])?$param['page_size']:20;
           //手动设置页数
         AbstractPaginator::currentPageResolver(function() use ($page) {
  	    return $page;
  	 });
         $fields=['mobilephone','username','bundle','updateTime','deviceOs','version'];
         $result = $query->select($fields)->join('user','user.user_id','=','request_log.userId')->paginate($page_size)->toArray();
         unset($result['next_page_url']);
         unset($result['prev_page_url']);
         return $result;
        
      }
}