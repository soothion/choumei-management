if(!localStorage.getItem('token')){
	location.href='user/login.html';
}
$('#page').on('_ready',function(){
	function resize(){
		$('.frame-main').height($(window).height()-$('header').height());
	}
	window.onresize=resize;
	resize();
	var loadbar=$('.loadbar');
	var iframe=$('iframe');
	var timeStamp=0;
	$(document.body).on('loading',function(e){
		timeStamp=e.timeStamp;
		loadbar.stop().width(0).animate({
			width:$(window).width()-100
		},600);
		iframe.css('opacity',0.35);
	}).on('loadingend',function(e){
		if(e.timeStamp-timeStamp<600){
			setTimeout(function(){
				loadbar.animate({
					width:'100%'
				},150,'swing',function(){
					loadbar.css({width:0});
				});
				iframe.css('opacity',1);
			},600-(e.timeStamp-timeStamp))
		}else{
			loadbar.animate({
				width:'100%'
			},150,'swing',function(){
				loadbar.css({width:0});
			});
			iframe.css('opacity',1);
		}
	});
	$('aside').on('click','li',function(){
		$('aside li.active').removeClass('active');
		$(this).addClass('active');
	}).on('click','a',function(){
		if(iframe[0].contentWindow.location.href==this.href){
			iframe[0].contentWindow.location.reload();
		}
	}).on('click','.menu-category-title',function(e){
		var $this=$(this);
		$this.parent().addClass('active').siblings().removeClass('active');
	});
	$('.refresh').on('click',function(){
		iframe[0].contentWindow.location.reload();
	});
	$('.nav-main li').on('click',function(){
		$(this).addClass('active').siblings().removeClass('active');
		$('#'+$(this).data('id')).show().siblings().hide();
	});
	$('#logout').on('click',function(){
		lib.ajax({
			url:'logout',
			success:function(data){
				lib.popup.result({
					bool:data.result==1,
					text:(data.result==1?'退出成功':data.msg),
					time:2000,
					define:function(){
						if(data.result==1){
							localStorage.setItem('token','');
							location.href='user/login.html';
						}
					}
				});
			}
		});
	});
	$(document).on('click','.drop-menu-toggle',function(){//下拉菜单
		var $this=$(this);
		$this.parent().toggleClass('open');
	}).on('click','.drop-menu-item',function(){
		$(this).closest('.open').removeClass('open');
	}).on('click',function(e){
		if($(e.target).closest('.open').length==0){
			$('.open').removeClass('open');
		}
	});
});
	//var myScroll = new IScroll('#aside',{ mouseWheel: true ,checkDOMChanges:true,click:true});
	
	
	