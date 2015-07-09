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
	get:function(slug){
		return this.map[slug];
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
				$this.show()
			}
		});
	}
}
var ajat=lib.ajat('list/menu#domid=aside&tempid=aside-t');
ajat.setExternal({slug:slug});
ajat.render().done(function(data){
	access.data=data.data;
	access.foreach(access.data);
});