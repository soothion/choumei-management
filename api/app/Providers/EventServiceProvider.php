<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        //用户模块
        'user.update' => [
            'App\Listeners\UserEventListener@onUpdate',
        ],
        'user.create' => [
            'App\Listeners\UserEventListener@onCreate',
        ],      
        'user.export' => [
            'App\Listeners\UserEventListener@onExport',
        ],
        'login' => [
            'App\Listeners\UserEventListener@onLogin',
        ],
        'logout' => [
            'App\Listeners\UserEventListener@onLogout',
        ],   


        //角色模块
        'role.update' => [
            'App\Listeners\RoleEventListener@onUpdate',
        ],
        'role.create' => [
            'App\Listeners\RoleEventListener@onCreate',
        ],              
        'role.export' => [
            'App\Listeners\RoleEventListener@onExport',
        ],      


        //权限模块
        'permission.update' => [
            'App\Listeners\PermissionEventListener@onUpdate',
        ],
        'permission.create' => [
            'App\Listeners\PermissionEventListener@onCreate',
        ],          
        'permission.export' => [
            'App\Listeners\PermissionEventListener@onExport',
        ],  

        //日志模块          
        'role.export' => [
            'App\Listeners\LogEventListener@onExport',
        ],  
    		
    	//店铺模块
    	'salon.export' => [
    		'App\Listeners\SalonEventListener@onExport',
    	],
    	'salon.save' => [
    		'App\Listeners\SalonEventListener@onSave',
    	],
    	'salon.del' => [
    		'App\Listeners\SalonEventListener@onDel',
    	],
		'salon.endCooperation' => [
    		'App\Listeners\SalonEventListener@onEndCooperation',
    	],
		'salon.update' => [
    		'App\Listeners\SalonEventListener@onUpdate',
    	],
		//商户模块
    	'merchant.export' => [
    		'App\Listeners\MerchantEventListener@onExport',
    	],
    	'merchant.save' => [
    		'App\Listeners\MerchantEventListener@onSave',
    	],
    	'merchant.del' => [
    		'App\Listeners\MerchantEventListener@onDel',
    	],
		'merchant.update' => [
    		'App\Listeners\MerchantEventListener@onUpdate',
    	],
		//店铺账号模块
    	'salonAccount.save' => [
    		'App\Listeners\SalonAccountEventListener@onSave',
    	],
    	'salonAccount.delAct' => [
    		'App\Listeners\SalonAccountEventListener@onDelAct',
    	],
		'salonAccount.resetPwd' => [
    		'App\Listeners\SalonAccountEventListener@onResetPwd',
    	],

    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
