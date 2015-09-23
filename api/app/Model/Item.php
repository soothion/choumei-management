<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model {

	protected $table = 'salon_item';
    protected $primaryKey = 'itemid';
    public $timestamps = false;



	public static function getQueryByParam($param=[]){
        $query = Self::getQuery();


        //项目名称筛选
        if(!empty($param['itemname'])){
        	$itemname = '%'.$itemname.'%';
            $query = $query->where('itemname','like',$itemname);
        }  

	    //项目分类筛选
        if(!empty($param['typeid'])){
            $query = $query->where('typeid','=',$param['typeid']);
        }	    

        //有无规格筛选
        if(!empty($param['norms_cat_id'])){
        	$norms_cat_id = intval($param['norms_cat_id']);
        	if($norms_cat_id == 1)
            	$query = $query->where('norms_cat_id','>',0);
            if($norms_cat_id == 2)
            	$query = $query->where('norms_cat_id','=',0);
        }        

        //有无期限筛选
        if(!empty($param['exp_time'])){
        	$exp_time = intval($param['exp_time']);
        	if($exp_time == 1)
            	$query = $query->where('exp_time','>',0);
            if($exp_time == 2)
            	$query = $query->where('exp_time','=',0);
        }        

        //有无库存限制筛选
        if(!empty($param['total_rep'])){
        	$total_rep = intval($param['total_rep']);
        	if($total_rep == 1)
            	$query = $query->where('total_rep','>',0);
            if($total_rep == 2)
            	$query = $query->where('total_rep','=',0);
        }        

        //有无资格限制筛选
        if(!empty($param['buylimit'])){
        	$buylimit = intval($param['buylimit']);
        	$query = $query->join('salon_item_buylimit','salon_item_buylimit.salon_item_id','=','salon_item.itemid');
        	if($buylimit == 1)
        		$query = $query->where('salon_item_buylimit.limit_first','=',1);

            if($buylimit == 2)
            	$query = $query->where('salon_item_buylimit.limit_invite','=',1);
        }


        //排序
        $sort_key = empty($param['sort_key'])?'itemid':$param['sort_key'];
        $sort_type = empty($param['sort_type'])?'DESC':$param['sort_type'];
        $query = $query->orderBy($sort_key,$sort_type);

        return $query;
    }
}
