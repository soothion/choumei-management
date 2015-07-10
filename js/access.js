var slug={
	'user.index':'user/userList.html',
	'role.index':'role/index.html#page=1&page_size=20',
	'permission.index':'user/powerList.html',
	'log.index':'user/logList.html',
	'salon.index':'shop/index.html#page=1&page_size=10',
	'salonAccount.index':'shop/account.html',
	'merchant.index':'merchant/index.html',
	'shop_count.index':'shopSettlement/singleList.html',
	'shop_count.delegate_list':'shopSettlement/collectingOrderList.html',
	'shop_count.balance':'shopSettlement/fromBalance.html'
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
				this.map[data[i].slug]=data[i].show;
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
			if(self.map[$this.data('slug')]==1){
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
if($('.loadbar').length==1){
	var ajat=lib.ajat('list/menu#domid=aside&tempid=aside-t');
	ajat.setExternal({slug:slug});
	ajat.render().done(function(data){
		access.foreach(data.data);
		localStorage.setItem('access.data',JSON.stringify(access.data));
		localStorage.setItem('access.map',JSON.stringify(access.map));
		access.init();
	});
}else{
	access.init();
}