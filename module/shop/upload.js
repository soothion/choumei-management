(function(){
    var type = utils.getSearchString("type");
    var currentData = {} , uploadData = {};
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
        if(type && type === 'add'){
            currentData = JSON.parse(sessionStorage.getItem('add-shop-data')); 
        }
        //编辑
        if(type && type === 'edit'){
            currentData = JSON.parse(sessionStorage.getItem('edit-shop-data')); 
        }
    }

    var initEvent = function(){
        $(".flex-item a").on('click',function(e){
            e.preventDefault();
            location.href = $(this).attr('href') + "?type="+type;
        });        
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
                    // var index = $("#contractBox img").length;
                    // $("#contractBox").append('<div class="thumbnail" data-type="1"><div index="'+index+'" id="'+file.id+'" class="remove-img upload"></div><img width="110px" height="110px" src="'+src+'"/></div>');             
                    // contractArr.push({'img':src,'thumbimg':src});                      
                }
                //hideUploaderButton();
            },1,1);
        })
        conLoader.on('uploadSuccess',function(file,response){
            uploadConArr.push(response.data.main.images[0]);
        })              
        conLoader.on('uploadFinished',function(){
            if(uploadConArr.length > 0){
                uploadData['contractPicUrl'] = JSON.stringify(readyConArr.concat(uploadConArr));               
            }else{
                uploadData['contractPicUrl'] = JSON.stringify(readyConArr); 
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
                    // var index = $("#licenseBox img").length;
                    // $("#licenseBox").append('<div class="thumbnail" data-type="2"><div index="'+index+'" id="'+file.id+'" class="remove-img upload"></div><img width="110px" height="110px" src="'+src+'"/></div>');             
                    // licenseArr.push({'img':src,'thumbimg':src});                      
                }
                //hideUploaderButton();
            },1,1);
        })
        licLoader.on('uploadSuccess',function(file,response){
             uploadLicArr.push(response.data.main.images[0]);
        })              
        licLoader.on('uploadFinished',function(){
             if(uploadLicArr.length > 0){
                 uploadData['licensePicUrl'] = JSON.stringify(readyLicArr.concat(uploadLicArr));
             }else{
                 uploadData['licensePicUrl'] = JSON.stringify(readyLicArr);
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
                    // var index = $("#corporateBox img").length;
                    // $("#corporateBox").append('<div class="thumbnail" data-type="3"><div index="'+index+'" id="'+file.id+'" class="remove-img upload"></div><img width="110px" height="110px" src="'+src+'"/></div>');
                    // corporateArr.push({'img':src,'thumbimg':src});                                   
                }
                //hideUploaderButton();
            },1,1);


        })
        corLoader.on('uploadSuccess',function(file,response){
             uploadCorArr.push(response.data.main.images[0]);
        })              
        corLoader.on('uploadFinished',function(){
             if(uploadCorArr.length > 0){
                 uploadData['corporatePicUrl'] = JSON.stringify(readyCorArr.concat(uploadCorArr));
             }else{
                 uploadData['corporatePicUrl'] = JSON.stringify(readyCorArr);
             }
        }) 
    }

    var initUploader = function(button,limit){
        var uploader = WebUploader.create({
            swf   : '../../js/Uploader.swf',
            server: 'http://service.choumei.cn/FileUploadService/imgUpload',
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

    init();
})();
