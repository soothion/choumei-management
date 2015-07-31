var slug={
	'user.index':'/module/user/userList.html',
	'role.index':'/module/role/index.html',
	'permission.index':'/module/user/powerList.html',
	'log.index':'/module/user/logList.html',
	'salon.index':'/module/shop/index.html',
	'salonAccount.index':'/module/shop/account.html',
	'merchant.index':'/module/merchant/index.html',
	'shop_count.index':'/module/shopSettlement/singleList.html',
	'shop_count.delegate_list':'/module/shopSettlement/collectingOrderList.html',
	'shop_count.balance':'/module/shopSettlement/fromBalance.html'
}
var access={
	data:[],
	map:{},
	init:function(){
		this.data=JSON.parse(localStorage.getItem('access.data'));
		this.map=JSON.parse(localStorage.getItem('access.map'));
	},
	get:function(slug){
		return this.map[slug];
	},
	getData:function(){
		return JSON.parse(localStorage.getItem('access-data'))
	},
	foreach:function(data){
		for(var i=0;i<data.length;i++){
			if(data[i].slug){
				this.map[data[i].slug]=true;
			}
			if(data[i].child instanceof Array){
				access.foreach(data[i].child);
			}
		}
	},
	control:function(dom){
		var self=this;
		$(dom).find('[data-slug]').each(function(){
			var $this=$(this);
			var arr=$this.data('slug').split(',');
			if(self.map[arr[0]]||(arr[1]&&self.map[arr[1]])){
				$this.removeAttr('data-slug');
			}else{
				var form=$this.closest('form[data-role="form"]');
				if($this.is('button')&&form.length==1){
					form.attr('disabled','disabled');
				}
			}
		});
	}
}
$(function(){
	if($('.loadbar').length==1){
		if(!localStorage.getItem('access.data')){
			var ajat=lib.ajat('list/menu#domid=page&tempid=page-t');
			ajat.setExternal({slug:slug});
			ajat.render().done(function(data){
				access.foreach(data.data);
				localStorage.setItem('access.data',JSON.stringify(data.data));
				localStorage.setItem('access.map',JSON.stringify(access.map));
				access.init();
			});
		}else{
			var ajat=lib.ajat('#domid=page&tempid=page-t');
			ajat.template(JSON.parse(localStorage.getItem('access.data')));
		}
	}else{
		access.init();
	}
})