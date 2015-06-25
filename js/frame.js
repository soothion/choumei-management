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
	}).trigger('loading');
	$('aside').on('click','li',function(){
		$('aside li.active').removeClass('active');
		$(this).addClass('active');
	});
	$('aside').on('click','a',function(){
		if(iframe[0].contentWindow.location.href==this.href){
			iframe[0].contentWindow.location.reload();
		}
		$(document.body).trigger('loading');
	});
	
	$('.back').on('click',function(){
		$(document.body).trigger('loading');
		iframe[0].contentWindow.history.back();
	});
	$('.forward').on('click',function(){
		$(document.body).trigger('loading');
		iframe[0].contentWindow.history.forward();
	})
	$('.refresh').on('click',function(){
		$(document.body).trigger('loading');
		iframe[0].contentWindow.location.reload();
	});
	
	$(document).on('click','.menu-category-title',function(e){
		var $this=$(this);
		$this.parent().addClass('active').siblings().removeClass('active');
	})
	var myScroll = new IScroll('#scroller',{ mouseWheel: true ,checkDOMChanges:true,click:true});
	var toggle=function(){
		$('.frame-main').toggleClass('flat');
		var $this=$('.article-arrow');
		if($this.text()=='>'){
			$this.text('<');
		}else{
			$this.text('>');
		}
		myScroll.refresh();
	}
	$('.article-arrow').show().on('click',toggle);
	if(lib.isMobile()){
		toggle();
	}
	$('.nav-main li,.nav-sub li').on('click',function(){
		$(this).addClass('active').siblings().removeClass('active');
	})