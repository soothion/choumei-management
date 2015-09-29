/* 
* @Author: anchen
* @Date:   2015-09-28 11:17:09
* @Last Modified by:   anchen
* @Last Modified time: 2015-09-29 18:13:17
*/

(function(){
    var type = lib.query.type;
    var currentData = {};

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

        if(currentData.salonLogo && typeof currentData.salonLogo == 'string'){
            currentData.salonLogo = JSON.parse(currentData.salonLogo);
        }

        if(currentData.salonImg && typeof currentData.salonImg == 'string'){
            currentData.salonImg = JSON.parse(currentData.salonImg);
        }

        if(currentData.workImg && typeof currentData.workImg == 'string'){
            currentData.workImg = JSON.parse(currentData.workImg);
        } 
        initUploader();       
    }

    var initUploader = function(){
        logoUpload();
        shopUpload();
        teamUpload();
    }

    var logoUpload = function(){
        lib.puploader.image({
            browse_button: 'logoUpload',
            auto_start:true,
            filters: {
                mime_types : [
                    { title : "Image files", extensions : "jpg,png,jpeg,gif" },
                ]
            },
            max_file_size:'10mb',
            imageArray:currentData.salonLogo,
            multi_selection:true,
            files_number:1,
            crop:true
        },function(uploader){
            uploader.bind('ImageUploaded',function(up,response){
                lib.cropper.create({
                    src:response.img,
                    aspectRatio : 1/1,
                    thumbnails  : ['300x300'],
                    define:function(data){
                        if(response._this){
                            up.preview($(response._this),{thumbimg:data['300x300'],img:data['300x300']});
                            return;
                        }
                        if(up.createThumbnails&&!response.edit){
                            up.createThumbnails({
                                thumbimg:data['300x300'],
                                img:response.img,
                                ratio:1,
                                type : 1
                            },function(){
                                saveImagesUrl();
                            });
                        }else{
                            up.preview(up.area,{thumbimg:data['300x300'],img:data['300x300']});
                        }
                    }
                });
            });
        });
    }

    var shopUpload = function(){
        lib.puploader.image({
            browse_button: 'shopUpload',
            auto_start:true,
            filters: {
                mime_types : [
                    { title : "Image files", extensions : "jpg,png,jpeg,gif" },
                ]
            },
            max_file_size:'10mb',
            imageArray:currentData.salonImg,
            multi_selection:true,
            files_number:4,
            crop:true
        },function(uploader){
            uploader.bind('ImageUploaded',function(up,response){
                lib.cropper.create({
                    src:response.img,
                    aspectRatio : 1125/405,
                    thumbnails  : ['1125x405'],
                    define:function(data){
                        if(response._this){
                            up.preview($(response._this),{thumbimg:data['1125x405'],img:data['1125x405']});
                            return;
                        }
                        if(up.createThumbnails&&!response.edit){
                            up.createThumbnails({
                                thumbimg:data['1125x405'],
                                img:response.img,
                                ratio:1125/405,
                                type : 1
                            },function(){
                                saveImagesUrl();  
                            });
                        }else{
                            up.preview(up.area,{thumbimg:data['1125x405'],img:data['1125x405']});
                        }
                    }
                });
            });
        });        
    }

    var teamUpload = function(){
        lib.puploader.image({
            browse_button: 'teamUpload',
            auto_start:true,
            filters: {
                mime_types : [
                    { title : "Image files", extensions : "jpg,png,jpeg,gif" },
                ]
            },
            max_file_size:'10mb',
            imageArray:currentData.workImg,
            multi_selection:true,
            files_number:1,
            crop:true
        },function(uploader){
            uploader.bind('ImageUploaded',function(up,response){
                lib.cropper.create({
                    src:response.img,
                    aspectRatio : 1125/405,
                    thumbnails  : ['1125x405'],
                    define:function(data){
                        if(response._this){
                            up.preview($(response._this),{thumbimg:data['1125x405'],img:data['1125x405']});
                            return;
                        }
                        if(up.createThumbnails&&!response.edit){
                            up.createThumbnails({
                                thumbimg:data['1125x405'],
                                img:response.img,
                                ratio:1125/405,
                                type : 1
                            },function(){
                                saveImagesUrl();                               
                            });
                        }else{
                            up.preview(up.area,{thumbimg:data['1125x405'],img:data['1125x405']});
                        }
                    }
                });
            });
        });         
    }

    var saveImagesUrl=function(){
        currentData.salonLogo = [];
        $('#control-thumbnails1 .control-thumbnails-item').each(function(){
            var $this=$(this);
            currentData.salonLogo.push({
                thumbimg:$this.find('input[name="thumb"]').val(),
                img:$this.find('input[name="original"]').val(),
                ratio : $this.find('input[name="ratio"]').val()
            });
        });
        currentData.salonImg = [];
        $('#control-thumbnails2 .control-thumbnails-item').each(function(){
            var $this=$(this);
            currentData.salonImg.push({
                thumbimg:$this.find('input[name="thumb"]').val(),
                img:$this.find('input[name="original"]').val(),
                ratio : $this.find('input[name="ratio"]').val()
            });
        });
        currentData.workImg = [];
        $('#control-thumbnails3 .control-thumbnails-item').each(function(){
            var $this=$(this);
            currentData.workImg.push({
                thumbimg:$this.find('input[name="thumb"]').val(),
                img:$this.find('input[name="original"]').val(),
                ratio : $this.find('input[name="ratio"]').val()
            });
        });
        if(type === 'add')  sessionStorage.setItem('add-shop-data',JSON.stringify(currentData));
        if(type === 'edit') sessionStorage.setItem('edit-shop-data',JSON.stringify(currentData));        
    }

    var initEvent = function(){
        $(".flex-item a").on('click',function(e){
            e.preventDefault();
            location.href = $(this).attr('href') + "?type="+type;
        });

        $(".preview").on('click',function(){
            sessionStorage.setItem("preview-shop-data",JSON.stringify(currentData));
            location.href="detail.html?type=preview";
        })

        $(".submit").on('click',function(){
            document.body.onbeforeunload=function(){}
            location.href="bank.html?type="+type;
        });  
    }

    init();
})();