/* 
* @Author: anchen
* @Date:   2015-12-02 19:50:31
* @Last Modified by:   anchen
* @Last Modified time: 2015-12-03 20:44:30
*/

$(function(){

    function init(){
        var promise = lib.ajat("banner/index?type=2#domid=warpper&tempid=warpper-t").render();
        promise.done(function(data){initUploader();});
    }

    $(".box-warpper").on('click','.edit',function(){
        var parent = $(this).closest('.fr');
        parent.find('.save').removeClass('hidden');
        parent.find('.cancel').removeClass('hidden');
        parent.find('.edit').addClass('hidden');

        var topBanner = $(this).closest('.banner');
        topBanner.find('.operation').removeClass('hidden');
        topBanner.find('input').removeAttr('disabled');
    })

    $(".box-warpper").on('click','.cancel',function(){
        var parent = $(this).closest('.fr');
        parent.find('.save').addClass('hidden');
        parent.find('.cancel').addClass('hidden');
        parent.find('.edit').removeClass('hidden');
        var topBanner = $(this).closest('.banner');
        topBanner.find('.operation').addClass('hidden');
        topBanner.find('input').attr('disabled',true);
        topBanner.find('.control-help').hide();
    })
     
    lib.Form.prototype.save = function(data){  
        if(!data.image){    
            $(this.el).find('.control-help').show();
            return;  
        }   
        lib.ajax({
            type: "post",
            url : data.id ? "banner/edit/"+data.id : "banner/create",
            data:data
        }).done(function(data, status, xhr){
            if(data.result == 1){
                parent.lib.popup.result({
                    text:"操作成功！",
                    define:function(){
                        location.reload();
                    }
                });                  
            }
        })                
    }

    function initUploader(){
        lib.puploader.image({
            browse_button: 'uploader2',
            auto_start:true,
            filters: {
            mime_types : [
            { title : "Image files", extensions : "jpg,png,jpeg,gif" },
            ]
            },
            max_file_size:'10mb',
            multi_selection:true,
            files_number:1,
            crop:true
        },function(uploader){
            var item = $("#uploader2").closest(".thumbnails-item");
            uploader.bind('ImageUploaded',function(up,response){
                createThumbnails(up,response,750/500,'750x500',item);
            });

            item.on('click','.fa',function(){                
                var src = item.find('img').attr('src');
                src = src.split('?')[0]+"?imageView2/0/w/720/h/1280";
                uploader.trigger('ImageUploaded',{img:src});
            }) 
        });

        lib.puploader.image({
            browse_button: 'uploader3',
            auto_start:true,
            filters: {
            mime_types : [
            { title : "Image files", extensions : "jpg,png,jpeg,gif" },
            ]
            },
            max_file_size:'10mb',
            multi_selection:true,
            files_number:1,
            crop:true
        },function(uploader){
            var item = $("#uploader3").closest(".thumbnails-item");
            uploader.bind('ImageUploaded',function(up,response){
                createThumbnails(up,response,750/500,'750x500',item);
            });

            item.on('click','.fa',function(){                
                var src = item.find('img').attr('src');
                src = src.split('?')[0]+"?imageView2/0/w/720/h/1280";
                uploader.trigger('ImageUploaded',{img:src});
            }) 
        });

        lib.puploader.image({
            browse_button: 'uploader4',
            auto_start:true,
            filters: {
            mime_types : [
            { title : "Image files", extensions : "jpg,png,jpeg,gif" },
            ]
            },
            max_file_size:'10mb',
            multi_selection:true,
            files_number:1,
            crop:true
        },function(uploader){
            var item = $("#uploader4").closest(".thumbnails-item");
            uploader.bind('ImageUploaded',function(up,response){
                createThumbnails(up,response,750/500,'750x500',item);
            });

            item.on('click','.fa',function(){               
                var src = item.find('img').attr('src');
                src = src.split('?')[0]+"?imageView2/0/w/720/h/1280";
                uploader.trigger('ImageUploaded',{img:src});
            }) 
        });       
    }              

    function createThumbnails(up,response,ratio,name,item){
        console.log('createThumbnails');
        lib.cropper.create({
            src:response.img,
            aspectRatio : ratio,
            thumbnails  : [name],
            define:function(data){
                item.find('.thumbnails-item-btn').css('display','none');
                item.find('.thumbnails-item-img').remove();
                item.removeClass('dashed');
                item.append('<div class="thumbnails-item-img"><img src='+data[name]+'><input type="hidden" name="image" value='+data[name]+' required><div class="operation"><i class="fa fa-pencil-square-o"></i></div><div>'); 
            }
        });        
    }

    init();   
});