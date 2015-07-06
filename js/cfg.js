var cfg={
	env:'dev',
	dev:'http://192.168.13.46:8090/',
	test:'http://192.168.13.49/',
	product:'',
	getHost:function(){
		return cfg[cfg.env];
	},
	version:'1.0'
}