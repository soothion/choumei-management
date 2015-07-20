var cfg={
	version:'1.0',
	url:'<#MANAGER_BACK_URL#>',
	env:'dev',
	dev:'http://192.168.13.46:8090/',
	test:'',
	product:'',
	getHost:function(){
		if(cfg.url&&cfg.url.indexOf('<#')==-1){
			return cfg.url;	
		}
		if(cfg.env){
			return cfg[cfg.env];
		}
	}
}
if(!cfg.url&&(cfg.env=='dev'||cfg.env=='test')){
	cfg.version=Math.random()*10;
}

document.writeln('<meta name="renderer" content="webkit|ie-stand">');
document.writeln('<meta http-equiv=”X-UA-Compatible” content=”IE=edge” > ');
document.writeln('<meta charset="utf-8">');
document.writeln('<link rel="icon" href="http://app.choumei.cn/images/logo16.ico" type="image/x-icon">');
document.writeln('<meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no">');
document.writeln('<meta name="apple-mobile-web-app-capable" content="yes">');
document.writeln('<meta name="apple-mobile-web-app-status-bar-style" content="black">');
document.writeln('<meta name="format-detection" content="telephone=no">');
document.writeln('<link rel="stylesheet" href="/awesome/css/font-awesome.min.css?v='+cfg.version+'" />');
document.writeln('<link rel="stylesheet" href="/css/reset.css?v='+cfg.version+'" />');
document.writeln('<link rel="stylesheet" href="/css/global.css?v='+cfg.version+'" />');
document.writeln('<link rel="stylesheet" href="/css/admin.css?v='+cfg.version+'" />');
document.writeln('<script type="text/javascript" src="/js/jquery.min.js?v='+cfg.version+'"></script>');
document.writeln('<script type="text/javascript" src="/js/sea.js?v='+cfg.version+'"></script>');
document.writeln('<script type="text/javascript" src="/js/ejs.js?v='+cfg.version+'"></script>');
document.writeln('<script type="text/javascript" src="/js/lib.js?v='+cfg.version+'"></script>');
document.writeln('<script type="text/javascript" src="/js/access.js?v='+cfg.version+'"></script>');
document.writeln('<script type="text/javascript" src="/js/admin.js?v='+cfg.version+'"></script>');