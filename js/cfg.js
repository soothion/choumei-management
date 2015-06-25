var cfg={
	env:'dev',
	dev:'',
	test:'',
	product:'',
	getHost:function(){
		return cfg[cfg.env];
	},
	version:'1.0'
}