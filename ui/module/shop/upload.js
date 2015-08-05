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
        initConUploader();
        initLicUploader();
        initCorUploader();
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
        renderImage();
    }

    var renderImage = function(){
        var contractPicUrl = currentData.contractPicUrl;
        if(contractPicUrl){
            contractPicUrl = JSON.parse(contractPicUrl);
            readyConArr    = contractPicUrl;
            contractPicUrl.forEach(function(obj,index){           
                $("#con_loader_button").before('<div class="thumbnail" data-type="1"><i index="'+index+'"  class="fa fa-times-circle del"></i><img src="'+obj.thumbimg+'"></div>');               
            })
        }

        var licensePicUrl = currentData.licensePicUrl;
        if(licensePicUrl){
            licensePicUrl = JSON.parse(licensePicUrl);
            readyLicArr   = licensePicUrl;
            licensePicUrl.forEach(function(obj,index){            
                $("#lic_loader_button").before('<div class="thumbnail" data-type="2"><i index="'+index+'"  class="fa fa-times-circle del"></i><img src="'+obj.thumbimg+'"></div>');      
            })
        }

        var corporatePicUrl = currentData.corporatePicUrl;
        if(corporatePicUrl){
            corporatePicUrl = JSON.parse(corporatePicUrl);
            readyCorArr     = corporatePicUrl;
            corporatePicUrl.forEach(function(obj,index){              
                $("#cor_loader_button").before('<div class="thumbnail" data-type="3"><i index="'+index+'"  class="fa fa-times-circle del"></i><img src="'+obj.thumbimg+'"></div>');      
            })                     
        }
        hideUploaderButton()        
    }

    var initConUploader = function(){
        conLoader = initUploader("#con_loader_button",10);
        conLoader.on('fileQueued',function(file){
            conLoader.makeThumb(file, function(error, src) {
                if(error) {return;}
                if($("#con_wrapper img").length >= 10){
                    conLoader.removeFile(file,true);
                    $("#con_loader_button").hide();
                    return;
                }else{
                    var index = $("#con_wrapper img").length;
                    $("#con_loader_button").before('<div class="thumbnail" data-type="1"><i index="'+index+'" id="'+file.id+'" class="fa fa-times-circle del"></i><img src="'+src+'"></div>');           
                    contractArr.push({'img':src,'thumbimg':src});                                      
                }
                hideUploaderButton();
            },1,1);
        })
        conLoader.on('uploadSuccess',function(file,response){
            uploadConArr.push(response.data.main.images[0]);
        })              
        conLoader.on('uploadFinished',function(){
            if(uploadConArr.length > 0){
                currentData['contractPicUrl'] = JSON.stringify(readyConArr.concat(uploadConArr));               
            }else{
                currentData['contractPicUrl'] = JSON.stringify(readyConArr); 
            }
        });  
    }

    var initLicUploader = function(){
        licLoader = initUploader("#lic_loader_button",3);
        licLoader.on('fileQueued',function(file){
            licLoader.makeThumb(file, function(error, src) {
                if (error) return;
                if($("#lic_wrapper img").length >= 3){
                    licLoader.removeFile(file,true);
                    $("#lic_loader_button").hide();
                    return;
                }else{
                    var index = $("#lic_wrapper img").length;
                    $("#lic_loader_button").before('<div class="thumbnail" data-type="2"><i index="'+index+'" id="'+file.id+'" class="fa fa-times-circle del"></i><img src="'+src+'"></div>');           
                    licenseArr.push({'img':src,'thumbimg':src});                      
                }
                hideUploaderButton();
            },1,1);
        })
        licLoader.on('uploadSuccess',function(file,response){
             uploadLicArr.push(response.data.main.images[0]);
        })              
        licLoader.on('uploadFinished',function(){
             if(uploadLicArr.length > 0){
                 currentData['licensePicUrl'] = JSON.stringify(readyLicArr.concat(uploadLicArr));
             }else{
                 currentData['licensePicUrl'] = JSON.stringify(readyLicArr);
             }
        })  
    }

    var initCorUploader = function(){
        corLoader = initUploader("#cor_loader_button",3);
        corLoader.on('fileQueued',function(file){
            corLoader.makeThumb(file, function(error, src) {
                if (error) return;
                if($("#cor_wrapper img").length >= 3){
                    corLoader.removeFile(file,true);
                    $("#lic_loader_button").hide();
                    return;
                }else{
                    var index = $("#cor_wrapper img").length;
                    $("#cor_loader_button").before('<div class="thumbnail" data-type="3"><i index="'+index+'" id="'+file.id+'" class="fa fa-times-circle del"></i><img src="'+src+'"></div>'
                    ); 
                    corporateArr.push({'img':src,'thumbimg':src});                                   
                }
                hideUploaderButton();
            },1,1);


        })
        corLoader.on('uploadSuccess',function(file,response){
             uploadCorArr.push(response.data.main.images[0]);
        })              
        corLoader.on('uploadFinished',function(){
             if(uploadCorArr.length > 0){
                 currentData['corporatePicUrl'] = JSON.stringify(readyCorArr.concat(uploadCorArr));
             }else{
                 currentData['corporatePicUrl'] = JSON.stringify(readyCorArr);
             }
        }) 
    }

    var initUploader = function(button,limit){
        var uploader = WebUploader.create({
            swf   : '../../js/Uploader.swf',
            server: 'http://service.choumei.cn/upload/image',
            prepareNextFile : true,
            fileNumLimit : limit,
            fileSingleSizeLimit : 1024*1024,
            fileSizeLimit : 10*1024*1024 ,
            pick  : {'id': button,'multiple':true},
            accept :{
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/*'                
            },
            thumb:{
                width  : 110,
                height : 110,
                quality: 90,
                allowMagnify: true,
                crop: true,
                type: 'image/jpeg'            
            }                        
        });
        var picker = $(".webuploader-pick:eq(0)").next();
        var timer = setInterval(function(){
            if(picker){
                clearInterval(timer);
                $(window).trigger('resize');
            }
        }, 50)           
        return uploader;
    }

    var initEvent = function(){
        //导航条绑定事件
        $(".flex-item a").on('click',function(e){
            e.preventDefault();
            localStorage.removeItem("contractPicUrl"); 
            sessionStorage.removeItem("licensePicUrl");
            sessionStorage.removeItem("corporatePicUrl");            
            location.href = $(this).attr('href') + "?type="+type;
        });

        //删除图片
        $(".td-wrapper").delegate('.thumbnail i', 'click', function(event) {
            var fileId    = $(this).attr("id");
            var index = $(this).attr("index");
            var dataType = $(this).parent().attr("data-type");
            if(dataType == "1") {
                fileId && conLoader.removeFile(fileId,true);
                fileId && contractArr.splice(index,1);
               !fileId && readyConArr.splice(index,1);
            }
            if(dataType == "2") {
                fileId && fileId && licLoader.removeFile(fileId,true);
                fileId && licenseArr.splice(index,1);
               !fileId && readyLicArr.splice(index,1);                
            }
            if(dataType == "3") {
                fileId && corLoader.removeFile(fileId,true);
                fileId && corporateArr.splice(index,1);
               !fileId && readyCorArr.splice(index,1);                   
            }
            $(this).parent().remove();
            imageSort(dataType);
            hideUploaderButton();            
        });

        $(".td-wrapper").delegate("img","click",function(){
            var dataType = $(this).parent().attr("data-type");
            var index = $(this).prev().attr("index") * 1;
            if(dataType == "1") {
                initswiper(readyConArr.concat(contractArr),index);
            }
            if(dataType == "2"){
                initswiper(readyLicArr.concat(licenseArr),index);
            } 
            if(dataType == "3"){
                initswiper(readyCorArr.concat(corporateArr),index);                
            }   
        });

        $("#preview_btn").on('click',function(){          
            //base64数据过大分开存储
            localStorage.setItem("contractPicUrl",JSON.stringify(readyConArr.concat(contractArr)));
            //base64数据过大分开存储    
            sessionStorage.setItem("licensePicUrl",JSON.stringify(readyLicArr.concat(licenseArr)));
            sessionStorage.setItem("corporatePicUrl",JSON.stringify(readyCorArr.concat(corporateArr)));
            sessionStorage.setItem("preview-shop-data",JSON.stringify(currentData));
            window.open("detail.html?type=preview&upload=true");        
        })

        $(".submit").on('click',function(){
            parent.lib.popup.tips({text:'<img src="/images/oval.svg" class="loader"/>数据正在提交...'});
            conLoader.upload();
            licLoader.upload();
            corLoader.upload();
            var timer = setInterval(function(){
                if(conLoader.isInProgress() || licLoader.isInProgress() || corLoader.isInProgress()){
                    return;
                }else{
                    clearInterval(timer);
                    save();
                }
            },50)             
        })
    }

    var initswiper = function(arr,index){
		var list=[];
        arr && arr.forEach(function(obj,index){
			list.push(obj.img);
        });
        parent.lib.popup.swiper({list:list,index:index});
    }

    var imageSort = function(dataType){
        if(dataType == 1){
            $("#con_wrapper img").each(function(index,obj){
                $(obj).prev().attr('index',index);
            });
        }
        if(dataType == 2){
            $("#lic_wrapper img").each(function(index,obj){
                $(obj).prev().attr('index',index);
            });            
        }
        if(dataType == 3){
            $("#cor_wrapper img").each(function(index,obj){
                $(obj).prev().attr('index',index);
            });            
        }
    }    

    var hideUploaderButton = function(){
        if($("#con_wrapper img").length >= 10){
            $("#con_loader_button").hide();
        }else{
            $("#con_loader_button").show();
        }

        if($("#lic_wrapper img").length >= 3){
            $("#lic_loader_button").hide();
        }else{
            $("#lic_loader_button").show();
        }

        if($("#cor_wrapper img").length >= 3){
            $("#cor_loader_button").hide();
        }else{
            $("#cor_loader_button").show();
        } 
        $(window).trigger('resize');       
    }

    var save = function(){
        lib.ajax({
            type: "post",
            url : (type=="add"?"salon/save":"salon/update"),
            data: currentData    
        }).done(function(data, status, xhr){
			parent.lib.popup.result({
				bool:data.result == 1,
				text:(data.result == 1?"店铺信息提交成功":data.msg),
				time:2000,
				define:function(){
					if(data.result == 1){
						localStorage.removeItem("contractPicUrl"); 
						sessionStorage.removeItem("licensePicUrl");
						sessionStorage.removeItem("corporatePicUrl");
						sessionStorage.removeItem('add-shop-data');
						sessionStorage.removeItem('edit-shop-data');
						document.body.onbeforeunload=function(){}
						if(type === "edit") location.href="/module/shop/detail.html?type=detail&salonid="+currentData.salonid;
						if(type === "add") location.href="/module/merchant/index.html";
					}
				}
			});
           
        });
    }     

    init();
})();
