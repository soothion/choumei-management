(function () {
	jQuery.support.cors = true;
	seajs.config({
		'map': [
			[ /^(.*\.(?:css|js))(.*)$/i, '$1?v='+cfg.version ]
		]
	});
	EJS.ext = '.html?v=' + cfg.version;
    var lib = {
		tools:{
			getDate:function(time){
				var date= time ? new Date(time*1000) : new Date();
				return date.getFullYear()+"-"+(date.getMonth()<10?"0"+(date.getMonth()+1):date.getMonth()+1)+"-"+(date.getDate()<10?"0"+date.getDate():date.getDate());
			},
			parseQuery: function (str) {//解析字符串的参数
				var ret = {},reg = /([^?=&]+)=([^&]+)/ig,match;
				while (( match = reg.exec(str)) != null) {
					ret[match[1]] = decodeURIComponent(match[2]);
				}
				return ret;
			},
			browser:function(){
				var ua=navigator.userAgent;
				return {
					mobile:/(iphone|ipod|ipad|android|ios|windows phone)/i.test(ua),
					android:/(android)/i.test(ua),
					ios:/(iphone|ipod|ipad)/i.test(ua),
					winphone:/(windows phone)/i.test(ua),
					webkit:/webkit/i.test(ua)
				}
			},
			getFormData:function($form){
				var data={};
				var fields=$form.serializeArray();
				$.each(fields,function(i,field){
					//防止xss攻击
					if(field.value&&$form.data('xss')!='none'){
						field.value=$.trim(field.value.replace(/>/g,'&gt;').replace(/</g,'&lt;'));
					}
					if(!data[field.name]){
						if($('input[name="'+field.name+'"]').attr('type')=='checkbox'){
							data[field.name]=[];
							if(field.value){
								data[field.name].push($.trim(field.value));
							}
						}else{
							data[field.name]=$.trim(field.value);
						}
					}else{
						if(data[field.name] instanceof Array){
							data[field.name].push(field.value);
						}
					}
				});
				return data;
			}
		},
        ajax: function (options) {
			if(options.url.indexOf('http://')==-1){
				options.url=cfg.getHost()+options.url;
				if(!options.data){
					options.data={};
				}
				if(localStorage.getItem('token')&&options.url.indexOf('/login')==-1){
					options.url+=(options.url.indexOf('?')==-1?"?":"&")+"token="+localStorage.getItem('token');
				}
			}
			options.timeout=6000;
			/*
			options.headers={
				token:localStorage.getItem('token')
			}*/
			var done=function(data){
				//code 异常处理
				if(data.result==0){
					if(data.code==402){
						parent.lib.popup.result({
							text:"出现异常：没有权限",
							bool:false
						});
					}else{
						if(data.code==401||data.code==400){
							data.msg="登录超时，请重新登录";
						}
						parent.lib.popup.result({
							text:"出现异常："+data.msg,
							bool:false,
							define:function(){
								if(data.code==400||data.code==401){
									parent.location.href="/module/system/user/login.html";
								}
							}
						});
					}
				}
			}
			if(options.done){
				done=options.done;
			}
			var promise=$.ajax(options);
			promise.fail(function(xhr, status){
				if(status=="abort") return;
				var msg = "请求失败，请稍后再试!";
				if (status === "parseerror") msg = "数据响应格式异常!";
				if (status === "timeout")    msg = "请求超时，请稍后再试!";
				if (status === "offline")    msg = "网络异常，请稍后再试!";
				parent.lib.popup.tips({text:'<i class="fa fa-times-circle"></i>'+msg,time:2000});
			}).done(done).done(function(data,status,xhr){
				//console.log(xhr.getAllResponseHeaders());
				if(data.token){
					localStorage.setItem('token',data.token);
				}
			});
            return promise;
        },
		getSession:function(){
			return localStorage.getItem('session')?JSON.parse(localStorage.getItem('session')):{}
		},
		setSession:function(obj){
			var session=$.extend(this.getSession(),obj);
			localStorage.setItem('session',JSON.stringify(session));
		},
        ejs:{
            render:function(temp,data){
                return new EJS(temp).render($.extend(this.getDefault(),data));
            },
            getDefault:function(){
                return {
                    query:lib.query,
                    session:lib.getSession()
                }
            }
        },
        ajat: function (_protocol) {
            return new Ajat(_protocol);
        },
        popup: {//弹出层
            path:'/js/popup.js',
            alert: function (options) {
                seajs.use(this.path,function(a){
                    a.alert(options);
                });
            },
            confirm: function (options) {
                seajs.use(this.path,function(a){
                    a.confirm(options);
                });
            },
            prompt: function (options) {
                seajs.use(this.path,function(a){
                    a.prompt(options);
                });
            },
            sheet: function (options) {
                seajs.use(this.path,function(a){
                    a.sheet(options);
                });
            },
            menu: function (options) {
                seajs.use(this.path,function(a){
                    a.menu(options);
                });
            },
			swiper: function (options) {
                seajs.use(this.path,function(a){
                    a.swiper(options);
                });
            },
            tips: function (options) {
				var popup =$('.popup-tips');
				if(popup.length==0){
					popup = $('<div class="popup popup-center popup-tips" ><div><div class="popup-tips-text">' + options.text + '</div></div></div>');
					$(document.body).append(popup);
				}else{
					popup.find('.popup-tips-text').html(options.text);
				}
                if(options.time){
					setTimeout(function(){
						popup.fadeOut(300,function(){
							popup.remove();
						});
						options.define && options.define.call();
					},options.time);
                }else{
					options.define && options.define.call();
				}
                
            },
            close: function () {
                seajs.use(this.path,function(a){
                    a.close();
                });
            },
			result:function(options){
				options=options||{};
				if(options.bool===undefined){
					options.bool=true;
				}
				if(!options.text){
					options.text=(options.bool?"操作成功":"操作失败");
				}
				if(options.time===undefined){
					options.time=1000;
				}
				options.text='<i class="fa fa-'+(options.bool?"check":"times")+'-circle"></i>'+options.text;
				this.tips(options)
			},
			loading:function(options){
				options.text='<img src="/images/oval.svg" class="loader"/>'+options.text;
				parent.lib.popup.tips(options);
			}
        },
		getFormData:function($form){
			return this.tools.getFormData($form);
		},
        init:function(){
			lib.query={};
			if(location.search){
				$.extend(lib.query,this.tools.parseQuery(location.search.replace('?','')))
				lib.query._=location.search.replace('?','');
			}
			if(location.hash){
				$.extend(lib.query,this.tools.parseQuery(location.hash.replace('#','')))
				lib.query._=location.hash.replace('#','');
			}
        },
		puploader:{
			tokenCfg:{
				request:{
					userId:'ba6c14bb30e17281',
					type:1,
					num:1
				},
				count:1,
				time:new Date().getTime()
			},
			use:function(cb){//加载上传资源文件
				var arr=[
					'/qiniu/demo/js/plupload/plupload.full.min.js',
					'/qiniu/demo/js/qiniu.js',
					'/js/jquery.md5.js'
				];
				seajs.use(arr,function(){
					seajs.use(['/qiniu/demo/js/plupload/i18n/zh_CN.js']);
					cb &&cb();
				});
			},
			getToken:function(cb){
				var self=this;
				var _arguments=arguments;
				var query={
					'bundle':"FQA5WK2BN43YRM8Z",
					'version':"5.3",
					'device-type':window.navigator.appCodeName,
					'device-uuid':this.random(),
					'device-model':'',
					'device-network':'',
					'device-dpi':window.screen.width+"x"+window.screen.height,
					'device-os':window.navigator.userAgent,
					'timestamp':new Date().getTime(),
					'sequence':this.tokenCfg.time+(this.tokenCfg.count++),
					'request':JSON.stringify(this.tokenCfg.request)
				}
				query.sign=$.md5($.param(query));
				lib.ajax({
					url:cfg.url.token,
					data:query,
					dataType:'json',
					success:function(data){
						if(data.code==0&&data.response&&data.response.data&&data.response.data[0]){
							Qiniu.temp=data.response.data[0];
							cb &&cb(data.response.data[0]);
						}else{
							parent.lib.popup.result({
								text:'获取上传token失败',
								bool:false
							});
						}
						clearTimeout(self.timer);
						self.timer=setTimeout(function(){
							lib.puploader.getToken.apply(lib.puploader,_arguments);
						},1000*60);
					}
				});
			},
			random:function(len) {
				len = len || 32;
				var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
				var maxPos = $chars.length;
				var pwd = [];
				for (var i = 0; i < len; i++) {
					pwd.push($chars.charAt(Math.floor(Math.random() * maxPos)));
				}
				return pwd.join("");
			},
			create:function(options,cb){
				var _default={
					runtimes:'html5,flash,html4',
					flash_swf_url:'/qiniu/demo/js/plupload/Moxie.swf',
					silverlight_xap_url:'/qiniu/demo/js/plupload/Moxie.xap',
					domain:cfg.url.upload,
					dragdrop:true,
					multi_selection:false,
					init:{
						Key:function(up, file) {
							// 若想在前端对每个文件的key进行个性化处理，可以配置该函数
						   // 该配置必须要在 unique_names: false , save_key: false 时才生效,key即为上传文件名
						   return Qiniu._fileName;
						}
					}
				};
				options=$.extend(_default,options);
				if(options.domain.indexOf('qiniu')==-1){
					seajs.use(['/qiniu/demo/js/plupload/plupload.full.min.js'],function(){
						seajs.use(['/qiniu/demo/js/plupload/i18n/zh_CN.js']);
						options.url=options.domain;
						delete options.runtimes;
						delete options.dragdrop;
						var uploader=new plupload.Uploader(options);
						uploader.init();
						if(options.auto_start){
							uploader.bind('FilesAdded',function(up,files){
								up.start();
							});
						}
						uploader.bind('UploadFile',function(){
							var $dom=$('<div class="popup-overlay" style="background:rgba(255,255,255,0.4)"></div>');
							$(document.body).append($dom);
						});
						uploader.bind('UploadProgress',function(){
							parent.lib.popup.loading({text:options.loaderText||'文件上传中..'});
						});
						uploader.bind('UploadComplete',function(up,file){
							$('.popup-overlay').remove();
						});
						uploader.bind('Error',function(up, err, errTip){
							parent.lib.popup.result({bool:false,text:err.message});
						});
						uploader.bind('FileUploaded',function(up,file,res){
							if(res&&res.response&&typeof res.response=='string'){
								var data=JSON.parse(res.response);
								if(data.result==1){
									parent.lib.popup.result({text:options.successText||'文件上传成功'});
								}else{
									parent.lib.popup.result({bool:false,text:options.failText||'文件上传失败'});
								}
								if(data.token){
									localStorage.setItem('token',data.token);
								}
							}
						});
						cb && cb(uploader)
					});
				}else{
					return Qiniu.uploader(options);
				}
			},
			createImage:function(){
				var imagePreview=$('<div style="position:absolute;left:0;top:0;z-index:-1;width:100%;height:100%;overflow:hidden;visibility:hidden;"><img/></div>')
				$(document.body).append(imagePreview);
				return imagePreview;
			},
			file:function(options,cb){
				var self=this;
				this.use(function(){
					self.getToken(function(data){
						options.uptoken=data.uptoken;
						Qiniu._fileName=data.fileName;
						if(!options.max_file_size){
							options.max_file_size=data.maxFileSize+'mb';
						}
						var uploader=self.create(options);
						uploader.bind('UploadFile',function(){
							var $dom=$('<div class="popup-overlay" style="background:rgba(255,255,255,0.4)"></div>');
							$(document.body).append($dom);
						});
						uploader.bind('UploadProgress',function(){
							parent.lib.popup.loading({text:options.loaderText||'文件上传中..'});
						});
						uploader.bind('UploadComplete',function(up,file){
							//console.log(arguments);
							$('.popup-overlay').remove();
						});
						uploader.bind('FileUploaded',function(up,file,res){
							if(res&&res.response&&typeof res.response=='string'){
								var data=JSON.parse(res.response);
								clearTimeout(self.timer);
								self.getToken(function(data){
									Qiniu.token=data.uptoken;
									Qiniu._fileName=data.fileName;
								});
								if(data.code==0){
									parent.lib.popup.result({text:options.successText||'文件上传成功'});
								}else{
									parent.lib.popup.result({bool:false,text:options.failText||'文件上传失败'});
								}
							}
						});
						uploader.bind('Error',function(up, err, errTip){
							parent.lib.popup.result({bool:false,text:err.message});
						});
						cb &&cb(uploader);
					});
				});
			},
			getSource:function(file,cb){//file为plupload事件监听函数参数中的file对象,callback为预览图片准备完成的回调函数 
				if (!file || !/image\//.test(file.type)) return; //确保文件是图片
				if (file.type == 'image/gif') {//gif使用FileReader进行预览,因为mOxie.Image只支持jpg和png
					var fr = new mOxie.FileReader();
					fr.onload = function () {
						cb(fr.result);
						fr.destroy();
						fr = null;
					}
					fr.readAsDataURL(file.getSource());
				} else {
					var preloader = new mOxie.Image();
					preloader.onload = function () {
						//preloader.downsize(300, 300);//先压缩一下要预览的图片,宽300，高300
						var imgsrc = preloader.type == 'image/jpeg' ? preloader.getAsDataURL('image/jpeg', 80) : preloader.getAsDataURL(); //得到图片src,实质为一个base64编码的数据
						cb && cb(imgsrc); //callback传入的参数为预览图片的url
						preloader.destroy();
						preloader = null;
					};
					preloader.load(file.getSource());
				}
			},
			image:function(options,cb){
				options=$.extend({
					successText:'图片上传成功',
					failText:'图片上传失败',
					loaderText:'图片上传中..',
					sizeErrorText:'图片的尺寸大小不正确'
				},options);
				if(options.imageLimitSize){
					if(options.auto_start===true){
						options._auto_start=true;
						options.auto_start=false;
					}
				}
				this.file(options,function(uploader){
					uploader.bind('FileUploaded',function(up,file,res){
						if(res&&res.response&&typeof res.response=='string'){
							var data=JSON.parse(res.response);
							var options=up.getOption();
							if(data.code==0){
								uploader.createThumbnails && uploader.createThumbnails(data.response);
								uploader.preview&&uploader.preview(data.response)
							}
						}
					});
					if(options.browse_button){
						var $target=$('#'+options.browse_button).parent();
						uploader.thumbnails=$target.siblings('.control-thumbnails');
						if($target.hasClass('control-image-upload')&&uploader.thumbnails.length==1){
							uploader.createThumbnails=function(data){
								uploader.thumbnails.append(lib.ejs.render({url:uploader.thumbnails.data('tempid')||'/module/public/template/thumbnails'},{data:[data]}));
								if(uploader.thumbnails.data('max')&&parseInt(uploader.thumbnails.data('max'))==uploader.thumbnails.children().length){
									uploader.thumbnails.siblings('.control-image-upload').hide();
								}
							}
							uploader.thumbnails.on('click','.control-thumbnails-remove',function(){
								var item=$(this).closest('.control-thumbnails-item');
								if(item.attr('id')){
									uploader.removeFile(item.attr('id'));
								}
								item.remove();
								if(uploader.thumbnails.data('max')&&parseInt(uploader.thumbnails.data('max'))>uploader.thumbnails.children().length){
									uploader.thumbnails.siblings('.control-image-upload').show();
								}
							});
						}else if($target.closest('.control-single-image').length==1){
							uploader.area=$target.closest('.control-single-image');
							uploader.preview=function(data){
								this.area.find('img').attr('src',data.thumbimg||data.img).data('original',data.img);
								this.area.find('input.original').val(data.img).blur();
								this.area.find('input.thumb').val(data.thumbimg).blur();
							}
						}
					}
					if(options.imageLimitSize){
						uploader.bind('FilesAdded',function(up, files){
							plupload.each(files, function(file) {
								var image=lib.puploader.createImage();
								lib.puploader.getSource(file,function(src){
									image=image.find('img').attr('src',src);
									var options=up.getOption();
									var imageLimitSize=options.imageLimitSize;
									if(typeof imageLimitSize=="string"){
										var width=imageLimitSize.split('*')[0];
										var height=imageLimitSize.split('*')[1];
										if(image.width()!=width||image.height()!=height){
											parent.lib.popup.result({bool:false,text:options.sizeErrorText});
											up.removeFile(file);
										}else{
											if(options._auto_start){
												up.start();
											}
										}
										image.parent().remove();
									}
									if(typeof imageLimitSize=="function"){
										if(!imageLimitSize(image.width(),image.height())){
											parent.lib.popup.result({bool:false,text:options.sizeErrorText});
											up.removeFile(file);
										}else{
											if(options._auto_start){
												up.start();
											}
										}
										image.parent().remove();
									}
								});
							});
						});
					}
					cb &&cb(uploader);
				});
			}
		},
		uploader:{
			use:function(cb){//加载上传资源文件
				seajs.use(['/css/webuploader.css','/js/webuploader.js'],function(){
					cb &&cb();
				});
			},
			create:function(options){//创建上传对象
				options.swf='/js/Uploader.swf';
				if(options.server.indexOf('http://')==-1){
					options.server=cfg.getHost()+options.server;
				}
				return WebUploader.create(options);
			},
			createImage:function(){
				var imagePreview=$('<div style="position:absolute;left:0;top:0;z-index:-1;width:100%;height:100%;overflow:hidden;visibility:hidden;"><img/></div>')
				$(document.body).append(imagePreview);
				return imagePreview;
			},
			image:function(options,cb){//图片上传
				var self=this;
				options.loaderText=options.loaderText||"图片准备上传中";
				options.successText=options.successText||"图片上传完成";
				options.errorText=options.errorText||"图片上传失败";
				if(options.imageLimitSize){
					if(options.auto===true){
						options._auto=true;
						options.auto=false;
					}
				}
				this.file(options,function(uploader){
					//支持缩略小图预览
					if(uploader.options.pick.id){
						var $target=$(uploader.options.pick.id);
						uploader.thumbnails=$target.siblings('.control-thumbnails');
						if($target.hasClass('control-image-upload')&&uploader.thumbnails.length==1){
							uploader.createThumbnails=function(data){
								var arr=[data];
								arr.postName=this.options.postName;
								uploader.thumbnails.append(lib.ejs.render({url:'/module/public/template/thumbnails'},{data:arr}))
								if(uploader.thumbnails.data('max')&&parseInt(uploader.thumbnails.data('max'))==uploader.thumbnails.children().length){
									uploader.thumbnails.siblings('.control-image-upload').hide();
								}
							}
							uploader.thumbnails.on('click','.control-thumbnails-remove',function(){
								var item=$(this).closest('.control-thumbnails-item');
								if(item.attr('id')){
									uploader.removeFile(item.attr('id'));
								}
								item.remove();
								if(uploader.thumbnails.data('max')&&parseInt(uploader.thumbnails.data('max'))>uploader.thumbnails.children().length){
									uploader.thumbnails.siblings('.control-image-upload').show();
								}
							});
						}
					}
					// 当有文件添加进来的时候
					uploader.on('fileQueued', function( file ) {
						if(options.imageLimitSize){
							var self=this;
							uploader.makeThumb(file, function( error, src ) {
								if(error){
									parent.lib.popup.result({bool:false,text:"图片预览失败"});
									return;
								}
								//校验图片尺寸大小
								var imagePreview=lib.uploader.createImage();
								imagePreview.children('img').on('load',function(){
									var $this=$(this);
									if(typeof self.options.imageLimitSize =='string'){
										var width=parseInt(self.options.imageLimitSize.split('*')[0]);
										var height=parseInt(self.options.imageLimitSize.split('*')[1]);
										if($this.width()!=width||$this.height()!=height){
											self.trigger( 'error', 'IAMGE_SIZE',file);
										}else{
											if(self.options._auto){
												self.upload();
											}
											if(self.options.thumb&&self.createThumbnails){
												var data=$.extend({},file,{src:src});
												self.createThumbnails(data);
											}
										}
									}
									if(typeof self.options.imageLimitSize =='function'){
										var ret=self.options.imageLimitSize($this.width(),$this.height());
										if(ret){
											if(self.options._auto){
												self.upload();
											}
											if(self.options.thumb&&self.createThumbnails){
												var data=$.extend({},file,{src:src});
												self.createThumbnails(data);
											}
										}else{
											self.trigger( 'error', 'IAMGE_SIZE',file);
										}
									}
									imagePreview.remove();
								}).attr('src',src);
							},1,1);
						}
					});
					uploader.on('uploadSuccess',function(file,res){
						var self=this;
						if(this.options.imageId){
							uploader.makeThumb(file, function( error, src ) {
								document.getElementById(self.options.imageId).src=src;
							},1,1);
						}
						if(res.result==1){
							var data=res.data;
							if(data.main&&data.main.images&&data.main.images[0]){
								if(!self.options.thumb&&self.createThumbnails){
									var data=$.extend({},file,{src:data.main.images[0].img});
									self.createThumbnails(data);
								}
							}
						}
					});
					uploader.on('error',function(err){
						if(err=='IAMGE_SIZE'){
							parent.lib.popup.result({bool:false,text:"图片的大小尺寸不正确"});
						}
					});
					cb&&cb(uploader);
				});
			},
			file:function(options,cb){//文件上传
				var self=this;
				this.use(function(){
					var uploader=self.create(options);
					uploader.on('uploadStart',function(file){
						parent.lib.popup.loading({text:options.loaderText||"文件准备上传中"});
					});
					uploader.on('uploadSuccess',function(file){
						parent.lib.popup.result({text:options.successText||"文件上传完成"});
					});
					uploader.on('uploadError',function(file){
						parent.lib.popup.result({bool:false,text:options.errorText||"文件上传失败"});
					});
					uploader.on('uploadProgress',function(file, percentage){
						parent.lib.popup.tips({
							text:'<img src="/images/oval.svg" class="loader"/><br />“'+file.name+'”文件上传进度：'+(Math.ceil(percentage*100))+"%"
						});
					});
					uploader.on('error',function(err){
						if(err=='F_EXCEED_SIZE'){
							parent.lib.popup.result({bool:false,text:"上传文件过大"});
						}
						if(err=='Q_EXCEED_NUM_LIMIT '){
							parent.lib.popup.result({bool:false,text:"上传文件数过大"});
						}
						if(err=='Q_TYPE_DENIED '){
							parent.lib.popup.result({bool:false,text:"上传文件格式不正确"});
						}
					});
					cb&&cb(uploader);
				});
			}
		}
    }
    lib.init();
	
    /*Ajat对象*/
    function Ajat(_protocol) {
        this._protocol = _protocol;
        this.protocol={
            url: '',
            query: '',
            custom: {
                domid: '',
                tempid: '',
                insert:''
            }
        }
        this.parseProtocol();
    }

    /**
     * protocol:自定义协议
     * protocol.url:请求地址
     * protocol.query:请求参数
     * protocol.custom:#号后面的参数,自定义配置.domid,tempid是必需项,insert是插入方式(前后插入)：before||after,默认为覆盖，cache=true是缓存请求，loader=true显示加载提示框loadertext提示语，
     */
    Ajat.prototype = {
        setUrl:function(url){//修改请求地址
            this.protocol.url=lib.ejs.render({text: url},{});
        },
        setQuery:function(obj){//修改请求参数
            this.protocol.query=$.extend(this.protocol.query,obj);
        },
        setCustom:function(obj){//修改自定义配置
            this.protocol.custom=$.extend(this.protocol.custom,obj);
        },
        render: function () {//发送http请求关渲染HTML
            if(this.protocol.url){
                return this.fetch();
            }else{
                this.template({});
            }
        },
        parseProtocol: function () {//解析协议内容
            if (this._protocol) {
                this.renderProtocol();
                var arr =this._protocol.split(/\?|#/);
                if(arr.length==3){
                    this.protocol.url = arr[0];
                    this.protocol.query = lib.tools.parseQuery(arr[1]);
                    this.protocol.custom = lib.tools.parseQuery(arr[2]);
                }else if(arr.length==2){
					if(arr[0].indexOf('/')>-1){
						this.protocol.url=arr[0];
					}else{
						this.protocol.query=lib.tools.parseQuery(arr[0])
					}
                    this.protocol.custom = lib.tools.parseQuery(arr[1]);
                }else if(arr.length==1){
					this.protocol.custom = lib.tools.parseQuery(arr[0]);
				}
                this.dom=document.getElementById(this.protocol.custom.domid);
            }
        },
        renderProtocol:function(){//渲染协议
            this._protocol = lib.ejs.render({text: this._protocol},{});
        },
        fetch: function () {//发送http请求并触发fetch事件
            var self = this;
            var pro=this.protocol;
            $(this.dom).trigger('fetch',{protocol:pro});
            this.showLoader();
            var options={
                url: pro.url,
                data: pro.query,
				cache:false,
                success: function (data) {
					if(data){
						//防止xss攻击
						if(self.protocol.custom.xss!=='false'){
							data=JSON.stringify(data);
							data=JSON.parse(data.replace(/>/g,'&gt;').replace(/</g,'&lt;'));
						}
						if(!self.exception(data)){
							self.template(self.parseResponse(data));
						}
					}
                },
                error:function(xhr,textStatus){
                    self.exception({errorLevel:'xhr',status:xhr.status,readyState:xhr.readyState,textStatus:textStatus});;
                }
            };
            if(pro.custom.cache=='true'){
                options.cache=true;
            }
            return lib.ajax(options);
        },
        setExternal:function(data){//引入外部数据，以便模板引擎渲染时能获取；
            this.external=data;
        },
        insertFix:{
            'after':'append',
            'before':'prepend'
        },
        template: function (data) {//模板引擎渲染并触发ready事件
            var pro=this.protocol;
            var domid = pro.custom.domid;
            var tempid = pro.custom.tempid;
            var options = /\/|\./g.test(tempid)?{url: tempid}:{text: document.getElementById(tempid).innerHTML};
			var tempData=$.extend({data:data,protocol:pro},this.external);
            this.insert(lib.ejs.render(options,tempData)).trigger('_ready',{protocol:pro,response:data});
            this.hideLoader();
            this.ready();
            this.format();
            this.destroy();
        },
        format : function(){
            $("td.format").each(function(index,item){
                var val = $(this).text();  
                if(val){
                	if(isNaN(val)){
                		$(this).text(new Date(val).format("yyyy-MM-dd"));
                	}else{
                		$(this).text(new Date(val*1).format("yyyy-MM-dd"));
                	}
                }
            });

            $("td.formatHms").each(function(index,item){
                var val = $(this).text();  
                if(val){
                	if(isNaN(val)){
                		$(this).text(new Date(val).format("yyyy-MM-dd hh:mm:ss"));
                	}else{
                		$(this).text(new Date(val*1).format("yyyy-MM-dd hh:mm:ss"));
                	}
                }
            });
        },
        exception:function(data){//异常处理
            var $dom=$(this.dom);
            if(data.errorLevel=='xhr'){
                $dom.trigger('exception',data);
                return true
            }else if(data.result==0){
                $dom.trigger('exception',data);
                return true
            }
            return false;
        },
        insert:function(html){//模板引擎渲染后返回带数据的字符串添加到页面
            return $(this.dom)[this.insertFix[this.protocol.custom.insert]||'html'](html);
        },
        parseResponse: function (data) {//模板引擎渲染前解析响应回来的数据
            return data.data;
        },
        showLoader: function () {//发送http请求前的loading效果
            var pro=this.protocol.custom;
            if (pro.loader||pro.loadertext) {
                lib.popup.tips({text:'<img src="/svg-loaders/oval.svg" class="loader"/>'+(pro.loadertext || '正在加载中')});
            }
        },
        hideLoader:function(){//loading效果结束
            lib.popup.close();
        },
        ready:function(){//模板引擎渲染后执行的函数，主要考虑扩展用的
        },
        destroy:function(){//移除对象引用
            this.external=null;
            this.dom=null;
        },
        toString:function(){
            var pro=this.protocol
            return pro.url+(!$.isEmptyObject(pro.query)?'?'+decodeURIComponent($.param(pro.query)):'')+'#'+ decodeURIComponent($.param(pro.custom));
        }
    }
	Ajat.before=function(){
		
	}
    /**
     * ajat自动执行
     */
    $(function(){
		Ajat.before();
        var ajat=document.body.getAttribute('ajat');
        if(ajat){
            lib.ajat(ajat).render();
        }
        Ajat.run();
    });
    Ajat.run=function($dom){
        $dom=$dom||$(document.body);
        $dom.find('script[type="text/template"]').each(function(){
            if(this.parentNode!=document.body){
                document.body.appendChild(this);
            }
        });
        $dom.find('[ajat]').each(function(){
            var ajat=this.getAttribute('ajat');
            lib.ajat(ajat).render();
            if(this.getAttribute('ajat-one')){
                this.setAttribute('_ajat',ajat);
                this.removeAttribute('ajat');
            }
        });
    }
    /**
     * 同步关联数据
     */
    Ajat.sync=function($dom){
        $dom.find('input[type=hidden][ajat-sync-selector]').each(function(){
            var $this=$(this);
            var selector=$this.attr('ajat-sync-selector');
            if(selector){
                $(selector).html($this.val());
                $this.remove();
            }
        });
    }
    /**
     * ajat协议事件
     */
    Ajat.event=function(){
        $(document).on('click','[ajat-click]',function(){
            var ajat=this.getAttribute('ajat-click');
            lib.ajat(ajat).render();
            if(this.getAttribute('ajat-one')){
                this.removeAttribute('ajat-click');
                this.setAttribute('_ajat',ajat);
            }
        }).on('change','select[ajat-change]',function(){
			if(this.value){
				var ajat=this.getAttribute('ajat-change').replace('${value}',this.value);
				lib.ajat(ajat).render();
			}
		}).on('_ready',function(e){
			var $target=$(e.target);
            Ajat.run($target);
            Ajat.sync($target);
			//加载对应的资源
			var resources=$target.attr('ajat-resources');
			resources && Ajat.seajs(resources.split(','));
        });
    }
    Ajat.event();
	Ajat.seajs=function(arr){
		seajs.use(arr);
	}
    lib.Ajat=Ajat;
	
	/**
	*form表单封装
	*/
	var Form=function(el){
		this.el=el;
		this.init();
	}
	Form.prototype={
		selector:'input,textarea,select',
		hooks:{
			email:'^[a-zA-Z0-9_-][\.a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$',
			mobile:'^1[0-9]{10}$',
			phone:'^\\d{7,12}$',
			password:'^[0-9A-Za-z]{6,20}$',
			float:function(val){
                // 表达式验证有问题：2. ， 2....
				//var reg=new RegExp('^[+-]?([0-9]*\.?[0-9]+|[0-9]+\.?[0-9]*)([eE][+-]?[0-9]+)?$');
                var reg = new RegExp('^(-?\\d+)(\.\\d+)?$');
				if(reg.test(val)){
					var arr=val.split('.');
					if(arr[0].length>12){
						return {msg:'输入值整数不能大于12位且小数不能大于2位'};
					}if(arr[1]&&arr[1].length>2){
						return {msg:'输入值的整数不能大于12位且小数不能大于2位'};
					}else{
						return true;
					}
				}else{
					return false;
				}
			},
			number:function(val){
				var reg=new RegExp('^[0-9]+$');
				if(!isNaN(val)){
					if(val.indexOf('.')>-1){
						return {msg:'输入值不能含小数点'};
					}
				}
				if(reg.test(val)){
					if(val.length>12){
						return {msg:'输入值整数不能大于12位且不能有小数点'};
					}
				}
				return reg.test(val);
			},
			percent:function(val){
				val=parseFloat(val);
				return val>=0&&val<=100;
			}
		},
		init:function(){
			this.cfg={};
			this.cfg.requiredmsg=this.el.requiredmsg||"未填写";
			this.cfg.patternmsg=this.el.patternmsg||"不正确";
			this.bindEvent();
			if(!this.el.goback){
				this.el.goback=function(){
					if(parent!=window){
						history.back();
					}else{
						window.close();
					}
				}
			}
			if(!this.el._getFormData){
				this.el._getFormData=function(){
					return lib.tools.getFormData($(this))
				}
			}
		},
		validateFields:function(e,eventData){
			var $target=$(e.target);
			var val=$.trim($target.val());
			//有disabled的不做校验
			if($target.is(':disabled')){
				var error=this.getErrorDom($target);
				error.remove();
				return;
			}
			//复选框单选框校验的非空校验
			if($target.is('input[type="checkbox"]')||$target.is('input[type="radio"]')){
				var name=$target.attr('name');
				var inputs=$('input[name="'+name+'"]');
				if(inputs.filter(':checked').length==0&&inputs.last().attr('required')){
					inputs.last().trigger('error',{type:'required'});
					return;
				}
			}
			//非复选框单选框校验的非空校验
			if($target.attr('required')&&!val&&!$target.is('input[type="checkbox"]')&&!$target.is('input[type="radio"]')){
				$target.trigger('error',{type:'required'});
				return;
			}
			//正则校验
			var pattern=$target.attr('pattern');
			if(val&&pattern){
				var ret=this.hooks[pattern]||pattern;
				if(typeof ret=='string'){
					var reg=new RegExp(ret);
					if(!reg.test(val)){
						$target.trigger('error',{type:'pattern'});
						return;
					}
				}
				if(typeof ret=='function'){
					var result=ret(val);
					if(result===false){
						$target.trigger('error',{type:'pattern'});
						return;
					}else{
						if(result.msg){
							$target.trigger('error',{type:'error',errormsg:result.msg});
							return;
						}
						//数字和浮点型添加值的限制
						if(pattern=="number"||pattern=="float"){
							var min=$target.attr('min')
							if(min&&parseFloat(val)<parseFloat(min)){
								$target.trigger('error',{type:'pattern'});
								return;
							}
							var max=$target.attr('max')
							if(max&&parseFloat(val)>parseFloat(max)){
								$target.trigger('error',{type:'pattern'});
								return;
							}
						}
					}
				}
			}
			//匹配校验
			var match=$target.attr('match');
			if(val&&match){
				if(val!=$.trim($('#'+match).val())){
					$target.trigger('error',{type:'match'});
					return;
				}
			}
			//唯一校验
			var unique=$target.attr('unique');
			if(val&&unique&&val!=$target.data('value')){
				$target.trigger('error',{type:'unique',msg:'正在校验中...'});
				var self=this;
				var data={};
				unique=unique.replace('${value}',val);
				lib.ajax({
					url:unique,
					type:'post',
					async:false,
					success:function(data){
						if(data.result!=1){
							$target.trigger('error',{type:'unique'});
						}else{
							var error=self.getErrorDom($target);
							error.remove();
						}
					},
					done:function(){}
				});
				return;
			}
			var error=this.getErrorDom($target);
			error.hide();
			$target.trigger('pass');
		},
		required:function(e){//非空校验
			var $target=$(e.target);
			var error=this.getErrorDom($target);
			var $relative=$target;
			if($target.siblings('.unit').length==1){
				$relative=$target.siblings('.unit');	
			}
			if($target.parent('label').length==1){
				$relative=$target.parent('label');
			}
			var requiredmsg=this.cfg.requiredmsg;
			if($target.is('select')||$target.is('input[type="checkbox"]')||$target.is('input[type="radio"]')){
				requiredmsg='请选择';
			}
			error.show().html(($target.attr('requiredmsg')||requiredmsg));
			if(!error.is(':visible')){
				$relative.after(error);	
			}
			
		},
		pattern:function(e){//正则表达式校验
			var $target=$(e.target);
			var error=this.getErrorDom($target);
			var $relative=$target;
			if($target.siblings('.unit').length==1){
				$relative=$target.siblings('.unit');	
			}
			error.show().html(($target.attr('patternmsg')||this.cfg.patternmsg));
			if(!error.is(':visible')){
				$relative.after(error);	
			}
		},
		unique:function(e,data){//唯一校验
			var $target=$(e.target);
			var error=this.getErrorDom($target);
			var $relative=$target;
			if($target.siblings('.unit').length==1){
				$relative=$target.siblings('.unit');	
			}
			error.show().html((data.msg||$target.attr('uniquemsg')||this.cfg.uniquemsg));
			if(!error.is(':visible')){
				$relative.after(error);	
			}
		},
		match:function(e){//匹配校验
			var $target=$(e.target);
			var error=this.getErrorDom($target);
			var $relative=$target;
			if($target.siblings('.unit').length==1){
				$relative=$target.siblings('.unit');	
			}
			error.show().text($target.attr('matchmsg'))
			if(!error.is(':visible')){
				$relative.after(error);	
			}
		},
		error:function(e,data){
			var $target=$(e.target);
			var error=this.getErrorDom($target);
			var $relative=$target;
			if($target.siblings('.unit').length==1){
				$relative=$target.siblings('.unit');	
			}
			error.show().html(data.errormsg);
			if(!error.is(':visible')){
				$relative.after(error);	
			}
		},
		getErrorDom:function($target){
			var error=$target.siblings('.control-help');
			if($target.data('helpid')){
				error=$('#'+$target.data('helpid'));
			}else if($target.parent('label').length==1){

				error=$target.parent().siblings('.control-help');
			}
			if(error.length==0){
				error=$('<span class="control-help"></span>');
			}
			return error;
		},
		parseResponse:function(data){
			var $el=$(this.el);
			if(data.result==1){
				$el.trigger('success',data);
			}else{
				$el.trigger('fail',data);
			}
		},
		success:function(data){
			var self=this;
			parent.lib.popup.result({
				text:(data.msg||"数据更新成功"),
				define:function(){
					self.el.goback();
				}
			});
		},
		fail:function(data){
			parent.lib.popup.result({
				bool:false,
				text:(data&&data.msg?data.msg:"数据更新失败")
			});
		},
		bindEvent:function(){
			var self=this;
			$(this.el).attr('novalidate','novalidate').on('blur',this.selector,function(e,data){
				self.validateFields(e,data);
			}).on('error',this.selector,function(e,data){
				self[data.type]&&self[data.type](e,data);
			}).on('submit',function(e){
				self.validate();
				e.preventDefault();
			}).on('save',function(e,data){
				self.save(data);
			}).on('response',function(e,data){
				self.parseResponse(data);
			}).on('success',function(e,data){
				self.success(data);
			}).on('fail',function(e,data){
				self.fail(data);
			}).on('input',this.selector,function(e){
				var $this=$(this);
				if($this.attr('nospace')!==undefined&&/\s+/g.test($this.val())){
					$this.val($this.val().replace(/\s+/g,''));
				}
			}).on('focus','input[type="date"]',function(){
				var $this=$(this);
				if(!$this.attr('pattern')){
					$this.attr({pattern:"^([0-9]{4})-([0-9]{2})-([0-9]{2})$",patternmsg:"日期格式不正确"});
				}
			});
		},
		validate:function(untrigger){
			var $form=$(this.el);
			$form.find(this.selector).trigger('blur');
			if($form.attr('disabled'))return;
			var help=$form.find('.control-help:visible');
			if(help.length==0){
				var data=this.el._getFormData();
				$form.trigger('save',data);
			}else{
				$('html,body').animate({scrollTop:help.eq(0).offset().top-50},200);
				help.eq(0).siblings('input:visible').focus();
			}
		},
		save:function(data){
			var $el=$(this.el);
			if($el.attr('disabled')) return;
			$el.attr('disabled',true);
			parent.lib.popup.loading({text:'数据正在提交...'});
			var self=this;
			lib.ajax({
				url:$el.attr('action'),
				data:data,
				type:this.el.method,
				success:function(data){
					$(self.el).trigger('response',data);
				},
				error:function(xhr,code){
					$(self.el).attr('disabled',false);
					self.fail(null,{})
				}
			});
		}
	}
	lib.Form=Form;
	
	/**
	*select美化封装
	*/
	var Select=function(){
		this.init();
	}
	Select.prototype={
		selector:'select',
		init:function(){
			this.bindEvent();
		},
		bindEvent:function(){
			var self=this;
			$(document).on('focus',this.selector,function(e){
				e.stopPropagation();
				e.preventDefault();
			}).on('blur',this.selector,function(e){
				$('#s-list').remove();
				$(this).removeClass('focus');
			}).on('mousedown',this.selector,function(e){
				var $this=$(this);
				if(document._activeElement){
					$(document._activeElement).trigger('blur');
				}else{
					var nodeName=document.activeElement.nodeName;
					var tagName=document.activeElement.tagName;
					if(!nodeName){
						nodeName=tagName;
					}
					nodeName=nodeName.toUpperCase();
					if(nodeName=='SELECT'||nodeName=='INPUT'||nodeName=='TEXTAREA'){
						document.activeElement.blur();
					}
				}
				$this.addClass('focus');
				if(!this.disabled){
					self.instance(this);
					document._activeElement=this;
				}
				e.stopPropagation();
				e.preventDefault();
			}).on('mousedown',function(){
				$('select.focus').trigger('blur');
			});
		},
		instance:function(select){
			var list=$('<div class="s-list" id="s-list"></div>');
			var $select=$(select);
			$select.children().each(function(){
				var $this=$(this);
				list.append('<div class="s-list-item '+($this.is(':checked')?"active":"")+'" value="'+($this.attr('value')||'')+'">'+$this.text()+'</div>');
			});
			$(document.body).append(list);
			list.on('mousedown','.s-list-item',function(e){
				var val=$select.val();
				var newVal=$(this).attr('value');
				$select.val(newVal);
				$select.trigger('blur');
				if(val!=newVal){
					$select.trigger('change');
				}
			});
			list.on('mousedown',function(e){
				e.stopPropagation();
				e.preventDefault();
			});
			var css={
				'minWidth':$select.outerWidth(),
				left:$select.offset().left,
				top:$select.offset().top+$select.outerHeight()-1,
				opacity:1
			};
			if(css.top+list.outerHeight()>$(document).scrollTop()+$(window).height()){
				css.top=$select.offset().top-list.outerHeight()+1;
				list.css(css);
			}else{
				list.css(css).hide().slideDown(100);
			}
		}
	}
	$(function(){
		if(!lib.tools.browser().mobile){
			new Select();
		}
	});

    window.lib = lib;
})();