/* 
* @Author: anchen
* @Date:   2015-10-09 10:53:59
* @Last Modified by:   anchen
* @Last Modified time: 2015-10-09 17:56:45
*/

(function(){       
    if(lib.query.id){
        $("#title").text("编辑造型师");
        var promise = lib.ajat("Stylist/edit/"+lib.query.id+"#domid=form&tempid=form-t").render();
        promise.done(function(data){                
            var arr = [];
            if(data.data && data.data.img) {                    
                arr.push(JSON.parse(data.data.img));
            }else{
                arr.push({'thumbimg':data.data.stylistImg,'img':data.data.stylistImg});
            }
            initUploader(arr);
        });
    }

    if(lib.query.salonid){
        $("#title").text("新增造型师");
        lib.ajat("#domid=form&tempid=form-t").template({});   
        initUploader([]);    
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
                            $("#form .upload-image-tip").hide();
                        }
                    }
                });            
            });
        });
    }

    $("#form").on('click','.tab td',function(){
        $(this).children().css('visibility','visible');
    });

    $("#form").on('click','input[type="checkbox"]',function(){   
        if($(this).val() == "1"){
            lib.popup.alert({text:'此造型师有未完成的赏金单，请完成后再更换店铺!'});
            return false;
        }  
        if($(this).attr("checked")){
            $(this).removeAttr("checked");
            $("#search").attr("disabled",true);     
        }else{  
            $(this).attr("checked",'true');
            $("#search").removeAttr("disabled"); 
        }
    });

    function checkedImage(){
        var len = $("#form .control-thumbnails-item img").length;
        if(len){
            return true;
        }else{
            $("#form .upload-image-tip").show();
            return false;
        }
    }

    lib.Form.prototype.save = function(data){        
       if(!checkedImage()) return;
       var img = {}; 
       var element = $(".control-thumbnails-item img");
       img['thumbimg'] = element.attr('src');
       img['img']      = element.data('original');
       data.stylistImg = element.attr('src');
       data['img'] = JSON.stringify(img);
       data[data.cardType] = data.cardNum;
       delete data.cardType;
       delete data.cardNum;      
       submit(data);
    }

    function submit(data){
        lib.ajax({
            type: "post",
            url : lib.query.id ? "Stylist/update/"+lib.query.id : "Stylist/create/"+lib.query.salonid,
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