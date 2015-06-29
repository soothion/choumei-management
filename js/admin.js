$(function(){
	/**hash和加载进度条**/
	lib.ajatCount=0;
	var $body=$(document.body);
	lib.ajat=function (_protocol) {
		this.ajatCount++;
        return new lib.Ajat(_protocol);
    }
	lib.hashchange=function(obj){
		var temphash=location.hash;
		var query=$.extend({},lib.query,obj);
		delete query._;
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
	var loadingend=function(){
		lib.ajatCount--;
		if(lib.ajatCount==0){
			parent.$('body').trigger('loadingend');
			$body.off('_ready',loadingend);
		}
	}
	$body.on('_ready',loadingend);
	
	if($('[ajat]').length==0){
		parent.$('body').trigger('loadingend');
	}
	$body.on('click','a[href]',function(){
		if(!$(this).attr('target')){
			parent.$('body').trigger('loading');
		}
	}).on('submit','form[data-role="hash"]',function(e){
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
		if(lib.hashchange(data)){
			$(window).trigger('hashchange');
		}
		e.stopPropagation();
		e.preventDefault();
	}).on('click','a[data-role="hash"]',function(e){
		var query=lib.parseQuery($(this).attr('href').replace('#',''));
		if(lib.hashchange(query)){
			$(window).trigger('hashchange');
		}
		e.preventDefault();
	}).on('click','label[data-role="hash"]',function(){
		$(this).closest('form[data-role="hash"]').submit();
	});
	
	$(document.body).on('click','.drop-menu-toggle',function(){
		var $this=$(this);
		$this.parent().toggleClass('open');
	}).on('click','.drop-menu-item',function(){
		$(this).closest('.open').removeClass('open');
	}).on('click',function(e){
		if($(e.target).closest('.open').length==0){
			$('.open').removeClass('open');
		}
	}).on('click','.tab li',function(){
		$(this).addClass('active').siblings().removeClass('active');
	});
	
	/**分页**/
	$body.on('_ready',function(e,data){
		var $pager=$(e.target).find('.pager');
		data=data.response;
		if(data.total > 0&&data.total>data.pageSize) {
			seajs.use(location.origin+'/js/jquery.pagination.js',function (){
				var query=$.extend({},lib.query);
				var pageNo=query.pageNo;
				delete query._;
				query.pageNo='__id__';
                $pager.pagination(data.total, {
					current_page : pageNo-1,
					items_per_page : data.pageSize,
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
				$pager.append('<form data-role="hash"><input type="text" name="pageNo" /><button type="submit" class="go link">go</button></form>');
			});
		}
	});
});    	
	