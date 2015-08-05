if(!localStorage.getItem('token')){
	location.href='/module/user/login.html';
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
	var time=500;
	$(document.body).on('loading',function(e){
		timeStamp=e.timeStamp;
		loadbar.stop().width(0).animate({
			width:$(window).width()-100
		},time);
		iframe.css('opacity',0.35);
		$(this).one('loadingend',function(e){
			if(e.timeStamp-timeStamp<time){
				setTimeout(function(){
					loadbar.animate({
						width:'100%'
					},150,'swing',function(){
						loadbar.css({width:0});
					});
					iframe.css('opacity',1);
				},time-(e.timeStamp-timeStamp))
			}else{
				loadbar.animate({
					width:'100%'
				},150,'swing',function(){
					loadbar.css({width:0});
				});
				iframe.css('opacity',1);
			}
		});
	})
	$('aside').on('click','li',function(){
		$('aside li.active').removeClass('active');
		$(this).addClass('active');
	}).on('click','a',function(){
		if(iframe[0].contentWindow.location.href==this.href){
			iframe[0].contentWindow.location.reload();
		}
	}).on('click','.menu-category-title',function(e){
		var $this=$(this);
		$this.parent().addClass('active').children('ul').slideToggle(200);
		$this.parent().siblings().removeClass('active').children('ul').slideUp(200);
	});
	$('.refresh').on('click',function(){
		iframe[0].contentWindow.location.reload();
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
							localStorage.removeItem('token');
							location.href='/module/user/login.html';
						}
					}
				});
			}
		});
	});
	if(window.ie9){
		$('.swiper-slide').hide();
		$('.nav-main li').on('click',function(){
			var $this=$(this)
			$this.addClass('active').siblings().removeClass('active');
			$('.swiper-slide').eq($this.index()).show().siblings().hide();
		}).first().trigger('click');
		return;
	}
	var swiper = new Swiper($('aside .swiper-container')[0],{
		loop: true,
		initialSlide : 0,
		spaceBetween: 0,
		simulateTouch:false
	});
	$('.nav-main li').on('click',function(){
		var $this=$(this)
		$this.addClass('active').siblings().removeClass('active');
		swiper.slideTo($this.index()+1);
	}).first().addClass('active');
});
	//var myScroll = new IScroll('#aside',{ mouseWheel: true ,checkDOMChanges:true,click:true});
	
	
	