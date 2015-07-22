(function(){
	lib.ajatCount=0;
	lib.ajat=function (_protocol) {
		lib.ajatCount++;
        return new lib.Ajat(_protocol);
    }
	lib.loadingend=function(e){//触发进度条加载完成
		lib.ajatCount--;
		if(lib.ajatCount==0){
			parent.$('body').trigger('loadingend');
			$(document.body).off('_ready',lib.loadingend);
		}
	}
	lib.Ajat.before=function(){
		$(document.body).on('_ready',lib.loadingend).on('exception',lib.loadingend);
	}
	document.onreadystatechange=function(){
		if(document.readyState=='interactive'){
			parent.$('body').trigger('loading');
			parent.lib.popup.close();
		}
	}
})();
$(function(){
	/**渲染面包屑**/
	var breadcrumb=$('.breadcrumb');
	if(breadcrumb.length==1){
		breadcrumb.html(lib.ejs.render({text:breadcrumb.html().replace(/%&gt;/g,'%>').replace(/&lt;%/g,'<%')},{}))
	}
	/**hash和加载进度条**/
	var $body=$(document.body);
	lib.tools.hashchange=function(obj){
		var temphash=location.hash;
		var query=$.extend({},lib.query,obj);
		delete query._;
		for(var name in query){
			if(!query[name]){
				delete query[name];
			}
		}
		location.hash='#'+decodeURIComponent($.param(query).replace(/\+/g,' '));
		return temphash==location.hash;
	}
	$(window).on('hashchange',function(){
		lib.ajatCount=0;
		parent.$('body').trigger('loading');
		lib.init();
		lib.Ajat.run();
		$('html,body').animate({scrollTop:0},200);
		$body.on('_ready',lib.loadingend);
	});
	if($('[ajat]').length==0){
		parent.$('body').trigger('loadingend');
		$(document.body).off('_ready',lib.loadingend);
	}
		
	$body.on('submit','form[data-role="hash"]',function(e){//表单submit提交
		$(this).trigger('hash');
		e.stopPropagation();
		e.preventDefault();
	}).on('hash','form[data-role="hash"]',function(e){//表单自定义hash提交
		var data=lib.tools.getFormData($(this));
		if(data.page===undefined){
			data.page=1;
			//清除排序条件
			if(lib.query.sort_key){
				data.sort_key="";
			}
			if(lib.query.sort_type){
				data.sort_type="";
			}
		}
		if(lib.tools.hashchange(data)){
			$(window).trigger('hashchange');
		}
	}).on('click','a[data-role="hash"]',function(e){//链接hash提交
		var query=lib.tools.parseQuery($(this).attr('href').replace('#',''));
		if(lib.tools.hashchange(query)){
			$(window).trigger('hashchange');
		}
		e.preventDefault();
	}).on('click','label[data-role="hash"]',function(e){//标签hash提交
		$(this).closest('form[data-role="hash"]').submit();
	}).on('submit','form[data-role="export"]',function(e){//导出功能
		console.log(cfg.getHost()+$(this).attr('action')+'?token='+localStorage.getItem('token')+"&"+location.hash.replace('#',''));
		window.open(cfg.getHost()+$(this).attr('action')+'?token='+localStorage.getItem('token')+"&"+location.hash.replace('#',''));
		e.preventDefault();
	}).on('submit','form[data-role="remove"]',function(e){//删除提交
		var $this=$(this);
		var postRemove=function(){
			var data=lib.getFormData($this);
			lib.ajax({
				url:$this.attr('action'),
				type:'POST',
				data:data,
				success:function(data){
					parent.lib.popup.result({
						bool:data.result==1,
						text:(data.result==1?"删除成功":data.msg),
						time:2000,
						define:function(){
							if(data.result==1){
								$this.closest('tr').remove();
							}
						}
					});
				}
			});
		}
		var title=$this.attr('data-title');
		if(title!=='false'){
			parent.lib.popup.confirm({text:(title||'确认删除此数据吗'),define:function(){
				postRemove();
			}})
		}else{
			postRemove();
		}
		e.preventDefault();
	});
	/**常见**/
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
	}).on('blur','input[data-role="start"]',function(){//日期区间
		var $this=$(this);
		$this.siblings('input[data-role="end"]').attr('min',$this.val());
	});
	/**自动补全**/
	$body.on('input','input[ajat-complete]',function(){//自动补全输入事件
		var $this=$(this);
		var val=$.trim($this.val());
		if(val){
			clearTimeout(lib.completeTimer);
			lib.completeTimer=setTimeout(function(){
				var ajat=$this.attr('ajat-complete').replace('${value}',val);
				$this.addClass('complete-loader');
				lib.ajat(ajat).render().done(function(){
					$this.closest('.complete').find('.complete-position').show();
					$this.removeClass('complete-loader');
				});
			},200);
		}else{
			$this.closest('.complete').find('.complete-position').hide();
		}
	}).on('keyup','input[ajat-complete]',function(e){//自动补全键盘事件
		if(e.keyCode==13||e.keyCode==38||e.keyCode==40){
			var $this=$(this);
			var complete=$this.closest('.complete');
			if(complete.find('.complete-item').length==0){
				return ;
			}
			if(e.keyCode==40||e.keyCode==38){
				var active=complete.find('.complete-item.active');
				if(active.length==0){
					active=complete.find('.complete-item')[e.keyCode==40?'first':'last']();
				}else{
					if(active[e.keyCode==40?'next':'prev']().length==1){
						active=active[e.keyCode==40?'next':'prev']();
					}
				}
				active.addClass('active').siblings().removeClass('active');
				$this.val(active.text()).trigger('autoinput',active.data());
			}
			if(e.keyCode==13){
				complete.find('.complete-position').hide();
			}
			e.preventDefault();
		}
	}).on('keydown','input[ajat-complete]',function(e){
		if(e.keyCode==13){
			e.preventDefault();
		}
	}).on('blur','input[ajat-complete]',function(){//自动补全失去焦点事件
		$(this).closest('.complete').find('.complete-position').hide();
	}).on('mousedown','.complete-item',function(){//自动补全单击事件
		var $this=$(this);
		var complete=$this.closest('.complete');
		complete.find('input[ajat-complete]').val($this.text()).trigger('autoinput',$this.data());
		complete.find('.complete-position').hide();
	});
	/**分页**/
	$body.on('_ready',function(e,data){
		var $target=$(e.target);
		var $pager=$target.find('.pager');
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
					num_edge_entries: 1,
					link_to:location.pathname+'#'+decodeURIComponent($.param(query).replace(/\+/g,' ')),
					callback:function(data){
						$pager.find('.pagination a').off('click').addClass('link');
					}
				});
				$pager.append('&nbsp;共'+data.total+'条&nbsp;<form data-role="hash"><input type="text" name="page" /><button type="submit" class="go link">go</button></form>');
			});
		}
		if(data.total==0){
			$pager.html('<div class="data-empty"><i class="fa fa-frown-o"></i>'+($target.attr('data-empty-alert')||"没有查找到相关数据")+'</div>');
		}
	});
	/**权限控制**/
	if(parent.access){
		parent.access.control(document.body);
	}
	$body.on('_ready',function(e){
		parent.access.control(e.target);
	});
	/**日期控件修正**/
	if(!lib.tools.browser().webkit){
		seajs.use([location.origin+'/laydate/laydate.js']);
		$body.on('focus','input[type=date]',function(e){
			$(this).attr('readonly',true);
		})
		$body.on('click','input[type=date]',function(e){
			var options={
				format: 'YYYY-MM-DD',
				min:this.min,
				max:this.max,
				zIndex:1000,
				choose:function(){
					e.target.focus();
				}
			};
			laydate(options);			
		});
	}
});    	
	