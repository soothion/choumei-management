<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



//权限管理后台接口
// Route::group(['middleware' => ['jwt.auth','acl.auth']], function(){
Route::group(['middleware'], function(){

	//用户模块
	Route::any('user/index',array(
			'as'=>'user.index',
			'uses'=>'UserController@index'
		));
	Route::any('user/show/{id}',array(
			'as'=>'user.show',
			'uses'=>'UserController@show'
		));
	Route::any('user/update/{id}',array(
			'as'=>'user.update',
			'uses'=>'UserController@update'
		));
	Route::any('user/create',array(
			'as'=>'user.create',
			'uses'=>'UserController@create'
		));	
	Route::any('user/export',array(
			'as'=>'user.export',
			'uses'=>'UserController@export'
		));

	//角色模块
	Route::any('role/index',array(
			'as'=>'role.index',
			'uses'=>'RoleController@index'
		));
	Route::any('role/show/{id}',array(
			'as'=>'role.show',
			'uses'=>'RoleController@show'
		));
	Route::any('role/update/{id}',array(
			'as'=>'role.update',
			'uses'=>'RoleController@update'
		));	
	Route::any('role/create',array(
			'as'=>'role.create',
			'uses'=>'RoleController@create'
		));	
	Route::any('role/export',array(
			'as'=>'role.export',
			'uses'=>'RoleController@export'
		));


	//权限模块
	Route::any('permission/index',array(
			'as'=>'permission.index',
			'uses'=>'PermissionController@index'
		));
	Route::any('permission/show/{id}',array(
			'as'=>'permission.show',
			'uses'=>'PermissionController@show'
		));
	Route::any('permission/update/{id}',array(
			'as'=>'permission.update',
			'uses'=>'PermissionController@update'
		));	
	Route::any('permission/create',array(
			'as'=>'permission.create',
			'uses'=>'PermissionController@create'
		));	
	Route::any('permission/export',array(
			'as'=>'permission.export',
			'uses'=>'PermissionController@export'
		));


	 // 店铺结算模块
    
    // 列表 搜索
    Route::any('shop_count/index', array(
        'as' => 'shop_count.index',
        'uses' => 'ShopCount\ShopCountController@index'
    ));
    // 详情
    Route::any('shop_count/show/{id}', array(
        'as' => 'shop_count.show',
        'uses' => 'ShopCount\ShopCountController@show'
    ));
    // 修改
    Route::any('shop_count/update/{id}', array(
        'as' => 'shop_count.update',
        'uses' => 'ShopCount\ShopCountController@update'
    ));
    // 新增
    Route::any('shop_count/create', array(
        'as' => 'shop_count.create',
        'uses' => 'ShopCount\ShopCountController@create'
    ));
    // 删除
    Route::any('shop_count/destroy', array(
        'as' => 'shop_count.destroy',
        'uses' => 'ShopCount\ShopCountController@destroy'
    ));
    
    // 代收单列表 搜索
    Route::any('shop_count/delegate_list', array(
        'as' => 'shop_count.delegate_list',
        'uses' => 'ShopCount\ShopCountController@delegate_list'
    ));
    
    // 代收单详情
    Route::any('shop_count/delegate_detail/{id}', array(
        'as' => 'shop_count.delegate_detail',
        'uses' => 'ShopCount\ShopCountController@delegate_detail'
    ));
    
    // 往来余额
    Route::any('shop_count/balance', array(
        'as' => 'shop_count.balance',
        'uses' => 'ShopCount\ShopCountController@balance'
    ));

	//日志模块
	Route::any('log/index','LogController@index');
	Route::any('log/export','LogController@export');


	Route::any('list/city','ListController@city');
	Route::any('list/department','ListController@department');
	Route::any('list/position','ListController@position');
	Route::any('list/permission','ListController@permission');
	Route::any('list/menu','ListController@menu');



});
