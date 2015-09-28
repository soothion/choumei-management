/* 
* @Author: anchen
* @Date:   2015-09-28 11:17:09
* @Last Modified by:   anchen
* @Last Modified time: 2015-09-28 11:37:47
*/

(function(){
    var type = lib.query.type;
    var currentData = {};

    var init = function(){
        initPage();
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

        if(currentData.contractPicUrl&&typeof currentData.contractPicUrl=='string'){
            currentData.contractPicUrl=JSON.parse(currentData.contractPicUrl);
        }

        if(currentData.licensePicUrl&&typeof currentData.licensePicUrl=='string'){
            currentData.licensePicUrl=JSON.parse(currentData.licensePicUrl);
        }

        if(currentData.corporatePicUrl&&typeof currentData.corporatePicUrl=='string'){
            currentData.corporatePicUrl=JSON.parse(currentData.corporatePicUrl);
        } 
        initUploader();       
    }

    var initUploader = function(){
        logoUpload();
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
            imageArray:currentData.contractPicUrl,
            multi_selection:true,
            files_number:10,
            crop:true
        },function(uploader){
            uploader.bind('ImageUploaded',function(up,response){
                lib.cropper.create({
                    src:response.img,
                    define:function(data){
                        if(response._this){
                            up.preview($(response._this),{thumbimg:data['300x300'],img:data['300x300']});
                            return;
                        }
                        if(up.createThumbnails&&!response.edit){
                            up.createThumbnails({thumbimg:data['300x300'],img:data['300x300']});
                        }else{
                            up.preview(up.area,{thumbimg:data['300x300'],img:data['300x300']});
                        }
                    }
                });
            });
        });
    }

    init();
})();