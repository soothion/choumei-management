/* 
* @Author: anchen
* @Date:   2015-10-09 10:53:59
* @Last Modified by:   anchen
* @Last Modified time: 2015-10-10 14:30:38
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

    $("#form").on('keydown','input[pattern=number]',function(e){   
        var key = e.which;
        //alert(key)
        if ((key > 95 && key < 106) || //小键盘上的0到9  
            (key > 47 && key < 58) || //大键盘上的0到9  
            key == 8 || key == 116 || key == 9 || key == 46 || key == 37 || key == 39
            //不影响正常编辑键的使用(116:f5;8:BackSpace;9:Tab;46:Delete;37:Left;39:Right;)  
        ) {
            return true;
        } else {
            return false;
        }
    });
   

    $('#form').on('autoinput','#search',function(e,data){
        if(data){
            $("#salonname").val($(this).val());
        }
    });

    $('#form').on('change','input.start',function(){
        var firstTd   = $(this).closest("tr");  
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
        var workExp = {};
        var educateExp = {};
        for(var i = 0; i < 6 ; i++){
            educateExp['sTime'+i]=data['sTime'+i]; 
            delete data['sTime'+i];
            educateExp['eTime'+i]=data['eTime'+i];
            delete data['eTime'+i];
            educateExp['name'+i]=data['name'+i]; 
            delete data['name'+i];

            workExp['wsTime'+i]=data['wsTime'+i]; 
            delete data['wsTime'+i];
            workExp['weTime'+i]=data['weTime'+i]; 
            delete data['weTime'+i];
            workExp['wname'+i]=data['wname'+i]; 
            delete data['wname'+i];
            workExp['wjob'+i]=data['wjob'+i]; 
            delete data['wjob'+i];
            workExp['waddress'+i]=data['waddress'+i];
            delete data['waddress'+i];
        }
        data['workExp'] = JSON.stringify(workExp);
        data['educateExp'] = JSON.stringify(educateExp);
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
                        //history.back();
                    }
                });                
            }
           
        });
    }
})();