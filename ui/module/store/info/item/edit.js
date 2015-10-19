$(function(){
	$(document.body).on('change','.form-item .control-label input',function(){//是否启用对应的项
		$(this).closest('.control-label').next('.control').find('input,button').attr('disabled',!this.checked);
	}).on('click','.form-item .btn-plus',function(){//增加造型师，药水
		var name=$(this).prev().attr('name');
		$(this).before('<input type="text" class="input-small" maxlength="10" nospace name="'+name+'"/>');
		$(this).prev().focus();
	}).on('click','.price-choose',function(){//定价类型的切换
		if(this.checked&&this.value==1){
			$('.price-group').show().next('.format-group').hide();
		}
		if(this.checked&&this.value==2){
			$('.price-group').hide().next('.format-group').show();
		}
	}).on('change','#type',function(){
		//是否显示快剪等级
		if(this.value==8){
			$('#level').show();
		}else{
			$('#level').hide();
		}
		$('#template').html(lib.ejs.render({text:$('#template-t').html()},{data:templateData['_'+this.value]}));
		$('#template a').on('click',function(){
			$('textarea[name="desc"]').val($(this).data('value'));
		})
	}).on('click','.limit',function(){//有无限制状态的切换
		$(this).parent().siblings('input').attr('disabled',this.checked);
		$(this).parent().siblings('label').children('input').attr('disabled',this.checked);
	}).on('change','#project',function(){//选择项目后渲染出对应的项目详情
		init(this.value);
	}).on('choose','input[name="timingAdded"]',function(){
		var timingShelves=$('input[name="timingShelves"]')
		timingShelves.attr('min',this.value);
		if(timingShelves.val()&&new Date(this.value).getTime()>new Date(timingShelves.val()).getTime()){
			timingShelves.val("");
		}
	});
	var init=function(id){
		id=id||lib.query.id;
		var ajat="";
		if(id){
			ajat="item/show/";
			if(lib.query.p=="warehouse"){
				if(lib.query.type=="item"){
					ajat="warehouse/show/"
				}
				if(lib.query.type=="special"){
					ajat="warehouse/detail/"
				}
			}
			if(lib.query.p=="special"){
				ajat="onsale/show/"
			}
			ajat+=id;
		}
		lib.ajat(ajat+"#domid=form&tempid=form-t").render();
	
		$('#form').one('_ready',function(e,data){
			//图片上传
			lib.puploader.image({
				browse_button: 'imageUpload',
				auto_start:true,
				filters: {
					mime_types : [
						{ title : "Image files", extensions : "jpg,png,jpeg" }
					]
				},
				crop:true
			},function(uploader){
				 uploader.bind('ImageUploaded',function(up,response){
					lib.cropper.create({
						src:response.img,
						thumbnails  : ["300x300"],
						define:function(data){
							up.preview($('.control-single-image'),{img:data['300x300']});
						}
					});                
				});
			});
			//反解析有规格的数据
			if(data.response&&!data.response.price&&data.response.userId!=0&&data.response.prices){
				var parseData={
					sex:[],
					hairstylist:[],
					longhair:[],
					solution:[]
				}
				var normMenu=[];
				var normarr=[];
				var tmpArray=[];
				data.response.prices.forEach(function(item){
					if(item.formats){
						var map={
							"发长":"longhair",
							"性别":"sex",
							"造型师":"hairstylist",
							"药水":"solution"
						}
						item.type={};
						item.formats.forEach(function(item2){
							if(map[item2.formats_name]){
								var tmp=parseData[map[item2.formats_name]];
								if(tmp&&tmp.indexOf&&tmp.indexOf(item2.format_name)==-1){
									parseData[map[item2.formats_name]].push(item2.format_name);
								}
								if(normMenu.indexOf(map[item2.formats_name])==-1){
									normMenu.push(map[item2.formats_name]);
								}
								item.type[map[item2.formats_name]]=item2.format_name;
							}
						});
						item.price=item.price&&parseInt(item.price)>0?parseInt(item.price):"";
						item.priceDis=item.price_dis&&parseInt(item.price_dis)>0?parseInt(item.price_dis):"";
						delete item.price_dis;
						item.priceGroup=item.price_group&&parseInt(item.price_group)>0?parseInt(item.price_group):"";
						delete item.price_group;
						delete item.formats;
						delete item.salon_item_format_id;
					}
					if(tmpArray.indexOf(JSON.stringify(item))==-1){
						normarr.push(item);
						tmpArray.push(JSON.stringify(item));
					}
				});
				//动态还原输入数据
				for(var name in parseData){
					if(parseData[name].length>0){
						var checkbox=$(".item-checkbox[value='"+name+"']");
						checkbox[0].checked=true;
						checkbox.closest('.control-label').next().find('input').attr('disabled',false);
						var btn=checkbox.closest('.control-label').next().children('button').attr('disabled',false);
						btn.siblings('input').remove();
						parseData[name].forEach(function(item){
							if(name=='sex'||name=='longhair'){
								$('input[type="checkbox"][value="'+item+'"]').attr({'checked':true});
							}else{
								btn.before('<input type="text" class="input-small" maxlength="10" nospace name="'+name+'" value="'+item+'">');
							}
						});
					}
				}
				//生成价格表
				$('input[name="normMenu"]').val(JSON.stringify(normMenu));
				$('input[name="normarr"]').val(JSON.stringify(normarr));
				$('#format-table').html(lib.ejs.render({text:$('#format-table-t').html()},{data:normarr,normMenu:normMenu}));
				
			}
			
			//原价与其他折扣价限制
			var price=$('input[name="price"]');
			price.on('pass',function(){
				$('input[name="priceDis"],input[name="priceGroup"]').attr('max',this.value);
			});
			//计算臭美价格
			$('.choumei-discount').on('blur',function(){
				var val=price.val();
				if(val&&!isNaN(val)&&this.value&&!isNaN(this.value)){
					$('input[name="priceDis"]').val(Math.round(parseInt(val)*parseInt(this.value)/100));
				}
			});
			//计算集团价格
			$('.group-discount').on('blur',function(){
				var val=$('input[name="priceDis"]').val();
				if(val&&!isNaN(val)&&this.value&&!isNaN(this.value)){
					$('input[name="priceGroup"]').val(Math.round(parseInt(val)*parseInt(this.value)/100));
				}
			});
			var $format=$('#format-form');
			//生成价格表
			$('#btn-price').on('click',function(){
				var itemCheckbox=$('.item-checkbox:checked');
				if(itemCheckbox.length==0){
					parent.lib.popup.alert({text:'至少要选择一个规格项'})
				}else{
					$format.find('input[type="text"],input[type="checkbox"]').blur();
					if($format.find('.control-help:visible').length==0){
						var sexArr=[];
						$('input[name="sex"]:checked').each(function(){
							sexArr.push({sex:this.value});
						});
						var longhairArr=[];
						$('input[name="longhair"]:checked').each(function(){
							longhairArr.push({longhair:this.value});
						});
						var hairstyArr=[];
						$('input[name="hairstylist"]').each(function(){
							if(this.value){
								hairstyArr.push({hairstylist:this.value});
							}
						});
						var solutionArr=[];
						$('input[name="solution"]').each(function(){
							if(this.value){
								solutionArr.push({solution:this.value});
							}
						});
						var array=[];
						if(sexArr.length>0){
							array.push(sexArr);
						}
						if(longhairArr.length>0){
							array.push(longhairArr);
						}
						if(hairstyArr.length>0){
							array.push(hairstyArr);
						}
						if(solutionArr.length>0){
							array.push(solutionArr);
						}
						var data=[];
						if(array.length>0){
							array[0].forEach(function(item1){
								if(array[1]){
									array[1].forEach(function(item2){
										if(array[2]){
											array[2].forEach(function(item3){
												if(array[3]){
													array[3].forEach(function(item4){
														data.push({type:$.extend({},item1,item2,item3,item4)});
													});
												}else{
													data.push({type:$.extend({},item1,item2,item3)});
												}
											});
										}else{
											data.push({type:$.extend({},item1,item2)});
										}
									});
								}else{
									data.push({type:$.extend({},item1)});
								}
							});
						}
						var normMenu=[];
						itemCheckbox.each(function(){
							normMenu.push(this.value);
						});
						$('input[name="normMenu"]').val(JSON.stringify(normMenu));
						$('#format-table').html(lib.ejs.render({text:$('#format-table-t').html()},{data:data,normMenu:normMenu}));
					}
				}
			});
			//校验性别，发长字段
			$format.on('blur','.input-small',function(e){
				if(this.disabled) return;
				var $this=$(this);
				var arr=[];
				$this.parent().children('input').each(function(){
					if(this.value){
						arr.push(this.value);
					}
				})
				if(arr.length==0){
					$this.siblings('.control-help').show();
				}else{
					$this.siblings('.control-help').hide();
				}
				e.stopPropagation();
			});
			//如果输入臭美，集团价折扣则到输入原价后生成臭美，集团价
			
			$('#format-table').on('focus','.format-price-dis',function(){
				var $this=$(this);
				var choumeiPrecent=$('#choumei-precent').val();
				var price=$this.closest('tr').find('.format-price').val();
				if(!$this.val()&&choumeiPrecent&&price){
					if(choumeiPrecent&&!isNaN(choumeiPrecent)&&!isNaN(price)){
						$this.val(Math.round(choumeiPrecent*price/100));
					}
				}
			}).on('focus','.format-price-group',function(){
				var $this=$(this);
				var groupPrecent=$('#group-precent').val();
				var price=$this.closest('tr').find('.format-price-dis').val();
				if(!$this.val()&&groupPrecent&&price){
					if(groupPrecent&&!isNaN(groupPrecent)&&!isNaN(price)){
						$this.val(Math.round(groupPrecent*price/100));
					}
				}
			}).on('blur','input',function(){
				var normarr=[];
				var tmp=0;
				$('#format-table tbody tr').each(function(){
					var $tr=$(this);
					var obj={type:{}};
					$tr.children().each(function(){
						var $td=$(this);
						var input=$td.children('input');
						if(input.length==0){
							obj.type[$td.data('name')]=$td.text();
						}else{
							var price=input.val();
							obj[$td.data('name')]=(!isNaN(price)?price:"");
						}
					});
					normarr.push(obj);
					if(obj.price&&obj.priceDis){
						tmp++;
					}
				});
				if(tmp>=2){
					$('input[name="normarr"]').val(JSON.stringify(normarr));
				}else{
					$('input[name="normarr"]').val('');
				}
			});
		});
	}
	init();
});