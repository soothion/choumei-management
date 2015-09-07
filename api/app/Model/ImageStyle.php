<?php  namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator;
use DB;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ImageStyle
 *
 * @author choumei
 */
class ImageStyle  extends Model{
   
    protected $table = 'style_img';
    protected $fillable = ['id', 'style', 'length','curl','color','img'];

    public static function getAllImage($param){
        
    	$query = Self::getQuery();
        
        if($param['style'] > 0 &&isset($param['style']) && $param['style']){
	        $query = $query->where('style','=',$param['style']);
	}
          if($param['length'] > 0 && isset($param['length']) && $param['length']){
	        $query = $query->where('length','=',$param['length']);
	}
	 if($param['curl'] > 0 && isset($param['curl']) && $param['curl']){
	        $query = $query->where('curl','=',$param['curl']);
	}
         if($param['color'] > 0 && isset($param['color']) && $param['color']){
	        $query = $query->where('color','=',$param['color']);
	}
         if(isset($param['img']) && $param['img']){
	        $query = $query->where('img','=',$param['img']);
	}
        $query = $query->where('status','=',1);      
        $page = isset($param['page'])?max($param['page'],1):1;
	$page_size = isset($param['page_size'])?$param['page_size']:20;
           //手动设置页数
	    AbstractPaginator::currentPageResolver(function() use ($page) {
		    return $page;
		});
         $fields = ['id', 'style', 'length','curl','color','img'];  
         $result = $query->select($fields)->paginate($page_size)->toArray();
         unset($result['next_page_url']);
	 unset($result['prev_page_url']);
	 return $result;
    }
    public static function getOneImage($id){
    	$query = Self::getQuery();
        $query = $query->where('id','=',$id);
        $fields = ['id', 'style', 'length','curl','color','img'];  
        return $query->select($fields)->first();
    }
    public static function updateImage($id,$param){
       
        $result= DB::table('style_img')->where('id',$id)->update($param);
        return $result;
    }
    public static function insertImage($param){

    $param['status']=1;
    $result=DB::table('style_img')->insert($param);
    return  $result;
    }
    public static function deleteImage($id){
        $result=DB::table('style_img')->where('id',$id)->update(['status'=>0]);
        return $result;
    }
}
