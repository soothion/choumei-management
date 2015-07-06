(function () {
	seajs.config({
		'map': [
			[ /^(.*\.(?:css|js))(.*)$/i, '$1?'+cfg.version ]
		]
	});
	EJS.ext = '.html?v=' + cfg.version;
    var lib = {
        ajax: function (options) {
			if(options.url.indexOf('merchant')>-1){
				//options.url='http://192.168.12.91:888/index.php/'+options.url;
				options.url=cfg.getHost()+options.url;	
			}else{
				options.url=cfg.getHost()+options.url;	
			}
			if(!options.data){
				options.data={};
			}
			if(localStorage.getItem('token')){
				options.data.token=localStorage.getItem('token');
			}
            return $.ajax(options);
        },
		getSession:function(){
			return localStorage.getItem('session')?JSON.parse(localStorage.getItem('session')):{}
		},
		setSession:function(obj){
			var session=$.extend(this.getSession(),obj);
			localStorage.setItem('session',JSON.stringify(session));
		},
		getDate:function(){
			var date=new Date();
			return date.getFullYear()+"-"+(date.getMonth()+1<10?"0"+(date.getMonth()+1):date.getMonth()+1)+"-"+(date.getDate()+1<10?"0"+(date.getDate()+1):date.getDate()+1);
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
        parseQuery: function (str) {//解析字符串的参数
            var ret = {},reg = /([^?=&]+)=([^&]+)/ig,match;
            while (( match = reg.exec(str)) != null) {
                ret[match[1]] = match[2];
            }
            return ret;
        },
        popup: {//弹出层
            path:'_popup.js',
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
							this.remove();
						});
						options.define && options.define.call();
                    },options.time)
                }else{
					options.define && options.define.call();
				}
                
            },
            close: function () {
                seajs.use(this.path,function(a){
                    a.close();
                });
            }
        },
		browser:function(){
			var ua=navigator.userAgent;
			return {
				moible:/(iphone|ipod|ipad|android|ios|windows phone)/i.test(ua),
				android:/(android)/i.test(ua),
				ios:/(iphone|ipod|ipad)/i.test(ua),
				winphone:/(windows phone)/i.test(ua)
			}
		},
		getFormData:function($form){
			var data={};
			var fields=$form.serializeArray();
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
			return data;
		},
        init:function(){
			lib.query={};
			if(location.search){
				$.extend(lib.query,this.parseQuery(location.search.replace('?','')))
				lib.query._=location.search.replace('?','');
			}
			if(location.hash){
				$.extend(lib.query,this.parseQuery(location.hash.replace('#','')))
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
        query:null,
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
                this.fetch();
            }else{
                this.template({});
            }
        },
        parseProtocol: function () {//解析协议内容
            if (this._protocol) {
                this.renderProtocol();
                var arr = this._protocol.split(/\?|#/);
                if(arr.length==3){
                    this.protocol.url = arr[0];
                    this.protocol.query = lib.parseQuery(arr[1]);
                    this.protocol.custom = lib.parseQuery(arr[2]);
                }else if(arr.length==2){
					if(arr[0].indexOf('/')>-1){
						this.protocol.url=arr[0];
					}else{
						this.protocol.query=lib.parseQuery(arr[0])
					}
                    this.protocol.custom = lib.parseQuery(arr[1]);
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
                success: function (data) {
                    if(!self.exception(data)){
                        self.template(self.parseResponse(data));
                    }
                },
                error:function(xhr,textStatus){
                    self.exception({errorLevel:'xhr',status:xhr.status,readyState:xhr.readyState,textStatus:textStatus});;
                }
            };
            if(pro.custom.cache=='true'){
                options.cache=true;
            }
            lib.ajax(options);
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
            }else if(data.status>=400){
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
            lib.ajat(this.getAttribute('ajat-change')).render();
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
			email:'^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$',
			mobile:'^1[0-9]{10}$',
			phone:'^[0-9]{7,8}$',
			password:'^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,20}$',
			float:function(val){
				var reg=new RegExp('^[+-]?([0-9]*\.?[0-9]+|[0-9]+\.?[0-9]*)([eE][+-]?[0-9]+)?$');
				if(reg.test(val)){
					var arr=val.split('.');
					if(arr[0].length>12){
						return false;
					}else{
						return true;
					}
				}else{
					return false;
				}
			},
			number:'^[0-9]*[1-9][0-9]*$',
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
			if($target.is(':disabled')){
				var error=this.getErrorDom($target);
				error.remove();
				return;
			}
			if($target.is('input[type="checkbox"]')||$target.is('input[type="radio"]')){
				var name=$target.attr('name');
				var inputs=$('input[name="'+name+'"]');
				if(inputs.filter(':checked').length==0&&inputs.last().attr('required')){
					inputs.last().trigger('error',{type:'required'});
					return;
				}
			}
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
					if(!ret(val)){
						$target.trigger('error',{type:'pattern'});
						return;
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
				data[$target.attr('name')]=val;
				lib.ajax({
					url:unique,
					data:data,
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
					}
				});
				return;
			}
			var error=this.getErrorDom($target);
			error.remove();
		},
		required:function(e){
			var $target=$(e.target);
			var error=this.getErrorDom($target);
			var $relative=$target;
			if($target.siblings('.unit').length==1){
				$relative=$target.siblings('.unit');	
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
		pattern:function(e){
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
		unique:function(e,data){
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
		match:function(e){
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
		getErrorDom:function($target){
			var error=$target.siblings('.control-help');
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
		success:function(e,data){
			lib.popup.tips({
				text:'<i class="fa fa-check-circle"></i>'+(data.msg||"数据更新成功"),
				time:2000,
				define:function(){
					history.back();
				}
			});
		},
		fail:function(e,data){
			lib.popup.tips({
				text:'<i class="fa fa-times-circle"></i>'+(data.msg||"数据更新失败"),
				time:2000
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
			}).on('success',function(){
				self.success();
			}).on('fail',function(){
				self.fail();
			}).on('input',this.selector,function(e){
				var $this=$(this);
				if($this.attr('nospace')!==undefined){
					$this.val($this.val().replace(/\s+/g,''));
				}
			});
		},
		validate:function(untrigger){
			if(!untrigger){
				$(this.selector).trigger('blur',{type:'validate'});
			}
			var $form=$(this.el);
			if($form.find('.control-help:visible').length==0){
				var data=lib.getFormData($form);
				$form.trigger('save',data);
			}
		},
		save:function(data){
			var $el=$(this.el);
			var btn=$el.find('button[type=submit]');
			if(btn.is(':disabled')) return;
			btn.attr('disabled',true);
			lib.popup.tips({text:'<img src="/images/oval.svg" class="loader"/>数据正在提交...'});
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
		selector:'.select',
		init:function(){
			this.bindEvent();
		},
		bindEvent:function(){
			var self=this;
			$(document.body).on('focus',this.selector,function(e){
				e.stopPropagation();
				e.preventDefault();
			}).on('blur',this.selector,function(e){
				$('.options').remove();
				$(this).removeClass('select-focus');
			}).on('mousedown',this.selector,function(e){
				$('.select').not($(this)).blur();
				self.instance(this);
				e.stopPropagation();
				e.preventDefault();
			});
			$(document).on('mousedown',function(){
				$('.select-focus').trigger('blur');
			});
		},
		instance:function(select){
			var options=$('<ul class="options"></ul>');
			var $select=$(select).addClass('select-focus');;
			$select.children().each(function(){
				var $this=$(this);
				options.append('<li class="'+($this.is(':checked')?"active":"")+'" value="'+($this.attr('value')||'')+'">'+$this.text()+'</li>')
			});
			$(document.body).append(options);
			options.on('mousedown','li',function(e){
				var val=$select.val();
				var newVal=$(this).attr('value');
				$select.val(newVal);
				$select.trigger('blur');
				if(val!=newVal){
					$select.trigger('change');
				}
			});
			options.on('mousedown',function(e){
				e.stopPropagation();
				e.preventDefault();
			});
			var css={
				'minWidth':$select.outerWidth(),
				left:$select.offset().left,
				top:$select.offset().top+$select.height(),
				opacity:1
			};
			if(css.top+options.outerHeight()>$(document).scrollTop()+$(window).height()){
				css.top=$select.offset().top-options.outerHeight();
			}
			options.css(css);
		}
	}
	$(function(){
		new Select();
	})
	
    window.lib = lib;
})();