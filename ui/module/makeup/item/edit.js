$('#form').on("_ready",function(){
	$('.makeup-item-list').on("click",".remove",function(){
		$(this).closest("tr").remove();
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
			content:lib.ejs.render({url:"cols-t"},{data:data}),
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
					if(tr){
						tr.children("td").eq(0).html(data.title);
						tr.children("td").eq(1).html(data.content);
						tr.closest('.table').trigger('datachange');
					}else{
						$this.closest('.makeup-item-list').find('tbody').append(lib.ejs.render({url:"table-t"},{data:[data]}));
						$this.closest('.makeup-item-list').find("table").trigger('datachange');
					}
					parent.lib.popup.close();
				});
				parent.lib.popup.resize();
			}
		});
	});
	
	$('.makeup-item-image-list').on("click",".remove",function(){
		$(this).closest("tr").remove();
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
			content:lib.ejs.render({url:"cols-image-t"},{data:data}),
			complete:function(){
				var popup=$(this);
				var form=popup.find('form');
				lib.puploader.image({
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
					form.submit();
					parent.lib.popup.resize();
				});
				form.on('save',function(e,data){
					if(tr){
						tr.children("td").eq(0).html(data.title);
						tr.children("td").eq(1).html(data.content);
						var imageArray=[]
						popup.find(".control-image img").each(function(){
							imageArray.push("<img class='image-preview' src='"+this.src+"'/>");
						})
						tr.children("td").eq(2).html(imageArray.join(""));
						tr.closest('.table').trigger('datachange');
					}else{
						$this.closest('.makeup-item-list').find('tbody').append(lib.ejs.render({url:"table-t"},{data:[data]}));
						$this.closest('.makeup-item-list').find("table").trigger('datachange');
					}
					parent.lib.popup.close();
				});
				parent.lib.popup.resize();
			}
		});
	});
	
	$('.makeup-item-image-list table,.makeup-item-list').on("datachange",function(){
		var data=[];
		$(this).find("tbody tr").each(function(){
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
						})
					}else{
						item[name]=td.html();
					}
				}
			});
			item.title && data.push(item);
		});
		if(data.length==0){
			data="";
		}	
		$(this).siblings(".json-hidden").val(JSON.stringify(data));
	});
})