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
    protected $fillable = ['user_id','bundle','version','device_type','device_dpi','device_model','device_network','device_uuid','device_cpu','device_os','timestamp','sequence','type','create_time','update_time'];
    public $timestamps = false;
    
    public static function getLogSelect($param){
         $query = Self::getQuery();
         if(!empty($param['mobilephone'])){
	        $query = $query->where('mobilephone','=',$param['mobilephone']);
	 }
         if(isset($param['username']) && $param['username']){
	        $query = $query->where('username','=',$param['username']);
	 }
	 if(isset($param['device_uuid']) && $param['device_uuid']){
	        $query = $query->where('device_uuid','=',$param['device_uuid']);
	 }
         
         if(isset($param['minTime']) && $param['minTime'] ){
               
                    $query = $query->where('update_time','>=', $param['minTime']); 
         }
         if( isset($param['maxTime']) && $param['maxTime'] ){
              
                    $query = $query->where('update_time','<=', $param['maxTime']);    
         }
         
         $sortable_keys=['update_time','mobilephone','version'];
         $sortKey = "update_time";
         $sortType = "DESC";
         if (isset($param['sortKey']) && in_array($param['sortKey'], $sortable_keys)) {
             $sortKey = $param['sortKey'];
             $sortType = $param['sortType'];
             if (strtoupper($sortType) != "DESC") {
                 $sortType = "ASC";
             }
         }
         $query->orderBy($sortKey, $sortType);
         $page = isset($param['page'])?max($param['page'],1):1;
	 $page_size = isset($param['page_size'])?$param['page_size']:20;
           //手动设置页数
         AbstractPaginator::currentPageResolver(function() use ($page) {
  	    return $page;
  	 });
         $fields=['mobilephone','username','device_uuid','update_time','device_os','version'];
         $result = $query->select($fields)->join('user','user.user_id','=','request_log.user_id')->paginate($page_size)->toArray();
         unset($result['next_page_url']);
         unset($result['prev_page_url']);
         return $result;
        
      }
      
      
       public static function exportLogSelect($param){
         $query = Self::getQuery();
         if(!empty($param['mobilephone'])){
	        $query = $query->where('mobilephone','=',$param['mobilephone']);
	 }
         if(isset($param['username']) && $param['username']){
	        $query = $query->where('username','=',$param['username']);
	 }
	 if(isset($param['device_uuid']) && $param['device_uuid']){
	        $query = $query->where('device_uuid','=',$param['device_uuid']);
	 }
         
         if(isset($param['minTime']) && $param['minTime'] ){
               
                    $query = $query->where('update_time','>=', $param['minTime']); 
         }
         if( isset($param['maxTime']) && $param['maxTime'] ){
              
                    $query = $query->where('update_time','<=', $param['maxTime']);    
         }
         $sortable_keys=['update_time','mobilephone','version'];
         $sortKey = "update_time";
         $sortType = "DESC";
         if (isset($param['sortKey']) && in_array($param['sortKey'], $sortable_keys)) {
             $sortKey = $param['sortKey'];
             $sortType = $param['sortType'];
             if (strtoupper($sortType) != "DESC") {
                 $sortType = "ASC";
             }
         }
         $query->orderBy($sortKey, $sortType);   
         $fields=['mobilephone','username','device_uuid','update_time','device_os','version'];
         $result = $query->select($fields)->join('user','user.user_id','=','request_log.user_id')->get();
         return $result;
        
      }
      
      public static function getLogByOrdersn($ordersn,$fields=[])
      {
          $base = self::where('ORDER_SN',$ordersn)->where('TYPE','PLC')->select($fields)->orderBy('CREATE_TIME','DESC')->first();
          if(empty($base))
          {
              return null;
          }
          return $base->toArray();
      }
      
      public static function getLogsByOrdersns($ordersns,$fields=[])
      {
          $bases = self::whereIn('ORDER_SN',$ordersns)->where('TYPE','PLC')->select($fields)->groupBy('ORDER_SN')->orderBy('CREATE_TIME','DESC')->get();
          if(empty($bases))
          {
              return [];
          }
          return $bases->toArray();
      }
}
