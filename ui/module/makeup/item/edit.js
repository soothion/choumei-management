$('#form').on("_ready",function(){
	$('.makeup-item-list').on("click",".remove",function(){
		var table=$(this).closest("table");
		$(this).closest("tr").remove();
		table.trigger("datachange");
	}).on("click",".edit,.add",function(){
		var $this=$(this);
		var data={};
		var tr=null;
		if($this.hasClass("edit")){
			$this.closest('td').siblings('td').each(function(){
				var $this=$(this);
				data[$this.data('name')]=this.innerHTML;
			});
			tr=$this.closest('tr');
		}
		parent.lib.popup.box({
			confirm:true,
			height:300,
			width:800,
			content:lib.ejs.render({url:"/module/makeup/item/cols-t"},{data:data}),
			complete:function(){
				var popup=$(this);
				var form=popup.find('form');
				new lib.Form(form[0]);
				popup.find('input').focus();
				popup.find(".popup-alert-define").on("click",function(e){
					e.stopPropagation();
					form.submit();
					parent.lib.popup.resize();
				});
				form.on('save',function(e,data){
					var html=lib.ejs.render({url:"/module/makeup/item/table-t"},{data:[data]});
					var table=$this.closest('.makeup-item-list').find("table");
					if(tr){
						tr.replaceWith(html);
					}else{
						$this.closest('.makeup-item-list').find('tbody').append(html);
					}
					table.trigger('datachange');
					parent.lib.popup.close();
				});
				parent.lib.popup.resize();
			}
		});
	});
	
	$('.makeup-item-image-list').on("click",".remove",function(){
		var table=$(this).closest("table");
		$(this).closest("tr").remove();
		table.trigger("datachange");
	}).on("click",".edit,.add",function(){
		var $this=$(this);
		var data={};
		var tr=null;
		var image=[];
		if($this.hasClass("edit")){
			$this.closest('td').siblings('td').each(function(){
				var $this=$(this);
				data[$this.data('name')]=this.innerHTML;
				if($this.data('name')=="image"){
					$this.find("img").each(function(){
						image.push({img:this.src});
					});
				}
			});
			tr=$this.closest('tr');
		}
		parent.lib.popup.box({
			confirm:true,
			height:300,
			width:800,
			content:lib.ejs.render({url:"/module/makeup/item/cols-image-t"},{data:data}),
			complete:function(){
				var popup=$(this);
				var form=popup.find('form');
				parent.lib.puploader.image({
					browse_button: popup.find(".control-image-upload>div").attr('id'),
					auto_start:true,
					filters: {
						mime_types : [
							{ title : "Image files", extensions : "jpg,png,jpeg,gif" },
						]
					},
					max_file_size:'10mb',
					imageArray:image,
					//imageLimitSize:"750*500",
					multi_selection:true,
					files_number:10,
				},function(uploader){
					uploader.bind('ImageUploaded',function(up,response){
						up.createThumbnails({
							img:response.img,
						}); 
					});
					uploader.unbind("UploadComplete");
					uploader.bind("UploadComplete",function(){
						parent.lib.popup.resize();
					});
					parent.lib.popup.resize();
				});
				new lib.Form(form[0]);
				popup.find('input').focus();
				popup.find(".popup-alert-define").on("click",function(e){
					e.stopPropagation();
					var data=lib.tools.getFormData(form);
					if(!data.title&&!data.content&&form.find('img').length==0){
						form.find('.control-help').show();
						parent.lib.popup.resize();
						return;
					}
					form.submit();
					parent.lib.popup.resize();
				});
				form.on('save',function(e,data){
					data.image=[];
					popup.find(".control-image img").each(function(){
						data.image.push({img:this.src});
					});
					var table=$this.closest('.makeup-item-image-list').find("table");
					var html=lib.ejs.render({url:"/module/makeup/item/table-t"},{data:[data]});
					if(tr){
						tr.replaceWith(html);
					}else{
						$this.closest('.makeup-item-image-list').find('tbody').append(html);
					}
					table.trigger('datachange');
					parent.lib.popup.close();
				});
				parent.lib.popup.resize();
			}
		});
	});
	
	$('.makeup-item-image-list table,.makeup-item-list table').on("datachange",function(){
		var data=[];
		var table=$(this);
		var trs=table.find("tbody tr");
		trs.each(function(){
			var tr=$(this);
			var item={};
			tr.children().each(function(){
				var td=$(this);
				var name=td.data('name');
				if(name){
					if(name=="image"){
						var image=[];
						td.find("img").each(function(){
							image.push({img:this.src});
						});
						item[name]=image;
					}else{
						item[name]=td.html().replace(/<br\/>|<br>/g,"\n");
					}
				}
			});
			item.title && data.push(item);
		});
		table.siblings(".json-hidden").val(data.length==0?"":JSON.stringify(data));
		table.siblings("button").attr("disabled",trs.length>=20);
	}).trigger("datachange");
	this._getFormData=function(){
		var data=lib.tools.getFormData($(this));
		data=$.extend(JSON.parse(sessionStorage.getItem("formdata")),data);
		sessionStorage.setItem("formdata",JSON.stringify(data));
		return data;
	}
})