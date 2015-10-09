/* 
* @Author: anchen
* @Date:   2015-10-09 10:53:59
* @Last Modified by:   anchen
* @Last Modified time: 2015-10-09 15:29:48
*/

(function(){       
    if(lib.query.id){
        var promise = lib.ajat("Stylist/edit/"+lib.query.id+"#domid=form&tempid=form-t").render();
        promise.done(function(data){                
            var arr = [];
            if(data.data && data.data.img) {                    
                arr.push(JSON.parse(data.data.img));
            }
            initUploader(arr);
        });
    }

    if(lib.query.salonid){
        lib.ajat("#domid=form&tempid=form-t");   
        //initUploader([]);    
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
                lib.cropper.create({
                    src:response.img,
                    aspectRatio : 1,
                    thumbnails  : ["420x492"],
                    define:function(data){
                        if(up.createThumbnails&&!response.edit){
                            up.createThumbnails({
                                thumbimg:data["420x492"],
                                img:response.img,
                                ratio: 1,
                                type : 1
                            });
                        }
                    }
                });            
            });
        });
    }

    $("#form").on('click','.tab td',function(){
        $(this).children().css('visibility','visible');
    });

    $("#form").on('change','input[type="checkbox"]',function(){
        if($(this).attr("checked")){
             $(this).removeAttr("checked"); 
             $(this).removeAttr("name");    
        }else{
            $(this).attr("checked",'true');
            $(this).attr("name",'checkbox');     
        }
    });

    lib.Form.prototype.save = function(data){
       var img = {}; 
       var element = $(".control-thumbnails-item img");
       img['thumbimg'] = element.attr('src');
       img['img']      = element.data('original');
       data.stylistImg = element.attr('src');
       data['img'] = JSON.stringify(img);
       data[data.cardType] = data.cardNum;
       delete data.cardType;
       delete data.cardNum;      
       if(lib.query.id) data['id'] = lib.query.id;
       submit(data);
    }

    function submit(data){
        lib.ajax({
            type: "post",
            url : lib.query.id ? "Stylist/update/"+lib.query.id : "salon/update",
            data: data    
        }).done(function(data, status, xhr){
            if(data.result == 1){
                parent.lib.popup.result({
                    text:"操作成功！",
                    define:function(){
                        history.back();
                    }
                });                
            }
           
        });
    }
})();