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

//welcome view
Route::any('/', function () {
    return view('welcome');
});


Route::any('captcha', 'IndexController@captcha');
Route::any('login', 'IndexController@login');
Route::any('logout', 'IndexController@logout');

//列表模块
Route::any('list/city',array(
	'as'=>'list.city',
	'uses'=>'ListController@city'
));
Route::any('list/department',array(
	'as'=>'list.department',
	'uses'=>'ListController@department'
));
Route::any('list/position',array(
	'as'=>'list.position',
	'uses'=>'ListController@position'
));
Route::any('list/permission',array(
	'as'=>'list.permission',
	'uses'=>'ListController@permission'
));
Route::any('list/menu',array(
	'as'=>'list.menu',
	'uses'=>'ListController@menu'
));
Route::any('salonList/getProvinces',array(  //获取省市区商圈
				'as'=>'salonList.getProvinces',
				'uses'=>'Merchant\ListController@getProvinces'
	));	
Route::any('salonList/getBussesName',array(  //获取业务代表
				'as'=>'salonList.getBussesName',
				'uses'=>'Merchant\ListController@getBussesName'
	));	
Route::any('merchant/checkMerchantSn',array( //检测商户编号
				'as'=>'merchant.checkMerchantSn',
				'uses'=>'Merchant\MerchantController@checkMerchantSn'
	));
Route::any('salon/checkSalonSn',array(  //检测店铺编号
				'as'=>'salon.checkSalonSn',
				'uses'=>'Merchant\SalonController@checkSalonSn'
	));	
Route::any('salonAccount/getSalonName',array(  //模糊查找店铺
				'as'=>'salonAccount.getSalonName',
				'uses'=>'Merchant\SalonAccountController@getSalonName'
	));		

//权限管理后台接口
Route::group(['middleware' => ['jwt.auth','acl.auth']], function(){
// Route::group(['middleware'], function(){

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


	//日志模块
	Route::any('log/index',array(
		'as'=>'log.index',
		'uses'=>'LogController@index'
	));
	Route::any('log/export',array(
		'as'=>'log.export',
		'uses'=>'LogController@export'
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
    // 预览
    Route::any('shop_count/preview', array(
    'as' => 'shop_count.preview',
    'uses' => 'ShopCount\ShopCountController@create'
    ));
    // 新增
    Route::any('shop_count/create', array(
        'as' => 'shop_count.create',
        'uses' => 'ShopCount\ShopCountController@store'
    ));
    // 删除
    Route::any('shop_count/destroy/{id}', array(
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
    
    // 往来余额 查询
    Route::any('shop_count/balance', array(
        'as' => 'shop_count.balance',
        'uses' => 'ShopCount\ShopCountController@balance'
    ));    




    Route::any('merchant/index',array(  //商户列表
				'as'=>'merchant.index',
				'uses'=>'Merchant\MerchantController@index'
	));
	Route::any('merchant/save',array(  //添加修改商户
					'as'=>'merchant.save',
					'uses'=>'Merchant\MerchantController@save'
		));
	Route::any('merchant/update',array(  //修改商户
					'as'=>'merchant.update',
					'uses'=>'Merchant\MerchantController@update'
		));
	Route::any('merchant/del',array(  //删除商户
					'as'=>'merchant.del',
					'uses'=>'Merchant\MerchantController@del'
		));
	Route::any('merchant/getMerchantList',array( //获取单个商户详情
					'as'=>'merchant.getMerchantList',
					'uses'=>'Merchant\MerchantController@getMerchantList'
		));


	Route::any('salon/index',array(  //店铺列表
					'as'=>'salon.index',
					'uses'=>'Merchant\SalonController@index'
		));
		
	Route::any('salon/save',array( //店铺添加 接口
					'as'=>'salon.save',
					'uses'=>'Merchant\SalonController@save'
		));
	Route::any('salon/update',array( //店铺 更新接口
					'as'=>'salon.update',
					'uses'=>'Merchant\SalonController@update'
		));
	Route::any('salon/getSalon',array(  //获取店铺详情
					'as'=>'salon.getSalon',
					'uses'=>'Merchant\SalonController@getSalon'
		));	
	Route::any('salon/endCooperation',array( //终止合作
					'as'=>'salon.endCooperation',
					'uses'=>'Merchant\SalonController@endCooperation'
		));
	Route::any('salon/del',array(  //删除店铺
					'as'=>'salon.del',
					'uses'=>'Merchant\SalonController@del'
		));		
		

	Route::any('salonAccount/index',array(  //店铺账号列表
					'as'=>'salonAccount.index',
					'uses'=>'Merchant\SalonAccountController@index'
		));	

	Route::any('salonAccount/save',array(  //添加账号
					'as'=>'salonAccount.save',
					'uses'=>'Merchant\SalonAccountController@save'
		));	
	Route::any('salonAccount/resetPwd',array(  //重置密码
					'as'=>'salonAccount.resetPwd',
					'uses'=>'Merchant\SalonAccountController@resetPwd'
		));	
	Route::any('salonAccount/delAct',array(  //删除
					'as'=>'salonAccount.del',
					'uses'=>'Merchant\SalonAccountController@delAct'
		));	


});

