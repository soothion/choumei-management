(function(){
    var type = lib.query.type;
    var currentData = {};
    var conLoader = {} ;
    var contractArr  = []; //图片预览数组
    var uploadConArr = []; //图片真正上传数组
    var readyConArr  = []; //图片已经存在数组（编辑时从服务器返回的图片数组）      

    var init = function(){
        initPage();
        initConUploader();
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
        });

        $("#preview_btn").on('click',function(){          
            //base64数据过大分开存储
            localStorage.setItem("contractPicUrl",JSON.stringify(readyConArr.concat(contractArr)));
            sessionStorage.setItem("preview-shop-data",JSON.stringify(currentData));
            window.open("detail.html?type=preview&upload=true");        
        })

        $(".submit").on('click',function(){
            document.body.onbeforeunload=function(){}
            var len = contractArr.length ;
            if(len > 0){                
                parent.lib.popup.tips({text:'<img src="/images/oval.svg" class="loader"/>图片正在上传中...'});
            } 
            conLoader.upload();
            var timer = setInterval(function(){
                if(conLoader.isInProgress() || licLoader.isInProgress() || corLoader.isInProgress()){
                    return;
                }else{
                    clearInterval(timer);
                    goSettlement();                    
                }
            },50)             
        })
    }

    var goSettlement = function(){
        localStorage.removeItem("contractPicUrl"); 
        if(type === 'add')  sessionStorage.setItem('add-shop-data',JSON.stringify(currentData));
        if(type === 'edit') sessionStorage.setItem('edit-shop-data',JSON.stringify(currentData));
        location.href="settlement.html?type="+type;
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
    }    

    var hideUploaderButton = function(){
        if($("#con_wrapper img").length >= 10){
            $("#con_loader_button").hide();
        }else{
            $("#con_loader_button").show();
        }
        $(window).trigger('resize');       
    }

    init();
})();
