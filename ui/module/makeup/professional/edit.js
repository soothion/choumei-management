/* 
* @Author: anchen
* @Date:   2015-10-09 10:53:59
* @Last Modified by:   anchen
* @Last Modified time: 2015-12-02 14:11:22
*/

(function(){       
    if(lib.query.id){
        var promise = lib.ajat("artificer/show/"+lib.query.id+"#domid=form&tempid=form-t").render();
        promise.done(function(data){                
            initUploader();
        });
    }else{
        lib.ajat("#domid=form&tempid=form-t").template({});   
        initUploader();         
    } 

    function initUploader(arr){
        lib.puploader.image({
            browse_button: 'personImagesUpload',
            auto_start:true,
            filters: {
                mime_types : [
                    { title : "Image files", extensions : "jpg,png,jpeg,gif" },
                ]
            },
            max_file_size:'10mb',
            imageArray:arr,
            multi_selection:true,
            files_number:1,
            crop:true
        },function(uploader){
            uploader.bind('ImageUploaded',function(up,response){
                createThumbnails(up,response,1,'160x160');
            });
        });

        lib.puploader.image({
            browse_button: 'personHomeImages',
            auto_start:true,
            filters: {
                mime_types : [
                    { title : "Image files", extensions : "jpg,png,jpeg,gif" },
                ]
            },
            max_file_size:'10mb',
            imageArray:arr,
            multi_selection:true,
            files_number:1,
            crop:true
        },function(uploader){
            uploader.bind('ImageUploaded',function(up,response){
                createThumbnails(up,response,1,'750x500');                       
            });
        });        
    }

    var createThumbnails = function(up,response,ratio,name){
        lib.cropper.create({
            src:response.img,
            aspectRatio : ratio,
            thumbnails  : [name],
            define:function(data){
                if(up.createThumbnails&&!response.edit&&!response._this){
                    up.createThumbnails({
                        thumbimg:data[name],
                        img:response.img
                    });
                }else{
                    up.preview($(response._this),{thumbimg:data[name]});
                }
            }
        });        
    }






 




})();