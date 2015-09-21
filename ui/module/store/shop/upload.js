(function(){
    var type = lib.query.type;
    var currentData = {};
    var conLoader = {} , licLoader = {} , corLoader = {};
    var contractArr  = [],licenseArr   = [],corporateArr = []; //图片预览数组
    var uploadConArr = [],uploadLicArr = [],uploadCorArr = []; //图片真正上传数组
    var readyConArr  = [],readyLicArr  = [],readyCorArr  = []; //图片已经存在数组（编辑时从服务器返回的图片数组）      

    var init = function(){
        initPage();
        initEvent();
    }

    var initPage = function(){
        //新增
        if(type === 'add'){
            currentData = JSON.parse(sessionStorage.getItem('add-shop-data')); 
			document.body.onbeforeunload=function(){return "确定离开当前页面吗？";}
        }
        //编辑
        if(type === 'edit'){
            currentData = JSON.parse(sessionStorage.getItem('edit-shop-data')); 
        }
		//合同上传
		lib.puploader.image({
			browse_button: 'imageUpload1',
			auto_start:true,
			filters: {
				mime_types : [
					{ title : "Image files", extensions : "jpg,png,jpeg,gif" },
				]
			},
			max_file_size:'10mb',
			imageArray:currentData.contractPicUrl
		});
		//营业执照上传
		lib.puploader.image({
			browse_button: 'imageUpload2',
			auto_start:true,
			filters: {
				mime_types : [
					{ title : "Image files", extensions : "jpg,png,jpeg,gif" },
				]
			},
			max_file_size:'10mb',
			imageArray:currentData.licensePicUrl
		});
		//法人执照上传
		lib.puploader.image({
			browse_button: 'imageUpload3',
			auto_start:true,
			filters: {
				mime_types : [
					{ title : "Image files", extensions : "jpg,png,jpeg,gif" },
				]
			},
			max_file_size:'10mb',
			imageArray:currentData.corporatePicUrl
		});
    }

    var initEvent = function(){
        //导航条绑定事件
        $(".flex-item a").on('click',function(e){
            e.preventDefault();           
            location.href = $(this).attr('href') + "?type="+type;
        });

		var setURLData=function(){
			currentData.contractPicUrl=[];
			$('#control-thumbnails1 .control-thumbnails-item').each(function(){
				var $this=$(this);
				currentData.contractPicUrl.push({
					thumbimg:$this.find('input[name="thumb"]').val(),
					img:$this.find('input[name="original"]').val()
				});
			});
			currentData.licensePicUrl=[];
			$('#control-thumbnails2 .control-thumbnails-item').each(function(){
				var $this=$(this);
				currentData.licensePicUrl.push({
					thumbimg:$this.find('input[name="thumb"]').val(),
					img:$this.find('input[name="original"]').val()
				});
			});
			currentData.corporatePicUrl=[];
			$('#control-thumbnails3 .control-thumbnails-item').each(function(){
				var $this=$(this);
				currentData.corporatePicUrl.push({
					thumbimg:$this.find('input[name="thumb"]').val(),
					img:$this.find('input[name="original"]').val()
				});
			});
		}
        $("#preview_btn").on('click',function(){
			setURLData();
            sessionStorage.setItem("preview-shop-data",JSON.stringify(currentData));
            window.open("detail.html?type=preview&upload=true");        
        })

        $(".submit").on('click',function(){
            document.body.onbeforeunload=function(){}
			setURLData();
			if(type === 'add')  sessionStorage.setItem('add-shop-data',JSON.stringify(currentData));
			if(type === 'edit') sessionStorage.setItem('edit-shop-data',JSON.stringify(currentData));
			location.href="settlement.html?type="+type;
        })
    }


    init();
})();
