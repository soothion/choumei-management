	lib.ajatCount=0;
	lib.ajat=function (_protocol) {
		lib.ajatCount++;
        return new lib.Ajat(_protocol);
    }
	lib.Ajat.prototype.parseResponse=function(data){
		if(data.result==0&&data.code==400){
			parent.location.href="/module/user/login.html";
		}
		 return data.data;
	}
	var loadingend=function(){//触发进度条加载完成
		lib.ajatCount--;
		if(lib.ajatCount==0){
			parent.$('body').trigger('loadingend');
			$(document.body).off('_ready',loadingend);
		}
	}
	lib.Ajat.before=function(){
		$(document.body).on('_ready',loadingend);
	}
$(function(){
	/**hash和加载进度条**/
	var $body=$(document.body);
	lib.hashchange=function(obj){
		var temphash=location.hash;
		var query=$.extend({},lib.query,obj);
		delete query._;
		for(var name in query){
			if(!query[name]){
				delete query[name];
			}
		}
		location.hash='#'+$.param(query);
		return temphash==location.hash;
	}
	$(window).on('hashchange',function(){
		lib.ajatCount=0;
		parent.$('body').trigger('loading');
		lib.init();
		lib.Ajat.run();
		$(document).scrollTop(0);
		$body.on('_ready',loadingend);
	});
	
	if($('[ajat]').length==0){
		parent.$('body').trigger('loadingend');
	}
		
	$body.on('click','a[href]',function(){//触发加载进度条
		if(!$(this).attr('target')){
			parent.$('body').trigger('loading');
		}
	}).on('submit','form[data-role="hash"]',function(e){//表单hash提交
		var data={};
		var fields=$(this).serializeArray();
		$.each(fields,function(i,field){
			if(!data[field.name]){
				data[field.name]=field.value;
			}else{
				if(data[field.name] instanceof Array){
					data[field.name].push(field.value);
				}else{
					data[field.name]=[data[field.name],field.value];
				}
			}
		});
		if(data.page!=1){
			data.page=1;
		}
		if(lib.hashchange(data)){
			$(window).trigger('hashchange');
		}
		e.stopPropagation();
		e.preventDefault();
	}).on('click','a[data-role="hash"]',function(e){//链接hash提交
		var query=lib.parseQuery($(this).attr('href').replace('#',''));
		if(lib.hashchange(query)){
			$(window).trigger('hashchange');
		}
		e.preventDefault();
	}).on('click','label[data-role="hash"]',function(e){//标签hash提交
		$(this).closest('form[data-role="hash"]').submit();
	}).on('submit','form[data-role="export"]',function(e){//导出功能
		window.open(cfg.getHost()+$(this).attr('action')+'?token='+localStorage.getItem('token')+"&"+location.hash.replace('#'));
		e.preventDefault();
	});
	
	$body.on('click','.drop-menu-toggle',function(){//下拉菜单
		var $this=$(this);
		$this.parent().toggleClass('open');
	}).on('click','.drop-menu-item',function(){
		$(this).closest('.open').removeClass('open');
	}).on('click',function(e){
		if($(e.target).closest('.open').length==0){
			$('.open').removeClass('open');
		}
	}).on('click','.tab li',function(){//选项卡切换
		$(this).addClass('active').siblings().removeClass('active');
	}).on('input','input[data-role="start"]',function(){//日期区间
		var $this=$(this);
		$this.siblings('input[data-role="end"]').attr('min',$this.val());
	});
	
	$body.on('_ready',function(e,data){
		data=data.response;
		if(data.result==0&&data.code=='400'){
			parent.location.href="/module/user/login.html";
		}
	})
	/**分页**/
	$body.on('_ready',function(e,data){
		var $pager=$(e.target).find('.pager');
		data=data.response;
		if(data.total > 0) {
			seajs.use('/js/jquery.pagination.js',function (){
				var query=$.extend({},lib.query);
				delete query._;
				query.page='__id__';
                $pager.pagination(data.total, {
					current_page : data.current_page-1,
					items_per_page : data.per_page,
					next_text:'>>',
					prev_text:'<<',
					num_display_entries: 7,
					num_edge_entries: 0,
					link_to:location.pathname+'#'+$.param(query),
					callback:function(data){
						$pager.find('.pagination a').off('click').addClass('link');
					}
				});
				$pager.prepend('共'+data.total+'条&nbsp;');
				$pager.append('<form data-role="hash"><input type="text" name="page" /><button type="submit" class="go link">go</button></form>');
			});
		}
	});
});    	
	