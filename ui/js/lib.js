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
					if(field.value){
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
			options.url=cfg.getHost()+options.url;
			if(!options.data){
				options.data={};
			}
			if(localStorage.getItem('token')&&options.url.indexOf('/login')==-1){
				options.url+=(options.url.indexOf('?')==-1?"?":"&")+"token="+localStorage.getItem('token');
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
				if(!options.text){
					options.text=(options.bool?"操作成功":"操作失败");
				}
				if(options.time===undefined){
					options.time=2000;
				}
				options.text='<i class="fa fa-'+(options.bool?"check":"times")+'-circle"></i>'+options.text;
				this.tips(options)
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
						data=JSON.stringify(data);
						data=JSON.parse(data.replace(/>/g,'&gt;').replace(/</g,'&lt;'));
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
            this.destroy();
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
				var reg=new RegExp('^[0-9]*[1-9][0-9]*$');
				if(!isNaN(val)){
					if(val==0){
						return {msg:'输入值不能为零'};
					}
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
					success:function(data){
						if(data.result!=1){
							$target.trigger('error',{type:'unique'});
						}else{
							var error=self.getErrorDom($target);
							error.remove();
							if(eventData&&eventData.type=='validate'){
								self.validate(true);
							}
						}
					},
					done:function(){}
				});
				return;
			}
			var error=this.getErrorDom($target);
			error.hide();
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
			if($target.parent('label').length==1){
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
			parent.lib.popup.result({
				bool:true,
				text:(data.msg||"数据更新成功"),
				define:function(){
					history.back();
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
			$(this.el).on('blur',this.selector,function(e,data){
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
			if(!untrigger){
				$(this.el).find(this.selector).trigger('blur',{type:'validate'});
			}
			var $form=$(this.el);
			if($form.attr('disabled'))return;
			var help=$form.find('.control-help:visible');
			if(help.length==0){
				var data=lib.tools.getFormData($form);
				$form.trigger('save',data);
			}else{
				$('html,body').animate({scrollTop:help.eq(0).offset().top-50},200);
				help.eq(0).siblings('input:visible').focus();
			}
		},
		save:function(data){
			var $el=$(this.el);
			var btn=$el.find('button[type=submit]');
			if(btn.is(':disabled')) return;
			btn.attr('disabled',true);
			parent.lib.popup.tips({text:'<img src="/images/oval.svg" class="loader"/>数据正在提交...'});
			var self=this;
			lib.ajax({
				url:$el.attr('action'),
				data:data,
				type:this.el.method,
				success:function(data){
					$(self.el).trigger('response',data).find('button[type=submit]').attr('disabled',false);;
				},
				error:function(xhr,code){
					self.fail(null,{})
				}
			});
		}
	}
	lib.Form=Form;
	$(document).one('mouseenter','form[data-role="form"]',function(){
		new lib.Form(this);
	}).one('touchstart','form[data-role="form"]',function(){
		new lib.Form(this);
	});
	
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
			$(document.body).on('focus',this.selector,function(e){
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