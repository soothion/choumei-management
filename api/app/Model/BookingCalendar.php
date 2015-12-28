<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\ApiException;
use App\Exceptions\ERROR;

class BookingCalendar extends Model {

	protected $table = 'booking_calendar';
	
	public $timestamps = false;
	
	protected $primaryKey = 'ID';
	
	public static  function modifyDay($orderSn,$modifyDate,$modifyDesc="DEF"){

	
	    if( strtotime($modifyDate) < strtotime( date('Y-m-d') )  ) 
	    {
	        throw  new ApiException("你提交的修改预约时间有误哦！！！", ERROR::PARAMETER_ERROR);
	    }
	    if( !in_array($modifyDesc,['DEF','MORNING','AFTERNOON'] )) 
	    {
	        throw  new ApiException("未知的时间类型哦！！！", ERROR::PARAMETER_ERROR);
	    }
	    	
	    $oldBookingDate = BookingOrder::select(['BOOKING_DATE','UPDATED_BOOKING_DATE','BOOKING_DESC'])->where(['ORDER_SN'=>$orderSn])->first();
	    if(empty($oldBookingDate))	
	    {
	        throw  new ApiException("数据有误，请联系管理员！", ERROR::PARAMETER_ERROR);
	    }
	        
	    // 需要取出预约的项目id哦:(
	    if(!empty($modifyDate)) 
	    {
	        $itemIds = BookingOrderItem::select(['ITEM_ID','QUANTITY'])->where(['ORDER_SN'=>$orderSn])->get()->toArray();
	    }
	
	    $oldBookingDate = $oldBookingDate->toArray();
	    if( $oldBookingDate['UPDATED_BOOKING_DATE'] ) 
	    {
	        $oldBookingTime = $oldBookingDate['UPDATED_BOOKING_DATE'];
	    }
	    else
	    {
	        $oldBookingTime = $oldBookingDate['BOOKING_DATE'];
	    }
	
	    $oldBookingDesc = $oldBookingDate['BOOKING_DESC'];
	
	
	
	    // 更新开始
	    if( $oldBookingTime == $modifyDate && $oldBookingDesc==$modifyDesc )
	    {
	        return ;
	    }
	
	    DB::beginTransaction();
	    // 1. 修改订单的预约时间
	    $result1 = BookingOrder::where(['ORDER_SN'=>$orderSn])->update(['UPDATED_BOOKING_DATE'=>$modifyDate,'BOOKING_DESC'=>$modifyDesc]);
	
	    // 		2. 修改日历数据 按照项目id和时间日期逐一修改统计
	    $resultN = true;
	    $tempCount = 'tempCount';
	    $tempResultStr = 'result';
	    foreach( $itemIds as $k => $v ){
	        $t = BookingCalendar::where(['ITEM_ID'=>$v['ITEM_ID'],'BOOKING_DATE'=>$oldBookingTime])->first();
	        if( empty($t) ) { 
	            DB::rollBack();
	           return $this->error('数据错误，请联系管理人员');
	        }
	        $tOld = $t->toArray();
	        	
	        $tNew = BookingCalendar::where(['ITEM_ID'=>$v['ITEM_ID'],'BOOKING_DATE'=>$modifyDate])->first();
	        	
	        // 如果新修改的时间日历不存在的情况
	        if( empty($tNew) ){
	            $updateCallendar = [];
	            $updateCallendar['UPDATE_TIME'] = date('Y-m-d H:i:s');
	            if( $v['QUANTITY'] == $tOld['QUANTITY'] ){
	                if( $oldBookingDesc != $modifyDesc && $oldBookingTime != $modifyDate ){
	                    $oldDescField = ['MORNING'=>'BOOKING_MORN_COUNT','AFTERNOON'=>'BOOKING_AFTERNOON_COUNT'];
	                    $updateCallendar['BOOKING_DATE'] = $modifyDate;
	                    switch( $modifyDesc ){
	                        case 'DEF':
	                            $updateCallendar[ $oldDescField[ $oldBookingDesc ] ] = $tOld[ $oldDescField[ $oldBookingDesc ] ]  -1;
	                            break;
	                        case 'MORNING':
	                            $tempee = isset( $oldDescField[ $oldBookingDesc ] )?1:0;
	                            $updateCallendar['BOOKING_AFTERNOON_COUNT'] = $tOld['BOOKING_AFTERNOON_COUNT']-$tempee;
	                            $updateCallendar['BOOKING_MORN_COUNT'] = $tOld['BOOKING_MORN_COUNT']+1;
	                            break;
	                        case 'AFTERNOON':
	                            $tempee = isset( $oldDescField[ $oldBookingDesc ] )?1:0;
	                            $updateCallendar['BOOKING_MORN_COUNT'] = $tOld['BOOKING_MORN_COUNT']-$tempee;
	                            $updateCallendar['BOOKING_AFTERNOON_COUNT'] = $tOld['BOOKING_AFTERNOON_COUNT']+1;
	                            break;
	                        default:
	                            break;
	                    }
	                }
	                // 					日期变化 具体时间不变
	                if($oldBookingDesc == $modifyDesc && $oldBookingTime != $modifyDate){
	                    $updateCallendar['BOOKING_DATE'] = $modifyDate;
	
	                }
	                // 日期不变 具体时间变化
	                if( $oldBookingTime == $modifyDate && $oldBookingDesc != $modifyDesc ){
	                    $oldDescField = ['MORNING'=>'BOOKING_MORN_COUNT','AFTERNOON'=>'BOOKING_AFTERNOON_COUNT'];
	                    switch( $modifyDesc ){
	                        case 'DEF':
	                            $updateCallendar[ $oldDescField[ $oldBookingDesc ] ] = $tOld[ $oldDescField[ $oldBookingDesc ]] -1;
	                            break;
	                        case 'MORNING':
	                            $tempee = isset( $oldDescField[ $oldBookingDesc ] )?1:0;
	                            $updateCallendar['BOOKING_AFTERNOON_COUNT'] = $tOld['BOOKING_AFTERNOON_COUNT']-$tempee;
	                            $updateCallendar['BOOKING_MORN_COUNT'] = $tOld['BOOKING_MORN_COUNT']+1;
	                            break;
	                        case 'AFTERNOON':
	                            $tempee = isset( $oldDescField[ $oldBookingDesc ] )?1:0;
	                            $updateCallendar['BOOKING_MORN_COUNT'] = $tOld['BOOKING_MORN_COUNT']-$tempee;
	                            $updateCallendar['BOOKING_AFTERNOON_COUNT'] = $tOld['BOOKING_AFTERNOON_COUNT']+1;
	                            break;
	                        default:
	                            break;
	                    }
	                }
	                $tempResult[ $tempResultStr . ($k+2) ] = BookingCalendar::where(['ITEM_ID'=>$v['ITEM_ID'],'BOOKING_DATE'=>$oldBookingTime])->update($updateCallendar);
	            }else{
	                $tNewData = $tOld;
	                	
	                $tNewData['BOOKING_DATE'] = $modifyDate;
	                $tNewData['QUANTITY'] = $v['QUANTITY'];
	                $tNewData['UPDATE_TIME'] = date('Y-m-d H:i:s');
	                $tNewData['BOOKING_MORN_COUNT'] = 0;
	                $tNewData['BOOKING_AFTERNOON_COUNT'] = 0;
	                $tNewData['CAME'] = 0;
	                unset( $tNewData['ID'] );
	                $updateCallendar = [];
	                $updateCallendar['UPDATE_TIME'] = date('Y-m-d H:i:s');
	                $updateCallendar['QUANTITY'] =  $tOld['QUANTITY'] - $v['QUANTITY'];
	                	
	                if( $oldBookingDesc != $modifyDesc && $oldBookingTime != $modifyDate ){
	                    $oldDescField = ['MORNING'=>'BOOKING_MORN_COUNT','AFTERNOON'=>'BOOKING_AFTERNOON_COUNT'];
	                    switch( $modifyDesc ){
	                        case 'DEF':
	                            $updateCallendar[ $oldDescField[ $oldBookingDesc ] ] = $tOld[$oldDescField[ $oldBookingDesc ]] -1;
	                            break;
	                        case 'MORNING':
	                            $tempee = isset( $oldDescField[ $oldBookingDesc ] )?1:0;
	                            $updateCallendar['BOOKING_AFTERNOON_COUNT'] = $tOld['BOOKING_AFTERNOON_COUNT']-$tempee;
	                            $tNewData['BOOKING_MORN_COUNT'] = 1;
	                            break;
	                        case 'AFTERNOON':
	                            $tempee = isset( $oldDescField[ $oldBookingDesc ] )?1:0;
	                            $updateCallendar['BOOKING_MORN_COUNT'] = $tOld['BOOKING_MORN_COUNT']-$tempee;
	                            $tNewData['BOOKING_AFTERNOON_COUNT'] = 1;
	                            break;
	                        default:
	                            break;
	                    }
	                }
	                // 	日期变化 具体时间不变
	                if($oldBookingDesc == $modifyDesc && $oldBookingTime != $modifyDate && $modifyDesc != 'DEF' ){
	                    $oldDescField = ['MORNING'=>'BOOKING_MORN_COUNT','AFTERNOON'=>'BOOKING_AFTERNOON_COUNT'];
	                    $tNewData[ $oldDescField[ $oldBookingDesc ] ] = 1;
	                    $updateCallendar[ $oldDescField[ $oldBookingDesc ] ] = $tOld[ $oldDescField[$oldBookingDesc] ]-1;
	                }
	                // 日期不变 具体时间变化
	                if( $oldBookingTime == $modifyDate && $oldBookingDesc != $modifyDesc ){
	                    $oldDescField = ['MORNING'=>'BOOKING_MORN_COUNT','AFTERNOON'=>'BOOKING_AFTERNOON_COUNT'];
	                    switch( $modifyDesc ){
	                        case 'DEF':
	                            $updateCallendar[ $oldDescField[ $oldBookingDesc ] ] = $tOld[ $oldDescField[ $oldBookingDesc ]]-1;
	                            break;
	                        case 'MORNING':
	                            $tempee = isset( $oldDescField[ $oldBookingDesc ] )?1:0;
	                            $updateCallendar['BOOKING_AFTERNOON_COUNT'] = $tOld['BOOKING_AFTERNOON_COUNT']-$tempee;
	                            $tNewData['BOOKING_MORN_COUNT'] = 1 ;
	                            break;
	                        case 'AFTERNOON':
	                            $tempee = isset( $oldDescField[ $oldBookingDesc ] )?1:0;
	                            $updateCallendar['BOOKING_MORN_COUNT'] = $tOld['BOOKING_MORN_COUNT']-$tempee;
	                            $tNewData['BOOKING_AFTERNOON_COUNT'] = 1;
	                            break;
	                        default:
	                            break;
	                    }
	                }
	                $tempResult[ $tempResultStr . ($k+2) ] = BookingCalendar::where(['ITEM_ID'=>$v['ITEM_ID'],'BOOKING_DATE'=>$oldBookingTime])->update($updateCallendar);
	                $tempResult[ $tempResultStr . ($k+20) ] = BookingCalendar::insertGetId( $tNewData );
	            }
	        }else{
	            $tNewData = $updateCallendar = [];
	            $updateCallendar = [];
	            $tNewData['UPDATE_TIME'] = $updateCallendar['UPDATE_TIME'] = date('Y-m-d H:i:s');
	            $oldDescField = ['MORNING'=>'BOOKING_MORN_COUNT','AFTERNOON'=>'BOOKING_AFTERNOON_COUNT'];
	            // 此旧日历统计只含有修改的项目情况时
	            if( $oldBookingDesc != $modifyDesc && $oldBookingTime != $modifyDate ){
	                $updateCallendar['QUANTITY'] =  $tOld['QUANTITY'] - $v['QUANTITY'];
	                $tNewData['QUANTITY'] =  $tNew['QUANTITY'] + $v['QUANTITY'];
	                switch( $modifyDesc ){
	                    case 'DEF':
	                        $updateCallendar[ $oldDescField[ $oldBookingDesc ] ] = $tOld[$oldDescField[ $oldBookingDesc ]] -1;
	                        $tNewData[ $oldDescField[ $oldBookingDesc ] ] = $tNew[$oldDescField[ $oldBookingDesc ]] +1;
	                        break;
	                    case 'MORNING':
	                        $tempee = isset( $oldDescField[ $oldBookingDesc ] )?1:0;
	                        $updateCallendar['BOOKING_AFTERNOON_COUNT'] =  $tOld['BOOKING_AFTERNOON_COUNT']-$tempee;
	                        $tNewData['BOOKING_MORN_COUNT'] = $tNew['BOOKING_MORN_COUNT']+1;
	                        break;
	                    case 'AFTERNOON':
	                        $tempee = isset( $oldDescField[ $oldBookingDesc ] )?1:0;
	                        $updateCallendar['BOOKING_MORN_COUNT'] = $tOld['BOOKING_MORN_COUNT']-$tempee;
	                        $tNewData['BOOKING_AFTERNOON_COUNT'] = $tNew['BOOKING_AFTERNOON_COUNT']+1;
	                        break;
	                    default:
	                        break;
	                }
	                	
	            }
	            // 					日期变化 具体时间不变
	            if($oldBookingDesc == $modifyDesc && $oldBookingTime != $modifyDate && $modifyDesc != 'DEF'){
	                $updateCallendar['QUANTITY'] =  $tOld['QUANTITY'] - $v['QUANTITY'];
	                $updateCallendar[ $oldDescField[ $oldBookingDesc ] ] = $tOld[ $oldDescField[ $oldBookingDesc ] ]-1;
	                $tNewData['QUANTITY'] =  $tNew['QUANTITY'] + $v['QUANTITY'];
	                $tNewData[ $oldDescField[ $oldBookingDesc ] ] = $tNew[ $oldDescField[ $oldBookingDesc ] ]+1;
	            }
	            // 日期不变 具体时间变化
	            if( $oldBookingTime == $modifyDate && $oldBookingDesc != $modifyDesc ){
	                switch( $modifyDesc ){
	                    case 'DEF':
	                        $updateCallendar[ $oldDescField[ $oldBookingDesc ] ] = $tOld[ $oldDescField[ $oldBookingDesc ]] -1;
	                        $tNewData[ $oldDescField[ $oldBookingDesc ] ] = $tNew[ $oldDescField[ $oldBookingDesc ]] +1;
	                        break;
	                    case 'MORNING':
	                        $tempee = isset( $oldDescField[ $oldBookingDesc ] )?1:0;
	                        $updateCallendar['BOOKING_AFTERNOON_COUNT'] =$tOld['BOOKING_AFTERNOON_COUNT']-$tempee;
	                        $tNewData['BOOKING_MORN_COUNT'] = $tNew['BOOKING_MORN_COUNT']+1;
	                        break;
	                    case 'AFTERNOON':
	                        $tempee = isset( $oldDescField[ $oldBookingDesc ] )?1:0;
	                        $updateCallendar['BOOKING_MORN_COUNT'] = $tOld['BOOKING_MORN_COUNT'] - $tempee;
	                        $tNewData['BOOKING_AFTERNOON_COUNT'] = $tNew['BOOKING_AFTERNOON_COUNT']+1;
	                        break;
	                    default:
	                        break;
	                }
	            }
	            $tOldNumber = $tOld['QUANTITY'] - $v['QUANTITY'];
	            if( $tOldNumber == 0 && $oldBookingTime != $modifyDate )
	                $tempResult[ $tempResultStr . ($k+2) ] = BookingCalendar::where(['ITEM_ID'=>$v['ITEM_ID'],'BOOKING_DATE'=>$oldBookingTime])->delete();
	            else
	                $tempResult[ $tempResultStr . ($k+2) ] = BookingCalendar::where(['ITEM_ID'=>$v['ITEM_ID'],'BOOKING_DATE'=>$oldBookingTime])->update($updateCallendar);
	            	
	            $tempResult[ $tempResultStr . ($k+20) ] = BookingCalendar::where( ['ITEM_ID'=>$v['ITEM_ID'],'BOOKING_DATE'=>$modifyDate])->update($tNewData);
	        }
	    }
	    foreach( $tempResult as $k => $v ){
	        if(empty($v)){ 
	            $resultN = false; 
	            break;
	        }
	    }
// 	    // 		var_dump($result1,$resultN);exit;
// 	    if( $result1 && $resultN ){
// 	        Event::fire('calendar.modifyDay','修改预约时间');
// 	        DB::commit();
// 	        return $this->success();
// 	    }else{
// 	        DB::rollBack();
// 	        return $this->error('修改失败，请稍后再试');
// 	    }
	}
}

